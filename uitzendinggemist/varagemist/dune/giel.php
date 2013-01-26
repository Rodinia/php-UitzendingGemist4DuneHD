<?php

header('Content-type: text/plain; charset=utf-8');

#Enable display errors
error_reporting(E_WARNING);

require_once '../lib_giel.php';
require_once '../../lib/dune.php';

$nr = 0;

$baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

function writeCarouselItems($path)
{
    global $baseurl;
    $nr = 0;
    foreach(getCarouselItems('http://giel.vara.nl/'.$path) as $li)
    {
        $a = $li->getElementsByTagName('a')->item(0);
        $href = $a->getAttribute('href');
        $title = $li->getElementsByTagName('div')->item(0)->nodeValue;
        $mediaid=end(explode( "/", $href));

        $url = $baseurl.'/vara_stream.php?type=dune&mediaid='.$mediaid;
        writeItem($nr++, $title, $url, 'play');
    }
}

if( isset($_GET['rubriek']) )
{
	writeCarouselItems('rubrieken/'.$_GET['rubriek'].'/');
}
else if( isset($_GET['artiesten']) )
{
    foreach(getArtiesten() as $link)
    {
        $title = $link->nodeValue;
        $href = $link->getAttribute('href');
        $artiest = trim(substr($href, 33), "/");
        $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/giel.php?artiest='.urlencode( $artiest);
        writeItem($nr++, $title, $url, 'item');
    }
    echo "</ul>\n";
}
else if( isset($_GET['artiest']) )
{
    writeCarouselItems('artiesten/artiest-detail/artikel/'.$_GET['artiest'].'/');
}
else if( isset($_GET['rubrieken']) )
{
	foreach(getGielRubrieken() as $rubriek)
	{
		$caption = $rubriek->getAttribute('title');
		$href = $rubriek->getAttribute('href');
		$rubriek = trim(substr($href, 10), "/");
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/giel.php?rubriek='.urlencode($rubriek);
		echo "\n";
		writeItem($nr++, $caption, $url, 'item');
	}
}
else
{
    $baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    writeItem($nr++, 'Rubrieken', $baseurl.'/giel.php?rubrieken', 'item');
    writeItem($nr++, 'Artiesten', $baseurl.'/giel.php?artiesten', 'item');
}
?>