<?php

function writePlaylist($streamurl, $type = 'asx', $contentType = false)
{
    if(!$streamurl) die('streamurl cannot be null');
    switch($type)
    {
        case 'dune': 
            require_once dirname(__FILE__).'/dune.php';
            dunePlay($streamurl, $contentType);
            break;
        case 'm3u': 
            write_m3u($streamurl);
            break;
        case 'redirect':
            redirectToUrl($streamurl);
            break;
        case 'asx':
        default:    
            writeAsx($streamurl);
            break;
    }
}

function redirectToUrl($url)
{
    header('Location: '.$url);
}

function writeAsx($href)
{
    header('Content-type: video/x-ms-asf');
    echo "<ASX version=\"3\">\n";
    echo "<Entry>\n";
    echo "	<ref href=\"$href\" />\n";
    echo "</Entry>\n";
    echo "</ASX>\n";
}

function write_m3u($url)
{
    header('Content-type: audio/x-mpegurl'); // audio/x-mpegurl, audio/mpeg-url, application/x-winamp-playlist, audio/scpls, audio/x-scpls
    echo $url."\n";
}

// Utility which follow redirects since the Dune HD does not support HTTP redirects
function followRedirects($streamurl, &$contentType, $maxRedirects = 4)
{
    $numRedirects=0;
    while($numRedirects<$maxRedirects)
    {
        $newUrl = checkRedirectUrl($streamurl, $contentType);
        //echo "#   content-type: $contentType\n";
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
        // echo "# redirected.\n";
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
        //echo "#[$url] header: $header\n";
        if( startsWith($header, 'Location: ') )
        {
            $location = substr($header, 10);
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
    $html = $dom->loadHTMLFile($url_asx);

    if(!$html) return null;
    
    $xpath = new DOMXpath($dom);
    
    foreach($xpath->query("//ref") as $ref)
    {
        return trim( $ref->getAttribute('href') );
    }
}

?>