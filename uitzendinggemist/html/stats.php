<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Favoriete Programma's</title>
	<link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
	<h1>Uitzending Gemist Database Statistieken</h1>
<?php
	require_once '../lib/lib_storage.php';
	
	$players = getPlayers();
	echo "<h2>Media Players</h2>\n";
    echo '<p>Service currently used by '.count($players).' Dune media players.</p>'."\n";
	echo "<table class=\"matrix\">\n";
	echo '<tr><th>Dune HD Media Player Serial</th><th>Eerste bezoek</th><th>Laatst bezoek</th><th>IP Address</th><th>#Favorieten</th><th>User-Agent</th></tr>'."\n";
	foreach(getPlayers() as $player)
	{
		echo '<tr>';
		echo '<td><pre>'.$player['serial'].'</pre></td>';
		echo '<td align="right">'.$player['firstSeen'].'</td>';
		echo '<td align="right">'.$player['lastSeen'].'</td>';
		echo '<td align="right">'.long2ip($player[ip]).'</td>';
		echo '<td align="right">'.$player['favorites'].'</td>';
		echo '<td>'.$player['userAgent'].'</td>';
		echo "</tr>\n";
	}
	
	echo "</table>\n";

	?>
</body>
</html>