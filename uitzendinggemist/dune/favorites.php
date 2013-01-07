<?php
    error_reporting(E_WARNING);

    header('Content-type: text/plain');
?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
num_rows = 3
async_icon_loading = yes

<?php
    include_once '../lib_ugemist.php';
	include_once '../lib_favorites.php';
    
    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";

    $num = 0;
	foreach(readFavorites('../favorieten_uitzendinggemist.xml') as $programma)
	{
		writeProgramma($baseurl, $num++, $programma['caption'], $programma['banner'], $programma['id']);
	}

	function writeProgramma($baseurl, $num, $caption, $url_icon, $aflevering_key)
	{
		$duneUrl = $baseurl.'/afleveringen.php?programid='.urlencode($aflevering_key)."\n";

		echo "item.$num.icon_path = $url_icon\n";
		echo "item.$num.scale_factor = 1\n";
		echo "item.$num.caption = $caption\n";
		echo "item.$num.media_url = dune_$duneUrl\n";
	}

?>