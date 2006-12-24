<?php
/**
 * PartyCal based Synchronization Suite.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 * The main class for syncing PartyCal.
 */
class PartyCalSync extends PartyCal {

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

		while($i->valid()) {
			$conf = new Config_PartyCal( 'provider-'.$i->key() );
			$classname = $conf->classname;
			$feedreader = new $classname;
			$feed = $feedreader->import($i->current());

			$event_stmt = $this->pdo->prepare('
				INSERT INTO event (
					start_ts,
					end_ts,
					event_name,
					shortdesc,
					longdesc,
					location,
					link
				) VALUES (
					:start_ts,
					:end_ts,
					:event_name,
					:shortdesc,
					:longdesc,
					:location,
					:link
				)
			');

			foreach ($feed as $item) {
				try {
					$event_stmt->execute($feedreader->getInsertData($item));
				} catch ( PDOException $e ) {
					// 23000 = duplicate record (ignore)
					if (!$e->getCode() == 23000) {
						throw new PDOExecption($e->getMessage(), $e->getCode());
					}
				}
			}

			$i->next();
		}
	}

	/**
	 * main sync action
	 *
	 * what has already been synced, gets stored in the db so we dont need any remote lookup facility, this is mostly hit and run
	 * 
	 * \todo move lots of this into array classes
	 * \todo rewire this to make sure subscribers can be added later and get old events (is this wanted?)
	 */
	public function syncWithWebAccounts() {

		$this->pdo->beginTransaction();
		$stmt = $this->pdo->prepare('
			SELECT * 
			FROM event 
			JOIN event_subscriber 
			ON (event.event_id = event_subscriber.event_id)
			WHERE event_subscriber.subscriber_name IS NULL
		');

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
