<?php

header('Content-type: text/plain; charset=utf-8');

#Enable display errors
error_reporting(E_WARNING);

require_once '../lib_giel.php';
require_once '../../lib/dune.php';

$nr = 0;

if( isset($_GET['rubriek']) )
{
	$rubriek = $_GET['rubriek'];
	
	foreach(getCarouselItems($rubriek) as $li)
	{
		echo "\n";
		$a = $li->getElementsByTagName('a')->item(0);
		$href = $a->getAttribute('href');
		echo "# href=$href\n";
		$caption = $li->getElementsByTagName('div')->item(0)->nodeValue;
		
		$media_id=split ( "/", $href);
		
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['PHP_SELF'])).'/vara_stream.php?type=dune&mediaid='.$media_id[3];
		writeItem($nr++, $caption, $url, 'play');
	}
}
else
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

?>