<?php

// Determine if the given numeric (use ip2long()) is in a IPv4 private range (RFC 1918)    
function isPrivateRange($numIp)
{
#       10.0.0.0 ..  10.255.255.255 =>   167772160 ..   184549375
#     172.16.0.0 ..  172.31.255.255 => -1408237568 .. -1407188993
#    192.168.0.0 .. 192.168.255.255 => -1062731776 .. -1062666241
	return ($numIp>-1062731776 && $numIp<-1062666241) 
	    || ($numIp>  167772160 && $numIp<  184549375)
	    || ($numIp>-1408237568 && $numIp<-1407188993);
}
    
function startsWith($haystack, $needle)
{
	return !strncmp($haystack, $needle, strlen($needle));
}

// Work arround for PHP since character encoding does not working properly loading html into DOM
function loadHtmlAsDom($url)
{
	$body = file_get_contents($url);
	
	// Insert a head and meta tag immediately after the opening <html> to force UTF-8 encoding
	$insertPoint = false;
	if (preg_match("/<html.*?>/is", $body, $matches, PREG_OFFSET_CAPTURE)) {
		$insertPoint = mb_strlen( $matches[0][0] ) + $matches[0][1];
	}
	if ($insertPoint) {
		$body = mb_substr(
			$body,
			0,
			$insertPoint
		) . "<head><meta http-equiv='Content-type' content='text/html; charset=UTF-8' /></head>" . mb_substr(
			$body,
			$insertPoint
		);
	}
	
	$dom = new DOMDocument;
	$dom->loadHTML($body);
	return $dom;
}

?>