<?php
	#Enable display errors
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

	include_once '../lib_ugemist.php';

	$epiid = $_GET['epiid'];

	$sessionKey = getSessionKey();

	//echo "/public/ug-od/wm/3/media/wm3c2/ceres/1/teleacnot/rest/2012/NPS_1207084/std.20120905.wmv";


	$streamUrl = getStreamUrl($epiid, $sessionKey, 'wmv', 'bb');

	$infoUrl = makeStreamInfoUrl($epiid, $secret);

	$metaDataUrl = makeAfleveringMetaDataUrl($epiid, $sessionKey);

	$metaData = getAfleveringMetaDataUrl($epiid, $sessionKey);
	$streamInfoUrl = makeStreamInfoUrl($epiid, $sessionKey);
	$serieMetaDataUrl = makeSerieMetaDataUrl($metaData['serie_id'], $sessionKey);

	$playerUrl = 'http://player.omroep.nl/?aflID='.$epiid.'&md5='.$hash;

	$prid = $metaData['prid']; // NPS_1207084
	$sko_dt = $metaData['sko_dt']; // 20120905

	$mediaPath = '/public/ug-od/wm/3/media/wm3c2/ceres/1/teleacnot/rest/2012/'.$prid.'/std.'.$sko_dt.'.wmv';

	$streamServerUrl = 'http://cgi.omroep.nl/cgi-bin/streams?'.$mediaPath;

	function makePlayerUrl($epiid, $secret)
	{
		$md5 = episodeHash($epiid, $secret);
		return 'http://player.omroep.nl/xml/metaplayer.xml.php?aflID='.$epiid.'&md5='.$md5;
	}

	$playerUrl = makePlayerUrl($epiid, $secret);

	?><html>
<body>
	<table>
		<tr><td>Episode</td><td><?php print $epiid; ?></td></tr>
		<tr><td>Serie</td><td><?php print $metaData['serie_id']; ?></td></tr>
		<tr><td>sessionKey</td><td><?php print  join('|',$sessionKey); ?></td></tr>
		<tr><td>Meta Data</td><td><?php print "<a href=\"$metaDataUrl\">Meta Data Episode $epiid</a>"; ?></td></tr>
		<tr><td>Stream Info</td><td><?php print "<a href=\"$streamInfoUrl\">Meta Data Stream Episode $epiid</a>"; ?></td></tr>
		<tr><td>Serie Meta Data</td><td><?php print '<a href="'.$serieMetaDataUrl.'">Meta Data Serie '.$metaData['serie_id'].'</a>'; ?></td></tr>
		<tr><td>Meta Data: prid</td><td><?php print $metaData['prid']; ?></td></tr>
		<tr><td>ASX</td><td><?php print "<a href=\"../asx.php?epiid=$epiid\">ASX</a>"; ?></td></tr>
		<tr><td>Dune</td><td><?php print "<a href=\"../dune/duneplay.php?epiid=$epiid\">Dune link</a>"; ?></td></tr>
		<tr><td>Player URL</td><td><?php print "<a href=\"$playerUrl\">Player URL</a>"; ?></td></tr>

	</table>
</body>
</html>