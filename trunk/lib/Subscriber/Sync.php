<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Subscriber
 */

/**
 *
 */
require_once 'Log/Subscriber.php';

/**
 *
 */
class Subscriber_Sync_PartyCal { 

	/**
	 * 
	 * - setup logging
	 *
	 * @param PDO $pdo SQLite Connection.
	 */
	public function __construct( $pdo ) {
		
		$this->pdo = $pdo;
		$this->log = new Log_Subscriber_PartyCal();
	}

	/**
	 * entry point for adding new records during the sync action.
	 *
	 * this gets called once per subscriber.
	 *
	 * @param Subscriber_PartyCal $subscriber wich subscriber to add records to
	 */
	public function addNewRecords( Subscriber_PartyCal $subscriber ) {

		static $select_events_stmt;
		if ( empty( $select_events_stmt ) ) {
			$select_events_stmt = $this->pdo->prepare('
				SELECT *
				FROM event 
			');
		}


		$select_events_stmt->execute( );

		while ( $row = $select_events_stmt->fetch() ) {

			if ( $this->addNewRecord( $row , $subscriber ) ) {
				$this->log->posted( $subscriber->name , $row['event_id'] );
				$this->markAsAdded( $row['event_id'] , $subscriber );
			}
		}

	}

	/**
	 * load data for posting and delegate it to the subscriber implementation.
	 *
	 * @param PDO_Row $data from a simple select * in event
	 * @param Subscriber_PartyCal $subscriber for delegating the action to
	 */
	public function addNewRecord( $data , Subscriber_PartyCal $subscriber ) {

		if ( $this->providerNeedsEvent( $data['event_id'] , $subscriber ) ) {

			static $select_event_data_stmt;
			if ( empty($select_event_data_stmt) ) {
				$select_event_data_stmt = $this->pdo->prepare('
					SELECT * FROM event
					WHERE event_id = :event_id
				');
			}

			$sel = array();
			$sel['event_id'] = $data['event_id'];
			$select_event_data_stmt->execute( $sel );

			if ($data = $select_event_data_stmt->fetch() ) {

				return $subscriber->delegate( 'insertNewRecord' , $data );
			}
		}
	}

	/**
	 * Check if an event needs adding to a subscriber.
	 *
	 * @param $event_id
	 * @param Subscriber_PartyCal $subscriber
	 * @return boolean
	 *
	 * @todo consider delegating to subscriber so being able to check for existing records online.
	 */
	public function providerNeedsEvent( $event_id , Subscriber_PartyCal $subscriber ) {

		static $check_event_for_provider_stmt;
		if ( empty( $check_event_for_provider_stmt ) ) {
			$check_event_for_provider_stmt = $this->pdo->prepare('
				SELECT * FROM event_subscriber
				WHERE event_id = :event_id
				AND subscriber_name = :subscriber_name
			');
		}

		$sel = array();
		$sel['subscriber_name'] = $subscriber->name;
		$sel['event_id'] = $event_id;
		$check_event_for_provider_stmt->execute( $sel );

		if ( $check_event_for_provider_stmt->fetch() ) {
			return false;
		} else {
			return true;
		}
	}

	public function markAsAdded( $event_id , Subscriber_PartyCal $subscriber ) {

		static $insert_event_subscriber_stmt;
		if ( empty( $insert_event_subscriber_stmt ) ) {
			$insert_event_subscriber_stmt = $this->pdo->prepare('
				INSERT INTO event_subscriber (
					event_id,
					subscriber_name
				) VALUES (
					:event_id,
					:subscriber_name
				)
			');
		}

		$ins = array();
		$ins['event_id'] = $event_id;
		$ins['subscriber_name'] = $subscriber->name;

		$insert_event_subscriber_stmt->execute( $ins );
	}

	/**
	 * entry point for updating existing records during the sync action.
	 *
	 * @param Subscriber_PartyCal $subscriber
	 */
	public function updateRecords( Subscriber_PartyCal $subscriber ) {
		return;
		static $select_update_flags_by_provider_stmt;
		if ( empty( $select_update_flags_by_provider_stmt ) ) {
			$select_update_flags_by_provider_stmt = $this->pdo->prepare('
				SELECT UNIQUE  
					event.event_id, event.link
				FROM event
				JOIN event_update
				ON ( event.event_id = event_update.event_id )
				LEFT JOIN event_update_subscriber
				ON ( event_update.event_update_flag_id = event_update_subscriber.event_update_id )
				WHERE event_update_subscriber.subscriber_name != :subscriber_name
				OR event_update_subscriber.subscriber_name IS NULL
			');
		}
	}

}

?>
