<?php
/**
 * The main class for administrating PartyCal.
 *
 * Released under the GNU GPL
 * Lucas S. Bickel 2006
 */
class PartyCalAdmin extends PartyCal {

	/**
	 * install tables into a newly created schema
	 */
	public function actioninstallschema()
	{
		$this->pdo->query('
			CREATE TABLE IF NOT EXISTS event (
				event_id INTEGER PRIMARY KEY,
				start_ts TEXT,
				end_ts TEXT,
				event_name TEXT,
				shortdesc BLOB,
				longdesc BLOB,
				location TEXT,
				link TEXT UNIQUE
			)
		');

		$this->pdo->query('
			CREATE TABLE IF NOT EXISTS event_subscriber (
				event_subscriber_id INTEGER PRIMARY KEY,
				event_id INTEGER,
				subscriber_name TEXT
			)
		');
	}

}

?>
