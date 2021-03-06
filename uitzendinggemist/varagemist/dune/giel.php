<?php

header('Content-type: text/plain; charset=utf-8');

#Enable display errors

require_once '../lib_giel.php';
require_once '../../lib/dune.php';

$nr = 0;

$baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

function writeCarouselItems($path, $imgXpath)
{
    global $baseurl;
    $vara_stream_url = dirname($baseurl).'/vara_stream.php?type=dune&mediaid=';
    $nr = 0;
    foreach(getCarouselItems('http://giel.vara.nl/'.$path, $imgXpath) as $item)
    {
        writeItem($nr++, $item['title'], $vara_stream_url.$item['mediaid'], 'play', $item['imgsrc']);
    }
}

if( isset($_GET['rubriek']) )
{
	echo "use_icon_view = exlist\n";
    echo "async_icon_loading = yes\n";
    writeCarouselItems('rubrieken/'.$_GET['rubriek'].'/', 'img');
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
	echo "use_icon_view = exlist\n";
    echo "async_icon_loading = yes\n";
    writeCarouselItems('artiesten/artiest-detail/artikel/'.$_GET['artiest'].'/', 'img[0]');
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