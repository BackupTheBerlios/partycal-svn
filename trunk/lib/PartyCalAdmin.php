<?php
/**
 * PartyCal based Controller for admin stuff
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

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

	public function actionaddsubscriber($mode)
	{
		return;
		// this snippet should move on to ./partycal-admin addsubscriber gcal
		$myCalendars = $this->cal->getCalendarListFeed();
		foreach ($myCalendars as $entry) {
			var_dump($entry->title());
			var_dump($entry->id());
		}
	
	}

}

?>
