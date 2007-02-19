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
		$r['flags']['imgupdate'] = false;

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
            <title>16.02.2007: Landfall &amp; Pornolé (Kraftfeld)</title>
            <link>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5346</link>
            <description></description>
            <author>Info@kraftfeld.ch</author>
            <category>Alternative</category>
            <pubDate>Fri, 12 Jan 2007 16:44:06 +0100</pubDate>
            <guid>http://tickets.petzi.ch/detail_evenement.php?new_lang=en&amp;id_evenement=5346</guid>
        <petzi:eventTitle>Landfall &amp; Pornolé</petzi:eventTitle>
        <petzi:eventDate>2007-02-16</petzi:eventDate>
        <petzi:eventType>Alternative</petzi:eventType>
        <petzi:eventTime>22:00:00</petzi:eventTime>
        <petzi:eventDoors>21:00:00</petzi:eventDoors>
        <petzi:eventPrice>12</petzi:eventPrice>
        <petzi:eventPriceType>0</petzi:eventPriceType>
        <petzi:eventHasAdvanceSale>0</petzi:eventHasAdvanceSale>
        <petzi:eventTicketsAvailable>0</petzi:eventTicketsAvailable>
        <petzi:eventCanceled>0</petzi:eventCanceled>
        <petzi:clubName>Kraftfeld</petzi:clubName>
        <petzi:clubStreet>Lagerplatz 18</petzi:clubStreet>
        <petzi:clubPostalCode>8400</petzi:clubPostalCode>
        <petzi:clubCity>Winterthur</petzi:clubCity>
        <petzi:clubCanton>ZH</petzi:clubCanton>
        <petzi:clubPhone>052.202.02.04</petzi:clubPhone>
        <petzi:clubWebsite>www.kraftfeld.ch</petzi:clubWebsite>
        <petzi:clubMail>Info@kraftfeld.ch</petzi:clubMail>
        </item>
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

		$cost_text = $this->getCostText( $item );
		if ( $item->eventPriceType() == 1 ) {
			$free = 1;
		} else {
			$free = 0;
		}

		$venue_name = $item->clubName();
		$city_name = $item->clubPostalCode().' '.$item->clubCity().', CH';
		$location = $venue_name.', '.$city_name;
		$venue_link = 'http://' . $item->clubWebsite();

		$style_tags = $item->category();

		$enc = $this->enclosure();
		if ($enc['type'] == 'image/jpeg') {	
			$image = $enc['url'];
			$image_desc = $item->eventTitle();
		}
		
		$r = array (
			'start_ts' => $start_ts,
			'end_ts' => $end_ts,
			'event_name' => htmlspecialchars($item->eventTitle()),
			'desc_text' => $desc_text,
			'desc_text_nolinks' => $desc_text_nolinks,
			'desc_html' => $desc_html,
			'desc_wiki' => $desc_wiki,
			'cost_text' => $cost_text,
			'free' => $free,
			'venue_name' => $venue_name,
			'venue_link' => $venue_link,
			'city_name' => $city_name,
			'city_postal' => $city_postal,
			'location' => $location,
			'link' => $item->link(),
			'style_tags' => $style_tags,
			'image' => $image,
			'image_desc' => $image_desc,
			'raw_data' => $item->saveXML()
		);
		return $r;
	}

	public function getCostText( $item ) {

		switch( $item->eventPriceType() ) {
			case 0:
				if ( $item->eventHasAdvanceSale() == 1 ) {
					$p = $item->eventPrice() . ' CHF '
					   . '(advance booking, box office pricing might be slightly different)';
				} else {
					$p = $item->eventPrice() . ' CHF '
					    . '(box office)';
				}
				return $p;
			break;
			case 1: // free
			case 2: // unknown
				return '';
			break;
			case 3:
				return 'free but donations requested';
			break;
		}
	}
}

?>
