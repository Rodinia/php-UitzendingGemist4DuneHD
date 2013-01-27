<?php
	/*
     * Generate Uitzending Gemist Playlist
     * arguments:
     *   type  = [asx | m3u | dune]
     *   epiid = <Uitzending Gemist episode / aflevering ID>
     */
    
    error_reporting(E_ALL & ~E_NOTICE);
    require_once 'lib/playlist.php';
	require_once 'lib/lib_ugemist.php';
	
	$type  = isset($_GET['type'])  ? $_GET['type']  : 'asx';
    $epiid = isset($_GET['epiid']) ? $_GET['epiid'] : wgetEpisodeId($_GET['localepiid']);
		
    $streams = getStreams($epiid, getSessionKey());
    
    $streamurl = null;
    // Check for redirects
    foreach($streams as $stream)
    {
        $format =  $stream->getAttribute('compressie_formaat');
        $quality = $stream->getAttribute('compressie_kwaliteit');
        $streamurl = trim($stream->getElementsByTagName('streamurl')->item(0)->nodeValue);
        
        $streamurl = followRedirects($streamurl, $contentType);
        
        if($contentType == 'application/smil')
		{
			if($type == 'dune')
            {
                echo "#   Retrieve SMIL file from: $streamurl\n";
                //$streamurl = wgetVideoSrcFromSmil($streamurl);
                //echo "#   SMIL stream URL = $streamurl\n";
                            
                echo "#   Skip MP4/rtsp stream, not support by Dune HD, content-Type: $contentType, url: $streamurl\n";
                echo "#   Continue with next best stream.\n";
            }
			continue;
		}

		if($streamurl == null)
		{
			duneError("To many redirects.");
			exit;
		}
        
        break;
    }
	
	writePlaylist($streamurl, $type, $contentType);
	
?>
