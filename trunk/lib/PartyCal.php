<?php
/**
 * Basic Controller for CLI Interaction.
 *
 * @copyright Released under the GNU GPL, see LICENSE for more Information
 * @author Lucas S. Bickel 
 * @package core
 */

/**
 * Abstract Controller.
 */
require_once 'PartyCalCore.php';

/**
 * The main class for CLI based usage of PartyCal.
 */
class PartyCal extends PartyCalCore{

	public function __construct( $mode , $argv ) 
	{
		$this->helpString = _('basic client for viewing data in db and config');
		parent::__construct( $mode , $argv );
	}

	public function actionlistproviders() {

		echo $this->providers;
	}

	public function actionlistsubscribers() {

		echo $this->subscribers;
	}

	public function actiondump() {
		$stmt = $this->pdo->prepare('SELECT COUNT(*) AS NUM FROM event');
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				echo 'Dumping '.$row['NUM'].' Events'."\n";
			}
		}

		$stmt = $this->pdo->prepare('SELECT * FROM event');
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				var_dump('NAME', $row['name']);
				var_dump('SHORT', $row['shortdesc'], 'LONG', $row['longdesc']);
			}
		}
	}

}

?>
