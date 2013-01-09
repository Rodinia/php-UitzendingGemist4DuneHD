<?php

require_once dirname(__FILE__).'/../lib/util.php';

function getCarouselItems($rubriek)
{
    $url = 'http://giel.vara.nl/rubrieken/'.$rubriek.'/';
    return getHttpItems($url, "//ul[@id='ankeilercarousel']/li");
}

function getGielRubrieken()
{
    return getHttpItems('http://giel.vara.nl/rubrieken/', "//div[@class='rubrieken-list-item' or @class='rubrieken-list-item ']/a");
}

function getHttpItems($url, $xpathQuery)
{
    //echo "# $url, $xpathQuery\n";
    $dom = loadHtmlAsDom($url);
    $xpath = new DOMXpath($dom);
    return $xpath->query($xpathQuery);
}

?>