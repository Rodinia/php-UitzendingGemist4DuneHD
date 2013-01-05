<?php
	include 'dune.php';

    header('Content-type: text/plain; charset=utf-8');

    $num=0;

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

 ?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
num_rows = 2
async_icon_loading = yes
<?php   
    $imgdir=dirname($baseurl).'/img';
    writeIcon($num++, 'Favorieten', 'dune_'.$baseurl.'/favorites.php', $imgdir.'/favorites.png');
    writeIcon($num++, 'Zapp (6 - 12 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zapp', $imgdir.'/zapp.png');
    writeIcon($num++, 'Zappelin (2 - 5 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zappelin', $imgdir.'/zappelin.png');
    writeIcon($num++, 'Omroepen', 'dune_'.$baseurl.'/omroepen.php');
    writeIcon($num++, 'Programm\'s A..Z', 'dune_'.$baseurl.'/programmas_az.php', $imgdir.'/a-z.png');
    writeIcon($num++, 'VARA Gemist', 'dune_'.$baseurl.'/vara_list.php', $imgdir.'/varagemist.png');

?>
