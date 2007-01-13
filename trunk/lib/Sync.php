<?php
/**
 * PartyCal based Synchronization Suite.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 */

/**
 *
 */
require_once 'PartyCal.php';

/**
 *
 */
require_once 'Provider/Sync.php';

/**
 *
 */
require_once 'Subscriber/Sync.php';

/**
 * Logging.
 */
require_once 'Log.php';

/**
 * The main class for syncing PartyCal.
 */
class Sync_PartyCal extends PartyCal {

	public $helpString = 'tool for syncinc db from and with remote providers and subscribers';

	/**
	 * Full synchronization, for calling with cron.
	 */
	public function actionFullCycle() {

		$this->loadNewData();
		$this->syncWithWebAccounts();
		$this->swipeOldRecords();
	}

	/**
	 * Just load the data, ignores duplicates from the same provider.
	 */
	public function actionLoad() {

		$this->loadNewData();
	}

	/**
	 * upload data to providers, makes sure no one get the same entry twice.
	 */
	public function actionSync() {

		$this->syncWithWebAccounts();
	}

	/**
	 * swipe old records in the db (mainly past events)
	 */
	public function actionSwipe() {
		
		$this->swipeOldRecords();
	}

	/**
	 * load data from providers in conf
	 *
	 * loads provider data using the class specified in conf.
	 * the data gets inserted into the db, existing db constraints ensure that no duplicates 
	 * get added, this implys some care taking when coding new providers.
	 */
	public function loadNewData() {
		
		$i = $this->providers->getIterator();
		$s = new Provider_Sync_PartyCal( $this->pdo );

		while($i->valid()) {
			$s->load( $i->current() );

			$i->next();
		}
	}

	/**
	 * main sync action
	 *
	 * what has already been synced, gets stored in the db so we dont need any remote lookup facility, this is mostly hit and run
	 * 
	 * @return void
	 */
	public function syncWithWebAccounts( ) {

		$i = $this->subscribers->getIterator();
		$s = new Subscriber_Sync_PartyCal( $this->pdo );

		while($i->valid()) {
			$s->addNewRecords( $i->current() );
			$s->updateRecords( $i->current() );

			$i->next();
		}
		return;
	}

	/**
	 * remove old records from the db
	 *
	 * @todo implement garcol functions, old stuff (past events) get kicked, this needs delete triggers on db
	 */
	public function swipeOldRecords() {
	}

}

?>
