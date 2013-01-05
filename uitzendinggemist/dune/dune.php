<?php

include_once '../util.php';

function writeItem($nr, $caption, $url, $action = 0)
{
	$caption = str_replace("\n", ' ', $caption);
	$caption = str_replace("\r", '', $caption);
	echo "item.$nr.caption = $caption\n";
	echo "item.$nr.media_url = $url\n";
	if($action)
		echo "item.$nr.media_action = $action\n";
}

function writeIcon($num, $caption, $url, $url_icon)
{
	$caption = str_replace("\n", ' ', $caption);
	$caption = str_replace("\r", '', $caption);
	
    echo "item.$num.caption = $caption\n";
    echo "item.$num.icon_path = $url_icon\n";
    echo "item.$num.scale_factor = 1\n";
    echo "item.$num.media_url = $url\n";    
}

function dunePlay($duneurl, $contentType)
{
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

function followRedirects($streamurl, &$contentType, $maxRedirects = 4)
{
	$numRedirects=0;
	while($numRedirects<$maxRedirects)
	{
		echo "# checking: $streamurl\n";
		$newUrl = checkRedirectUrl($streamurl, $contentType);
		echo "# contentType: $contentType\n";
		if(!$newUrl)
		{
			// Would expect  video/x-ms-asf is returned for ASX, but typically UG returns video/x-ms-wmv
			if( $contentType == "video/x-ms-wmv" || $contentType == "video/x-ms-asf")
			{
				$asxRef = getAsxRef($streamurl);
				//echo "# getAsxRef()=$asxRef\n";
				if( startsWith($asxRef, 'mms://') )
					return $asxRef;
				$newUrl = $asxRef;
			}
			else
				break;
		}
		$streamurl = $newUrl;
		echo "# redirected.\n";
		++$numRedirects;
	}
	return $streamurl;
}

function checkRedirectUrl($url, &$contentType)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_HEADER, true); // Only get header (will do HEAD-request, rather then GET-request)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET'); // Override HEAD-request

	$out = curl_exec($ch);

	// line endings is the wonkiest piece of this whole thing
	$out = str_replace("\r", "", $out);

	// only look at the headers
	$headers_end = strpos($out, "\n\n");
	if( $headers_end !== false )
	{
		$out = substr($out, 0, $headers_end);
	}

	$location = NULL;
	$contentType = NULL;

	$headers = explode("\n", $out);
	foreach($headers as $header)
	{
		// echo "# header: $header\n";
		if( startsWith($header, 'Location: ') )
		{
			$location = substr($header, 10);
		}
		else if(  startsWith($header, 'Content-Type: ') )
		{
			$contentType = substr($header, 14);
		}
		else if(  startsWith($header, 'Content-Type: ') )
		{
			$contentType = substr($header, 14);
		}
	}

	curl_close($ch);

	return $location;
}


function getAsxRef($url_asx)
{
	$dom = new DOMDocument();

	$episodes = array(); // result

	$pagefound = false;
	$page = $page_offset;
	$num = 0;

	$url = $ug_search_url . '&page='.$page++;

	$html = $dom->loadHTMLFile($url_asx);

	if(!$html) return NULL;

	foreach($dom->getElementsByTagName('entry') as $entry)
	{
		foreach($entry->getElementsByTagName('ref') as $ref)
		{
			return trim( $ref->getAttribute('href') );
		}
	}
}
?>