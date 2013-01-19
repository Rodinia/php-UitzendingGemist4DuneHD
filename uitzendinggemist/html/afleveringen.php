<?php
	// Enable display errors
	error_reporting(E_ALL);
	
	header('content-type: text/html; charset=utf-8');

	require_once '../lib/lib_ugemist.php';

	$pageOffset = isset($_GET['page']) ? $_GET['page'] : 1;
	
	$max_pages = 3;

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?php print $program_id; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	
	function writeEpisodes($episodes, $program_id = 0)
	{
		echo "<table>\n";
		echo "<tr><th>Caption</th><th colspan=\"2\">Play</th></tr>\n";

		foreach($episodes as $episode)
		{
			$q_episode = 'localepiid='.$episode['localepiid'];
			if($program_id)
				$q_episode .= '&programid='.$program_id; 
			
			echo '<tr>';
			echo '<td>'.htmlspecialchars($episode['caption']).'</td>';
			echo '<td><a href="../playlist.php?type=asx&'.$q_episode.'"><img alt="play" src="img/windows_media_player_32.png"/></a></td>';
			echo '<td><a href="../playlist.php?type=m3u&'.$q_episode.'"><img alt="play" src="img/media-playback-start_32.png"/></a></td>';
			echo '<td><a href="episode.php?'.$q_episode.'">Meta Data</a></td>';
			echo '<td><a href="../dune/duneplay.php?'.$q_episode.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a></td>';
			//echo '<td><a href="'.makeSerieMetaDataUrl($amd['serie_id'], $sessionKey).'">Meta Data Serie</a></td>';
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
    
    if( isset($_GET['programid']) )
	{
		$program_id = $_GET['programid'];

		echo '<h1>'.$program_id."</h1>\n";
		$url_ug = 'http://www.uitzendinggemist.nl/programmas/'.urlencode($program_id).'/';
		
		echo '<a href="'.$url_ug.'"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
		echo '<a href="../dune/afleveringen.php?'.$_SERVER['QUERY_STRING'].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";
		
		$episodes = wgetEpisodesByProgram($url_ug, $max_pages, $pageOffset, $program_id);
		writeEpisodes($episodes);

		$pageOffset += $max_pages;
		echo '<a href="?programid='.urlencode($program_id).'&page='.($pageOffset).'">Next Page</a>';
	}
    else if( isset($_GET['when']) )
    {
		$when = $_GET['when'];
        if($when=='vandaag')
        {
            echo "<h1>Vandaag</h1>\n";
        }
        else if($when=='gisteren')
        {
            echo "<h1>Gisteren</h1>\n";
        }
        $url_ug = "http://www.uitzendinggemist.nl/weekarchief/$when?display_mode=detail";

		echo '<a href="'.$url_ug.'"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
		echo '<a href="../dune/afleveringen.php?'.$_SERVER['QUERY_STRING'].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";

		$episodes = wgetEpisodesWeekarchief($url_ug, $max_pages, $pageOffset);
		writeEpisodes($episodes);

		$pageOffset += $max_pages;
		//echo '<a href="?when='.urlencode($when).'&page='.($pageOffset).'">Next Page</a>';
    }

?>
</body>
</html>