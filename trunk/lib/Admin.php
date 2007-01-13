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
class Admin_PartyCal extends PartyCal {

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
				desc_text BLOB,
				desc_text_nolinks BLOB,
				desc_html BLOB,
				desc_wiki BLOB,
				cost_text TEXT,
				free INTEGER,
				venue_name TEXT,
				venue_link TEXT,
				city_name TEXT,
				city_postal TEXT,
				location TEXT,
				link TEXT UNIQUE,
				style_tags TEXT
			)
		');

		$this->pdo->query('
			CREATE TABLE IF NOT EXISTS event_subscriber (
				event_subscriber_id INTEGER PRIMARY KEY,
				event_id INTEGER,
				subscriber_name TEXT
			)
		');
		$this->pdo->query('
			CREATE TABLE IF NOT EXISTS event_raw (
				event_raw_id INTEGER PRIMARY KEY,
				event_id INTEGER,
				raw_data TEXT,
				last_update TEXT
			)
		');
	}

	public function actionUpdate() {

		// copy needed alpha data away (backup tables)
		// drop old tables
		// $this->actioninstallschema();
		// restore data
		// drop backup tables
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
