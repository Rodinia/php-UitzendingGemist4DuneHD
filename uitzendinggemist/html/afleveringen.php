<?php
	// Enable display errors
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

	include_once '../lib_ugemist.php';

	$programma = $_GET['programma'];

	$pageOffset = $_GET['page'];

?><!DOCTYPE html>
<html>
<head>
	<title><?php print $programma; ?></title>
    <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	echo '<h1>'.$programma."</h1>\n";
    
    echo '<a href="../dune/afleveringen.php?program='.$programma.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";

	echo "<table>\n";
	echo "<tr><th>caption</th><th>Select</th><th>Play</th></tr>\n";

	$max_pages = 3;

	$episodes = wgetEpisodes($programma, $max_pages, $pageOffset);

	$sessionKey = getSessionKey();



	foreach($episodes as $episode)
	{
		$epiid=$episode['epiid'];

		//$amd = getAfleveringMetaDataUrl($epiid, $sessionKey);

		echo '<tr>';
		echo '<td>'.$episode['caption'].'</td>';
		echo '<td><a href="../asx.php?epiid='.$epiid.'"><img alt="play" src="img/button-play-icon_32.png"/></a></td>';
		echo '<td><a href="episode.php?epiid='.$epiid.'">Info</a></td>';
		echo '<td><a href="'.makeStreamInfoUrl($epiid, $sessionKey).'">Stream Info</a></td>';
		echo '<td><a href="'.makeAfleveringMetaDataUrl($epiid, $sessionKey).'">Meta Data Aflevering</a></td>';
		//echo '<td><a href="'.makeSerieMetaDataUrl($amd['serie_id'], $sessionKey).'">Meta Data Serie</a></td>';
		echo "</tr>\n";
	}
	echo "</table>\n";

	$pageOffset += $max_pages;

	echo '<a href="?programma='.urlencode($programma).'&page='.($pageOffset).'">Next Page</a>';

?>

</body>