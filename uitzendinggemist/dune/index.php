<?php
	error_reporting(E_WARNING);
    
    require_once '../lib/dune.php';
    
    header('Content-type: text/plain; charset=utf-8');

    $num=0;

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

 ?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 4
num_rows = 2
async_icon_loading = yes
<?php   
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";

   
    writeIcon($num++, 'Favorieten', 'dune_'.$baseurl.'/favorites.php', $imgdir.'/favorites.png');
    writeIcon($num++, 'Zapp (6 - 12 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zapp', $imgdir.'/zapp.png');
    writeIcon($num++, 'Zappelin (2 - 5 jaar)', 'dune_'.$baseurl.'/programmas.php?type=zappelin', $imgdir.'/zappelin.png');
    writeIcon($num++, 'Omroepen', 'dune_'.$baseurl.'/omroepen.php', $imgdir.'/nlkijkt.png');
    writeIcon($num++, 'Programm\'s A..Z', 'dune_'.$baseurl.'/programmas_az.php', $imgdir.'/a-z.png');
    writeIcon($num++, 'Vandaag', 'dune_'.$baseurl.'/afleveringen.php?when=vandaag', $imgdir.'/uitzendinggemist_250.png');
    writeIcon($num++, 'Gisteren', 'dune_'.$baseurl.'/afleveringen.php?when=gisteren', $imgdir.'/uitzendinggemist_250.png');
	$varagemisturl = dirname($baseurl).'/varagemist/dune/';
    writeIcon($num++, 'VARA Gemist', 'dune_'.$varagemisturl, $imgdir.'/varagemist.png');

?>
