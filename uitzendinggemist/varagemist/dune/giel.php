<?php

header('Content-type: text/plain; charset=utf-8');

#Enable display errors
error_reporting(E_WARNING);

require_once '../lib_giel.php';
require_once '../../lib/dune.php';

$rubriek = $_GET['rubriek'];

$nr = 0;
foreach(getCarouselItems($rubriek) as $li)
{
    echo "\n";
    $a = $li->getElementsByTagName('a')->item(0);
    $href = $a->getAttribute('href');
    echo "# href=$href\n";
    $caption = $li->getElementsByTagName('div')->item(0)->nodeValue;
    
    $media_id=split ( "/", $href);
    
    $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/vara_play.php?mediaid='.$media_id[3].'&hq=1';
    writeItem($nr++, $caption, $url, 'play');
}

?>