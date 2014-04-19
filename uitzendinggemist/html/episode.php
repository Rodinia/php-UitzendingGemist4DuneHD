<!DOCTYPE html>
  <html>
<?php
	#Enable display errors
	error_reporting(E_WARNING);
	//error_reporting(E_ALL);
	//header('Content-type: text/plain; charset=utf-8');

	include_once '../lib/lib_ugemist.php';

	$program_id = $_GET['programid'];
	$localepiid = $_GET['localepiid'];
	
	$token = getPlayerToken();
	$ed = wgetEpisodeData($localepiid, $token);
    //print_r($ed);

	$streamData = getStreams($ed);

	function printKeyValueRow($key, $value)
	{
		echo "<tr><td>$key</td><td>$value</tr>\n";
	}

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

if($ed['data-player-id'])
{
?>
		<tr><th colspan="2">Based on player ID:</th></tr>
<?php		
	$playerid = $ed['data-player-id'];

	printKeyValueRow('Token', $token);

	$streamInfoUrl = makePlayerStreamInfoUrl($playerid, $token, $time);
	printKeyValueRow('Stream info URL', "<a href=\"$streamInfoUrl\">$streamInfoUrl</a>");
	
	foreach($streamData['streams'] as $index => $streamInfoUrl)
	{        
		$streamInfoUrl = str_replace('type=jsonp&callback=?', 'json', $streamInfoUrl); // enforce pure JSON
		
		printKeyValueRow("Stream #$index", "<a href=\"$streamInfoUrl.\">$streamInfoUrl</a>");
        $json = getJson($streamInfoUrl);
		$streamVideoUrl = $json['url'];
		printKeyValueRow("Stream #$index video", "<a href=\"$streamVideoUrl\">$streamVideoUrl</a>");
		
		$m3u8_url = get_M3U8_url($playerid, $token);
		printKeyValueRow("Stream #$index M3U8", "<a href=\"$m3u8_url\">$m3u8_url</a>");
	}

} 

if($ed['data-episode-id']){ 

	$sessionKey = getSessionKey();

	$infoUrl = makeStreamInfoUrl($epiid, $sessionKey);

	$metaDataUrl = makeAfleveringMetaDataUrl($epiid, $sessionKey);

	$metaData = getAfleveringMetaDataUrl($epiid, $sessionKey);
	$streamInfoUrl = makeStreamInfoUrl($epiid, $sessionKey);
	$playlistSerieMetaDataUrl = makePlaylistSerieMetaDataUrl($metaData['serie_id'], $sessionKey);

	$sessionKey = getSessionKey();


	//$playerUrl = 'http://player.omroep.nl/?aflID='.$epiid.'&md5='.$hash;

	$prid = $metaData['prid']; // NPS_1207084
	$sko_dt = $metaData['sko_dt']; // 20120905

	$mediaPath = '/public/ug-od/wm/3/media/wm3c2/ceres/1/teleacnot/rest/2012/'.$prid.'/std.'.$sko_dt.'.wmv';

	$streamServerUrl = 'http://cgi.omroep.nl/cgi-bin/streams?'.$mediaPath;

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


?>
		<tr><th colspan="2">Dune:</th></tr>
		<tr><td>Dune</td><td><a href="<?php print "../ug_stream.php?type=dune&localepiid=$localepiid"; ?>">Dune link</a></td></tr>
		<tr><td>Uitzending Gemist URL</td><td><a href="<?php print "http://www.uitzendinggemist.nl/afleveringen/$localepiid"; ?>">Aflevering op Uitzending Gemist</a></td></tr>
	</table>
</body>
</html>