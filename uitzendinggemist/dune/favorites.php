<?php
    error_reporting(E_ALL);

    header('Content-type: text/plain');
?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
num_rows = 3
async_icon_loading = yes

<?php
    include '../common.php';

    $num = 0;
	foreach(readFavorites('../favorieten_uitzendinggemist.xml') as $programma)
	{
		writeProgramma($num++, $programma['caption'], $programma['banner'], $programma['id']);
	}

	function writeProgramma($num, $caption, $url_icon, $aflevering_key)
	{
		$duneUrl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/afleveringen.php?program='.urlencode($aflevering_key)."\n";

		echo "item.$num.icon_path = $url_icon\n";
		echo "item.$num.scale_factor = 1\n";
		echo "item.$num.caption = $caption\n";
		echo "item.$num.media_url = dune_$duneUrl\n";
	}

?>