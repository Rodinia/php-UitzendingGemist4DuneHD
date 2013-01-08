<?php
	require_once '../lib_ugemist.php';
    require_once '../../lib/dune.php';
    
    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);
	
	header('Content-type: text/plain');
	
	echo "# Omroepen\n";
	
	$elements = wgetBroadcasters();
	
	$baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
	
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";
?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 5
async_icon_loading = yes
<?php
   
	$num = 0;

	foreach ($elements as $element)
	{
		$href=$element->getAttribute('href');
		$title=$element->getAttribute('title');
		$omroepId=substr($href, 10);
		$imgsrc='http:'.$element->getElementsByTagName('img')->item(0)->getAttribute('src');
		
		$url = $baseurl.'/programmas.php?omroep='.urlencode($omroepId);
		echo "\n";
		//writeItem($num++, $title, 'dune_'.$url);
		writeIcon($num++, $title, 'dune_'.$url, $imgsrc);
	}

?>
