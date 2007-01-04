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
 * The main class for syncing PartyCal.
 */
class Sync_PartyCal extends PartyCal {

	public $helpString = 'tool for syncinc db from and with remote providers and subscribers';

	/**
	 * Full synchronization, for calling with cron.
	 */
	public function actioncycle() {

		$this->loadNewData();
		$this->syncWithWebAccounts();
		$this->swipeOldRecords();
	}

	/**
	 * Just load the data, ignores duplicates from the same provider.
	 */
	public function actionload() {

		$this->loadNewData();
	}

	/**
	 * upload data to providers, makes sure no one get the same entry twice.
	 */
	public function actionsync() {

		$this->syncWithWebAccounts();
	}

	/**
	 * swipe old records in the db (mainly past events)
	 */
	public function actionswipe() {
		
		$this->swipeOldRecords();
	}

	/**
	 * load data from providers in conf
	 *
	 * loads provider data using the class specified in conf.
	 * the data gets inserted into the db, existing db constraints ensure that no duplicates 
	 * get added, this implys some care taking when coding new providers.
	 *
	 * @todo the db schema here is not final, it needs serious refining for supporting more than 2 providers
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
	 * \todo move lots of this into array classes
	 */
	public function syncWithWebAccounts() {
		$i = $this->subscribers->getIterator();
		$s = new Subscriber_Sync_PartyCal( $this->pdo );

		while($i->valid()) {
			$s->addNewRecords( $i->current() );
			$s->updateRecords( $i->current() );

			$i->next();
		}
		return;

		$this->pdo->beginTransaction();
		if ($stmt->execute()) {
			$sucessful_events = array();

			while ($row = $stmt->fetch()) {


				$i = $this->subscribers->getIterator();

				while($i->valid()) {

					$conf = new Config_PartyCal( 'subscriber-'.$i->key() );
					$subscriberservice = new $conf->classname(  
						'subscriber-'.$i->key(), 
						$i->current() );
					$subscriberservice->createOrUpdate( $row );

					$i->next();
				}

				$sucessful_events[] .= $row['event_id'];
			}
		}


		$i = $this->subscribers->getIterator();
		$finish_stmt = $this->pdo->prepare('
			INSERT INTO event_subscriber (
				event_id,
				subscriber_name
			) VALUES (
				?,
				?
			)
		');

		while($i->valid()) {
			foreach ($sucessful_events AS $event) {
				$finish_stmt->execute( array( $event, $i->key() ) );
			}
			$i->next();
		}
		$this->pdo->commit();
	}

	/**
	 * remove old records from the db
	 *
	 * \todo implement carcol functions, old stuff (past events) get kicked, what to do with venue data is to be decided, lookup facilities would be kewl
	 */
	public function swipeOldRecords() {
	}

}

?>
