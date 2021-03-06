<?php
/**
 * Basic Controller for CLI Interaction.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package Core
 * @subpackage Core
 * @file
 */

/**
 * Abstract Controller.
 */
require_once 'Core.php';

/**
 * The main class for CLI based usage of PartyCal.
 * 
 * @todo implement actions for reading and setting ini file settings
 */
class PartyCal extends Core_PartyCal implements Core_Interface_Controller_PartyCal {

	public function __construct( $argv ) 
	{
		$this->helpString = _('basic client for viewing data in db and config');
		parent::__construct(  $argv );
	}

	public function actionListProviders() {

		$callback = create_function('$k,$p' , '
			return $p->name.": ".$p->description."\n";');

		$list = $this->providers->iterateWithCallback( $callback );

		file_put_contents( 'php://output' , $list->getArrayCopy() );
	}

	public function actionListSubscribers() {

		$callback = create_function('$k,$s' , '
			return $s->name.": ".$s->description."\n";');

		$list = $this->subscribers->iterateWithCallback( $callback );

		file_put_contents( 'php://output' , $list->getArrayCopy() );
	}

	public function actiondump() {

	if (true) {
		$stmt = $this->pdo->prepare('SELECT COUNT(*) AS NUM FROM event');
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				echo 'Dumping '.$row['NUM'].' Events'."\n";
			}
		}

		$stmt = $this->pdo->prepare('SELECT * FROM event');
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				var_dump('NAME', $row['event_name']);
/**				var_dump('SHORT', $row['desc_text'], 'LONG', $row['desc_html']);
				var_dump('LOC' , $row['location']);
				var_dump('TAGS' , $row['style_tags']);
				var_dump('COST', $row['free'], $row['cost_text']);*/
				var_dump($row['venue_link']);
			}
		}
		return;
	}
		$stmt = $this->pdo->prepare("
                                SELECT start_ts ,  strftime('%s', start_ts)
                                FROM event 
				WHERE strftime('%s', start_ts) < strftime('%s', 'now')
				ORDER BY strftime('%s', start_ts) DESC
		");

		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				var_dump($row);
			}
		}
	}

}

?>
