<?php
/**
 * Provider integration for petzi.ch.
 *
 * consider this the main place for developing the providers api for now
 *
 * @package Provider
 * @subpackage Petzi
 */

/**
 * Zend Framework Feed
 */
require_once 'Zend/Feed.php';

/**
 * Provider Feed Interface.
 */
require_once 'Provider/Feed/Interface.php';

/**
 * reader for Petzi Feeds.
 *
 * this will get changed to an object composition
 *
 * @subpackage Petzi
 */
class Feed_Petzi_PartyCal extends Zend_Feed implements Provider_Feed_Interface_PartyCal {

	public function getLink( Zend_Feed_EntryRss $item ) {
		return $item->link();
	}

	public function getUpdateData( Zend_Feed_EntryRss $item ) {
		$r = array();
		$r['raw_data'] = $item->saveXML();

		$r['flags'] = array();
		$r['flags']['cancelmsg'] = false;

		return $r;
	}


	/**
	 * Get data used for inserting into the DB.
	 *
	 * this cooks all data to whats needed by providers (all of them) later...
	 *
	 * for now the event end_ts is 00:00. some algorithms will be 
	 * needed for the following todo
	 * 
	 * @todo do something about events that start between 23:00 and 6:00
	 */
	public function getInsertData( Zend_Feed_EntryRss $item ) {

		if ($item->eventDoors() == '00:00:00') {
			$start_ts = $item->eventDate().'T'.$item->eventTime().'+01:00';
		} else {
			$start_ts = $item->eventDate().'T'.$item->eventDoors().'+01:00';
		}

		$end_ts = $item->eventDate().'T24:00:00+01:00';
/*
            <title>12.01.2007: Tight Finks &amp; Fuckadies (OX Kultur)</title>
            <link>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5131</link>
            <description />
            <author>info@oxx.ch</author>
            <category>Garage, Punk, Rock</category>
            <pubDate>Wed, 13 Dec 2006 13:00:04 +0100</pubDate>
            <guid>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5131</guid>
        <petzi:eventTitle>Tight Finks &amp; Fuckadies</petzi:eventTitle>
        <petzi:eventDate>2007-01-12</petzi:eventDate>
        <petzi:eventType>Garage, Punk, Rock</petzi:eventType>
        <petzi:eventTime>22:00:00</petzi:eventTime>
        <petzi:eventDoors>21:00:00</petzi:eventDoors>
        <petzi:eventPrice>16</petzi:eventPrice>
        <petzi:eventHasAdvanceSale>1</petzi:eventHasAdvanceSale>
        <petzi:clubName>OX Kultur</petzi:clubName>
        <petzi:clubStreet>Ochsengasse</petzi:clubStreet>
        <petzi:clubPostalCode>4800</petzi:clubPostalCode>
        <petzi:clubCity>Zofingen</petzi:clubCity>
        <petzi:clubCanton>AG</petzi:clubCanton>
        <petzi:clubPhone>062 751 93 74</petzi:clubPhone>
        <petzi:clubWebsite>www.oxx.ch/</petzi:clubWebsite>
        <petzi:clubMail>info@oxx.ch</petzi:clubMail>
*/
		$sDesc = ''.htmlspecialchars($item->eventTitle())."\n"
		       . 'Genre: '.htmlspecialchars($item->eventType())."\n"
		       . 'Doors: '.$item->eventDoors()."\n"
		       . 'Venue: '
		       . htmlspecialchars($item->clubName())
		       . ' ('.htmlspecialchars($item->clubWebsite).')'."\n\n"
		       . 'Tickets or more Infos: '.htmlspecialchars($item->link())."\n\n";
		/**

		$lDesc = $sDesc
		       . $item->clubStreet()."\n\n"
		       . $item->clubPostalCode().' '.$item->clubCity()."\n\n";

		if ($item->eventHasAdvanceSale() == 1) {
			$petzilink = 'buy tickets: '.$item->link();
		} else {
			$petzilink = 'more infos: $item->link()';
		}
		*/
		$petzilink .= 'Data source: http://www.petzi.ch '."\n";
		$petzilink .= 'commercial use is prohibited - see http://tickets.petzi.ch/rss.php for license';
		$lDesc .= $petzilink;
		$sDesc .= $petzilink;

		$location = $item->clubName().', '.$item->clubCanton().'-'.$item->clubPostalCode().' '.$item->clubCity().', CH';
		
		$r = array (
			'start_ts' => $start_ts,
			'end_ts' => $end_ts,
			'event_name' => htmlspecialchars($item->eventTitle()),
			'shortdesc' => $sDesc,
			'longdesc' => $lDesc,
			'location' => $location,
			'link' => $item->link(),
			'raw_data' => $item->saveXML()
		);
		return $r;
	}

}

?>
