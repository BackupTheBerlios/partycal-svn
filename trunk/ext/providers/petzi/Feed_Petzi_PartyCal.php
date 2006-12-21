<?php
/**
 * Provider integration for petzi.ch.
 *
 * consider this the main place for developing the providers api for now
 */

/**
 * Zend Framework Feed
 */
require_once 'Zend/Feed.php';

/**
 * reader for Petzi Feeds.
 *
 * this will get changed to an object composition
 */
class Feed_Petzi_PartyCal extends Zend_Feed {

	public function getInsertData(Zend_Feed_EntryRss $item) {

		$start_ts = $item->eventDate().'T'.$item->eventDoors().'+01:00';
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
		
		return array (
			'start_ts' => $start_ts,
			'end_ts' => $end_ts,
			'event_name' => htmlspecialchars($item->eventTitle()),
			'shortdesc' => $sDesc,
			'longdesc' => $lDesc,
			'location' => $location,
			'link' => $item->link()
		);
	}

}

?>
