<?php
/**
 * PartyCal based Controller for admin stuff
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Core
 * @file
 */

/**
 *
 */
require_once 'PartyCal.php';

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
		$this->updateAlpha1();
	}

	/**
	 * Alpha 0.0.1 -> Alpha 0.0.2
	 */
	public function updateAlpha1() {

		$file = $_ENV['PARTYCAL_ROOT'] . '/' . substr($this->config->db_dso, 7);
		copy($file, $file . '.bak');

		// copy needed alpha data away (backup tables)
		$this->pdo->query('
			CREATE TABLE IF NOT EXISTS event_backup (
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
			INSERT INTO event_backup SELECT * FROM event;
		');

		// drop old tables
		$this->pdo->query('DROP TABLE event');
		$this->pdo->query('DROP TABLE event_subscriber');

		$this->actioninstallschema();

		// restore data
		$this->pdo->query('
			INSERT INTO event (event_id, start_ts, event_name, desc_text, link) 
			SELECT event_id, start_ts, event_name, shortdesc, link  FROM event_backup
		');
		$this->pdo->query('
			INSERT INTO event_subscriber (event_id, subscriber_name)
			SELECT event_id , \'partilender\' FROM event_backup
		');
		$this->pdo->query('
			INSERT INTO event_subscriber (event_id, subscriber_name)
			SELECT event_id , \'eventful\' FROM event_backup
		');

		//swipe some old records...
		$this->pdo->query("
			DELETE FROM event_subscriber
			WHERE event_id IN (
				SELECT event_id FROM event WHERE strftime('%s', start_ts) < strftime('%s', 'now')
			)
		");
		$this->pdo->query("
			DELETE FROM event
			WHERE strftime('%s', start_ts) < strftime('%s', 'now')
		");

		// drop backup tables
		$this->pdo->query('DROP TABLE event_backup');
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
