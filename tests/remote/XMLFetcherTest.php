<?php

use PHPUnit\Framework\TestCase;

final class XMLFetcherTest extends TestCase {

    /*
     * TODO: Mock XML URIs out here.
     */

    public function testFetchesContentAndCreatesSimpleXmlElement() {
        $fetcher = new XMLFetcher();
        $this->assertInstanceOf(
            SimpleXMLElement::class,
            $fetcher->fetch($this->validXmlUri));
    }

    private $validXmlUri = "https://wvv.it4sport.de/data/vbnw/aufsteiger/public/tabelle_2016_301.xml";
}

?>