<?php
/**
 * .
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
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
class Provider_Sync_PartyCal { 

	public function __construct( $pdo ) {
		
		$this->pdo = $pdo;

	}

	public function load( $provider )
	{
		$conf = $provider->config;
		$classname = $conf->classname;

		require_once $classname . '.php';
		$feedreader = new $classname;
		$feed = $feedreader->import( $provider->feed );

		foreach ( $feed as $item ) {
			
			if ( $event_id = $this->recordExists( $feedreader->getLink( $item ) ) ) {

				$data = $feedreader->getUpdateData( $item );
				$data['event_id'] = $event_id;
				$this->updateRecord( $data );

			} else {
				$this->insertNewRecord( $feedreader->getInsertData( $item ) );
			}

		}
	}

	/**
	 * check if record is in db.
	 */
	public function recordExists( $link ) {

		static $count_events_by_link_stmt;
		if ( empty( $count_events_by_link_stmt ) ) {
			$count_events_by_link_stmt = $this->pdo->prepare('
				SELECT event_id
				FROM event
				WHERE link = :link
			');
		}

		$count = array();
		$count['link'] = $link;
		$count_events_by_link_stmt->execute( $count );
		
		if ( $row = $count_events_by_link_stmt->fetch() ) {
			return $row['event_id'];
		}

		return false;
	}

	/**
	 * flag in db if update is needed.
	 *
	 * @todo rollback recoverables without exception
	 */
	public function updateRecord( $data ) {
		if ( !$this->checkForUpdate( $data ) ) {
			return;
		}
		try {
			$this->insertRawEvent( $data );
			$flags = $this->compareLastTwoEvents( $data );
			$this->storeUpdateFlags( $data , $flags );

		} catch ( PDOException $e ) {
			throw $e;
		}
	}

	public function checkForUpdate( $data ) {

		static $select_last_raw_data_by_event_stmt;
		if ( empty( $select_last_raw_data_by_event_stmt ) ) {
			$select_last_raw_data_by_event_stmt = $this->pdo->prepare('
				SELECT
					raw_data
				FROM event_raw
				WHERE event_id = :event_id
				AND  last_update IN ( 
					SELECT MAX( last_update ) 
					FROM event_raw
					WHERE event_id = :event_id
				)
			');
		}

		$sel = array();
		$sel['event_id'] = $data['event_id'];
		$select_last_raw_data_by_event_stmt->execute( $sel );

		if ( $row = $select_last_raw_data_by_event_stmt->fetch() ) {
			if ( $row['raw_data'] === $data['raw_data'] ) {
				return false;
			}
			return true;
		}
	}

	public function compareLastTwoEvents( $data )
	{
		static $select_last_two_raw_events_stmt;

		$f = array();
		return $f;
	}

	public function storeUpdateFlags( $data , $flags )
	{
		static $insert_update_flags_stmt;
	}

	/**
	 * insert a new record.
	 *
	 * @todo rollback recoverables without exception, clean up leftovers and 
	 * 
	 * @throws PDOException
	 */
	public function insertNewRecord( $data ) {

		try {
			$this->insertEvent( $data );
			$this->insertRawEvent( $data );

		} catch ( PDOException $e ) {
			// 23000 = duplicate record (ignore)
			if ( $e->getCode() != 23000 ) {
				throw $e;
			}
		}
	}

	public function insertEvent( &$data ) {

		static $insert_event_stmt;
		if ( empty( $insert_event_stmt ) ) {
			$insert_event_stmt = $this->pdo->prepare('
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
				);
			');
		}

		$ins = array();
		$ins['start_ts']   = $data['start_ts'];
		$ins['end_ts']     = $data['end_ts'];
		$ins['event_name'] = $data['event_name'];
		$ins['shortdesc']  = $data['shortdesc'];
		$ins['longdesc']   = $data['longdesc'];
		$ins['location']   = $data['location'];
		$ins['link']       = $data['link'];

		$insert_event_stmt->execute( $ins );

		$data['event_id'] = $this->pdo->lastInsertId();
	}

	public function insertRawEvent( $data )
	{
		static $insert_raw_storage_stmt;
		if ( empty( $insert_raw_storage_stmt ) ) {
			$insert_raw_storage_stmt = $this->pdo->prepare('
				INSERT INTO event_raw (
					event_id,
					raw_data,
					last_update
				) VALUES (
					:event_id,
					:raw_data,
					( select datetime(\'now\',\'localtime\') )
				)
			');
		}

		$raw = array();
		$raw['event_id'] = $data['event_id'];
		$raw['raw_data'] = $data['raw_data'];

		$insert_raw_storage_stmt->execute( $raw );
	}
}

?>
