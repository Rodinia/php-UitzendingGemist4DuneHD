<?php
require_once dirname(__FILE__).'/log.php';
require_once dirname(__FILE__).'/util.php';
require_once dirname(__FILE__).'/../config.php';

global $useMySQL;
if($useMySQL)
{
    require_once dirname(__FILE__).'/lib_storage.php';
    registerMediaPlayer();
}

function writeItem($nr, $caption, $url, $action = false, $icon_url = false, $detailed_info = false)
{
	$caption = str_replace("\n", ' ', $caption);
	$caption = str_replace("\r", '', $caption);
	echo "item.$nr.caption = $caption\n";
	echo "item.$nr.media_url = $url\n";
    if($action)        echo "item.$nr.icon_path = $icon_url\n";
	if($icon_url)      echo "item.$nr.media_action = $action\n";
    if($detailed_info) echo "item.$nr.item_detailed_info = $detailed_info\n";

    //echo "item.$nr.item_small_icon_name = video_file\n";
    //echo "item.$nr.folder_small_icon_name = video_file\n";
}

function writeIcon($num, $caption, $url, $url_icon = 0)
{
	$caption = str_replace("\n", ' ', $caption);
	$caption = str_replace("\r", '', $caption);
	
    echo "item.$num.caption = $caption\n";
    if($url_icon)
        echo "item.$num.icon_path = $url_icon\n";
    //echo "item.$num.scale_factor = 1\n";
    echo "item.$num.media_url = $url\n";    
}

function dunePlay($duneurl, $contentType)
{
	header('Content-type: text/plain');
    
    echo "# Dune play content-type: $contentType\n";
	echo "# Dune play streamurl:    $duneurl\n";

	if($contentType == 'video/mp4')
	{
		// Enable special Dune-HD MP4 video stream buffering
		$duneurl=str_replace('http://', 'http://mp4://', $duneurl);
	}

	echo "paint_scrollbar=no\n";
	echo "paint_path_box=no\n";
	echo "paint_help_line=no\n";
	echo "paint_icon_selection_box=no\n";
	echo "paint_icons=no\n";
	writeItem(0, 'Play', $duneurl);
}

function duneError($error)
{
	echo "paint_scrollbar=no\n";
	echo "paint_path_box=no\n";
	echo "paint_help_line=no\n";
	echo "paint_icon_selection_box=no\n";
	echo "paint_icons=no'\n";
	writeItem(0, 'ERROR: '.$error, '');
}

function getDuneSerial()
{
    $headers = apache_request_headers();
    //return isset($headers['X-Dune-Serial-Number']) ? $headers['X-Dune-Serial-Number'] : "FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF";
    return $headers['X-Dune-Serial-Number'];
}

function getDuneLang()
{
    $headers = apache_request_headers();
    return $headers['X-Dune-Interface-Language'];
}

?>