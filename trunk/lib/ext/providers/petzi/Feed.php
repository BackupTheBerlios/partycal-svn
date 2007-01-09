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

/*
	<item>
            <title>23.02.2007: Brink Man Ship + Projet Bouvier (Ebullition)</title>
            <link>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5229</link>
            <description />
            <author>office@ebull.ch</author>
            <category>Electro, Jazz</category>
            <pubDate>Sun, 31 Dec 2006 22:02:14 +0100</pubDate>
            <guid>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5229</guid>
        <petzi:eventTitle>Brink Man Ship + Projet Bouvier</petzi:eventTitle>
        <petzi:eventDate>2007-02-23</petzi:eventDate>
        <petzi:eventType>Electro, Jazz</petzi:eventType>
        <petzi:eventTime>22:00:00</petzi:eventTime>
        <petzi:eventDoors>22:00:00</petzi:eventDoors>
        <petzi:eventPrice>18</petzi:eventPrice>
        <petzi:eventPriceType>0</petzi:eventPriceType>
        <petzi:eventHasAdvanceSale>1</petzi:eventHasAdvanceSale>
        <petzi:clubName>Ebullition</petzi:clubName>
        <petzi:clubStreet>Rue de Vevey 34</petzi:clubStreet>
        <petzi:clubPostalCode>1630</petzi:clubPostalCode>
        <petzi:clubCity>Bulle</petzi:clubCity>
        <petzi:clubCanton>FR</petzi:clubCanton>
        <petzi:clubPhone>026 913 90 33</petzi:clubPhone>
        <petzi:clubWebsite>www.ebull.ch</petzi:clubWebsite>
        <petzi:clubMail>office@ebull.ch</petzi:clubMail>
        </item
*/
		if ($item->eventDoors() == '00:00:00') {
			$start_ts = $item->eventDate().'T'.$item->eventTime().'+01:00';
		} else {
			$start_ts = $item->eventDate().'T'.$item->eventDoors().'+01:00';
		}

		$end_ts = $item->eventDate().'T24:00:00+01:00';

		$desc_text = ''.htmlspecialchars($item->eventTitle())."\n"
		       . 'Genre: '.htmlspecialchars($item->eventType())."\n"
		       . 'Doors: '.$item->eventDoors()."\n"
		       . 'Venue: '
		       . htmlspecialchars($item->clubName())
		       . ' ('.htmlspecialchars($item->clubWebsite).')'."\n\n"
		       . 'Tickets or more Infos: '.htmlspecialchars($item->link())."\n\n";
		$desc_text .= 'Data source: http://www.petzi.ch '."\n";
		$desc_text .= 'commercial use is prohibited - see http://tickets.petzi.ch/rss.php for license';


		$desc_text_nolinks = htmlspecialchars( $item->eventTitle() )."\n"
				   . 'Genre: '.htmlspecialchars($item->eventType())."\n"
				   . 'Doors: '.$item->eventDoors()."\n"
				   . 'Venue: '. htmlspecialchars($item->clubName()) . "\n\n";
		$desc_text_nolinks .= 'Data source: petzi.ch '."\n";
		$desc_text_nolinks .= 'commercial use is prohibited - see tickets.petzi.ch/rss.php for license';

		$desc_html = '<p><strong>' . htmlspecialchars( $item->eventTitle() ) . "</strong>\n"
			   . '<p>Data source: <a href="http://www.petzi.ch"><img src="http://petzi.ch/images/logo_petzi.jpg"/></a>'
			   . '<p>commercial use is prohibited - <a href="http://tickets.petzi.ch/rss.php">data license</a>';

		$desc_wiki = '';


		$venue_name = $item->clubName();
		$city_name = $item->clubPostalCode().' '.$item->clubCity().', CH';
		$location = $venue_name.', '.$city_name;
		$style_tags = $item->category();
		
		$r = array (
			'start_ts' => $start_ts,
			'end_ts' => $end_ts,
			'event_name' => htmlspecialchars($item->eventTitle()),
			'desc_text' => $desc_text,
			'desc_text_nolinks' => $desc_text_nolinks,
			'desc_html' => $desc_html,
			'desc_wiki' => $desc_wiki,
			'cost_text' => $cost_text,
			'venue_name' => $venue_name,
			'venue_link' => $venue_link,
			'city_name' => $city_name,
			'city_postal' => $city_postal,
			'location' => $location,
			'link' => $item->link(),
			'style_tags' => $style_tags,
			'raw_data' => $item->saveXML()
		);
		return $r;
	}

}

?>
