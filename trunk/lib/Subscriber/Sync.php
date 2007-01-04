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

/**
 *
 */

/**
 *
 */
class Subscriber_Sync_PartyCal { 

	public function __construct( $pdo ) {
		
		$this->pdo = $pdo;
	}


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

			$this->addNewRecord( $row , $subscriber->name );
		}

	}

	public function addNewRecord( $data , $subscriber_name ) {

		static $check_event_for_provider_stmt;
		if ( empty( $check_event_for_provider_stmt ) ) {
			$check_event_for_provider_stmt = $this->pdo->prepare('
				SELECT * FROM event_subscriber
				WHERE event_id = :event_id
				AND subscriber_name = :subscriber_name
			');
		}

		$sel = array();
		$sel['subscriber_name'] = $subscriber_name;
		$sel['event_id'] = $data['event_id'];
		$check_event_for_provider_stmt->execute( $sel );

		if ( ! $check_event_for_provider_stmt->fetch() ) {

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

				$c = new Config_Partycal( 'subscriber-' . $subscriber_name );
				$classname = $c->classname;

				require_once $c->subscribe_filename;
				if ( is_callable ( array ( $c->subscribe_classname , 'singleton' ) ) ) {
					$o = call_user_func( array ( $c->subscribe_classname , 'singleton' ) , $c );
				} else {
					$o = new $c->subscribe_classname( $c );
				}

				return $o->addNewRecord( $data );
			}
		}
	}

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
