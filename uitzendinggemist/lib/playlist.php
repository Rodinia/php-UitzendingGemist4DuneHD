<?php

function writePlaylist($streamurl, $type = 'asx')
{
    if(!$streamurl) die('streamurl cannot be null');
    switch($type)
    {
        case 'm3u': write_m3u($streamurl); break;
        case 'asx':
        default:    writeAsx($streamurl);  break;
    }
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

?>