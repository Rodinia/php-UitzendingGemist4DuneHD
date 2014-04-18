<!DOCTYPE html>
  <html>
<?php
	#Enable display errors
	error_reporting(E_WARNING);
	//error_reporting(E_ALL);
	//header('Content-type: text/plain; charset=utf-8');

	include_once '../lib/lib_ugemist.php';

	//$epiid = $_GET['epiid'];
	$program_id = $_GET['programid'];
	$localepiid = $_GET['localepiid'];
	
	$ed = wgetEpisodeData($localepiid);
	//echo "# epiid=$epiid\n";

	$sessionKey = getSessionKey();

	$streamUrl = getStreams($ed);

	//$playerUrl = 'http://player.omroep.nl/?aflID='.$epiid.'&md5='.$hash;

	$prid = $metaData['prid']; // NPS_1207084
	$sko_dt = $metaData['sko_dt']; // 20120905

	$mediaPath = '/public/ug-od/wm/3/media/wm3c2/ceres/1/teleacnot/rest/2012/'.$prid.'/std.'.$sko_dt.'.wmv';

	$streamServerUrl = 'http://cgi.omroep.nl/cgi-bin/streams?'.$mediaPath;

	?>
	<head>
		<title>Meta Data</title>
		<link href="application.css" media="screen" rel="stylesheet" type="text/css" />
	</head>
<body>
	<table>
	<tr><th colspan="2">Episode data:</th></tr>
<?
foreach($ed as $name => $value)
{        
    if($name == "id") continue;
	echo '<tr><td>'.$name.'</td><td>'.$value."</td></tr>\n";
}

if($ed['data-episode-id']){ 

	$sessionKey = getSessionKey();

	$infoUrl = makeStreamInfoUrl($epiid, $sessionKey);

	$metaDataUrl = makeAfleveringMetaDataUrl($epiid, $sessionKey);

	$metaData = getAfleveringMetaDataUrl($epiid, $sessionKey);
	$streamInfoUrl = makeStreamInfoUrl($epiid, $sessionKey);
	$playlistSerieMetaDataUrl = makePlaylistSerieMetaDataUrl($metaData['serie_id'], $sessionKey);

	function makePlayerUrl($epiid, $sessionKey)
	{
		$md5 = episodeHash($epiid, $sessionKey);
		return 'http://player.omroep.nl/xml/metaplayer.xml.php?aflID='.$epiid.'&md5='.$md5;
	}

	$playerUrl = makePlayerUrl($epiid, null);

?>
		<tr><th colspan="2">Based on episode ID:</th></tr>
		<tr><td width="20%">sessionKey</td><td><?php print  join('|',$sessionKey); ?></td></tr>
		<tr><td>Stream Info</td><td><a href="<?php print makeStreamInfoUrl($epiid, $sessionKey); ?>">Stream Info</a></td>
		<tr><td>Meta Data</td><td><a href="<?php print $metaDataUrl; ?>">Meta Data Episode <?php print $epiid ?></a></td></tr>
		<tr><td>Stream Info</td><td><a href="<?php print $streamInfoUrl; ?>">Meta Data Stream Episode <?php print $epiid ?></a></td></tr>
		<tr><td>Meta Data: prid</td><td><?php print $metaData['prid']; ?></td></tr>
		<tr><td>ASX</td><td><a href="<?php print "../ug_stream.php?type=asx&epiid=$epiid"; ?>">ASX</a></td></tr>
		<tr><td>Player URL</td><td><a href="<?php print $playerUrl; ?>">Player URL</a></td></tr>
		<tr><td>Programma ID</td><td><?php print $program_id; ?></td></tr>
		<tr><td>Serie ID</td><td><?php print $metaData['serie_id']; ?></td></tr>
		<tr><td>Playlist Serie Meta Data</td><td><?php print '<a href="'.$playlistSerieMetaDataUrl.'">Meta Data Serie '.$metaData['serie_id'].'</a>'; ?></td></tr>
		<tr><td>Uitzending Gemist URL</td><td><a href="<?php print "http://www.uitzendinggemist.nl/programmas/$program_id"; ?>">Programma op Uitzending Gemist</a></td></tr>
<? }; 

echo "		<tr><th colspan=\"2\">Streams:</th></tr>\n";

foreach($streamUrl['streams'] as $index => $stream)
{        
    echo "<tr><td>Stream info $index</td><td><a href=\"$stream\">$stream</td></tr>\n";
}

?>
		<tr><th colspan="2">Dune:</th></tr>
		<tr><td>Dune</td><td><a href="<?php print "../ug_stream.php?type=dune&localepiid=$localepiid"; ?>">Dune link</a></td></tr>
		<tr><td>Uitzending Gemist URL</td><td><a href="<?php print "http://www.uitzendinggemist.nl/afleveringen/$localepiid"; ?>">Aflevering op Uitzending Gemist</a></td></tr>
	</table>
</body>
</html>