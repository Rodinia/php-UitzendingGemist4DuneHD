<?php

require_once dirname(__FILE__).'/../config.php';

if( !function_exists('apache_request_headers') ) {
///
function apache_request_headers() {
  $arh = array();
  $rx_http = '/\AHTTP_/';
  foreach($_SERVER as $key => $val) {
    if( preg_match($rx_http, $key) ) {
      $arh_key = preg_replace($rx_http, '', $key);
      $rx_matches = array();
      // do some nasty string manipulations to restore the original letter case
      // this should work in most cases
      $rx_matches = explode('_', $arh_key);
      if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
        foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
        $arh_key = implode('-', $rx_matches);
      }
      $arh[$arh_key] = $val;
    }
  }
  return( $arh );
}
///
}
///

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

function curlGet($url)
{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$htmlContent = curl_exec($ch);
		$ch_info = curl_getinfo($ch);
		if($ch_info['http_code'] == 200)
		{
			return $htmlContent;
		}
		//print_r($ch_info);
		throw new Exception('Failed load HTML from URL: '.$url.', http-code: '.$ch_info['http_code']);
}

function loadXmlAsDom($url)
{
    global  $useCurlLoad;
    $dom = new DOMDocument;
	if( $useCurlLoad )
    {
        $xml = curlGet($url);
        echo "$xml\n";
        if(!$xml)
		{
			throw new Exception('Failed to load XML from URL: '.$url.', error message: '.$last_error['message']);
		}
        $dom->loadXML($xml);
    }
    else
    {
        $dom->load($url);
    }
    return $dom;
}

// Work arround for PHP since character encoding does not working properly loading html into DOM
function loadHtmlAsDom($url)
{
	global  $useCurlLoad;
	$body = $useCurlLoad ? curlGet($url) : file_get_contents($url);
	if(!$body)
	{
		$last_error = error_get_last();
		die('Failed load HTML from URL: '.$url.', error message: '.$last_error['message']);
	}
	
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
    # Suppress DOM warnings
    libxml_use_internal_errors(true);
	$dom->loadHTML($body);
	return $dom;
}


?>