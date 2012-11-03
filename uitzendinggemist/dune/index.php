<?php
	include 'dune.php';

    header('Content-type: text/plain; charset=utf-8');

    $num=0;

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

    echo "use_icon_view = no\n";
    writeItem($num++, 'Favorieten', 'dune_'.$baseurl.'/favorites.php');
    writeItem($num++, 'Programm\'s A..Z', 'dune_'.$baseurl.'/programmas.php');
    writeItem($num++, 'VARA (Giel)', 'dune_'.$baseurl.'/vara_list.php');

?>
