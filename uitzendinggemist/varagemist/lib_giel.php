<?php

require_once dirname(__FILE__).'/../lib/util.php';

function getGielRubrieken()
{
    return getHttpItems('http://giel.vara.nl/rubrieken/', "//div[@class='rubrieken-list-item' or @class='rubrieken-list-item ']/a");
}

function getArtiesten()
{
    return getHttpItems('http://giel.vara.nl/artiesten', "//div[@class='abc-unit line']/div[2]/ul/li/a");
}
/*
function getArtiest($url)
{
    $url  = 'http://giel.vara.nl/artiesten/artiest-detail/artikel/'.$artiest;
    return getHttpItems($url ,"//li[jcarouselindex=*]/div[0]/a");
}*/

function getCarouselItems($url)
{
    $result = array();
    foreach(getHttpItems($url, "//ul[@id='ankeilercarousel']/li") as $li)
    {
        $item = array();
        $a = $li->getElementsByTagName('a')->item(0);
        $item['href'] = $a->getAttribute('href');
        $item['title'] = $li->getElementsByTagName('div')->item(0)->nodeValue;
        $item['mediaid'] = end( explode("/", trim($item['href'], '/')) );
        $item['url'] = $vara_stream_url.$mediaid;
        $imgList = $a->getElementsByTagName('img');
        if($imgList->length > 0 )
        {
            $item['imgsrc'] = $imgList->item(0)->getAttribute('src');
        }
        $result[] = $item;
    }
    return $result;
}

function getHttpItems($url, $xpathQuery)
{
    //echo "# $url, $xpathQuery\n";
    $dom = loadHtmlAsDom($url);
    $xpath = new DOMXpath($dom);
    return $xpath->query($xpathQuery);
}

?>