<?php
	include 'dune.php';

    header('Content-type: text/plain; charset=utf-8');

    $num=0;

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

    echo "use_icon_view = no\n";
    writeItem($num++, 'Favorieten', 'dune_'.$baseurl.'/favorites.php');
    writeItem($num++, 'Zapp (6 - 12 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zapp');
    writeItem($num++, 'Zappelin (2 - 5 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zappelin');
    writeItem($num++, 'Omroepen', 'dune_'.$baseurl.'/omroepen.php');
    writeItem($num++, 'Programm\'s A..Z', 'dune_'.$baseurl.'/programmas_az.php');
    writeItem($num++, 'VARA Gemist', 'dune_'.$baseurl.'/vara_list.php');

?>
