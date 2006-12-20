<?php
/**
 * The main class for CLI based usage of PartyCal.
 *
 * Released under the GNU GPL
 * Lucas S. Bickel 2006
 */

class PartyCal {

	public $conf;
	public $pdo;
	public $providers;
	public $subscribers;

	public function __construct( $mode , $argv ) {

		$this->argv = $argv;
		$this->conf = new Config_PartyCal( $mode );

		$this->pdo = new PDO( $this->conf->db_dso , null, null,
		     array(PDO::ATTR_PERSISTENT => true));
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->providers = new ProviderArray_PartyCal ( $this->conf->getProviderListing() );
		$this->subscribers = new SubscriberArray_PartyCal ( $this->conf->getSubscriberListing() );
	}

	public function main() {

		switch( $this->argv[1] ) {
			case NULL:
			case '-h':
			case '--help':
				$this->actionhelp();
				break;
			default:
				$func = 'action'.$this->argv[1];
				$this->$func();
		}
	}

	public $helpString = 'basic client for viewing data in db and config';
	public function actionhelp() {

		echo __CLASS__.' -- '.$this->helpString."\n\n";
		echo 'Usage: '.$this->argv[0].' [COMMAND] '."\n";
		echo '       COMMAND: '.'i need code to dump this from class'."\n";
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
