<?php
	// Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
	header('content-type: text/html; charset=utf-8');

	include_once '../lib_ugemist.php';

	$program_id = $_GET['programid'];
	$when = $_GET['when'];

	$pageOffset = $_GET['page'];
	if(!$pageOffset) $pageOffset = 1;
	
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
	
	function writeEpisodes($url_ug, $max_pages, $pageOffset, $program_id)
	{
		$episodes = wgetEpisodes($url_ug, $max_pages, $pageOffset, $program_id);
		
		echo '<a href="'.$url_ug.'"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
		echo '<a href="../dune/afleveringen.php?'.$_SERVER['QUERY_STRING'].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";

		echo "<table>\n";
		echo "<tr><th>Caption</th><th>Play</th></tr>\n";

		foreach($episodes as $episode)
		{
			$epiid=$episode['epiid'];

			//$amd = getAfleveringMetaDataUrl($epiid, $sessionKey);

			echo '<tr>';
			echo '<td>'.htmlspecialchars($episode['caption']).'</td>';
			echo '<td><a href="../asx.php?epiid='.$epiid.'"><img alt="play" src="img/button-play-icon_32.png"/></a></td>';
			echo '<td><a href="episode.php?epiid='.$epiid.'&programid='.$program_id.'">Meta Data</a></td>';
			echo '<td><a href="../dune/duneplay.php?epiid='.$epiid.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a></td>';
			//echo '<td><a href="'.makeSerieMetaDataUrl($amd['serie_id'], $sessionKey).'">Meta Data Serie</a></td>';
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
    
    if($program_id)
	{
		echo '<h1>'.$program_id."</h1>\n";
		$url_ug = 'http://www.uitzendinggemist.nl/programmas/'.urlencode($program_id);
		
		writeEpisodes($url_ug, $max_pages, $pageOffset, $program_id);

		$pageOffset += $max_pages;
		echo '<a href="?programma='.urlencode($program_id).'&page='.($pageOffset).'">Next Page</a>';
	}
    else if($when)
    {
        if($when=='vandaag')
        {
            echo "<h1>Vandaag</h1>\n";
        }
        else if($when=='gisteren')
        {
            echo "<h1>Gisteren</h1>\n";
        }
        $url_ug = "http://www.uitzendinggemist.nl/weekarchief/$when?display_mode=detail";

		writeEpisodes($url_ug, $max_pages, $pageOffset);

		$pageOffset += $max_pages;

		//echo '<a href="?when='.urlencode($when).'&page='.($pageOffset).'">Next Page</a>';
    }

?>

</body>