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
    echo '<p>'.count($players).' Dune media players hebben tot nu toe gebruik gemaakt van deze mirror.</p>'."\n";
	echo "<table class=\"matrix\">\n";
	echo '<tr><th>Dune HD Media Player Serial</th><th>IP Address</th><th>Eerste bezoek</th><th>Laatst bezoek</th><th>Hits</th><th>#Favorieten</th><th>Language</th><th>DuneHD User-Agent</th></tr>'."\n";
	foreach(getPlayers() as $player)
	{
		echo '<tr>';
		echo '<td><pre>'.$player['serial'].'</pre></td>';
		echo '<td align="middle">'.long2ip($player[ip]).'</td>';
		echo '<td align="right">'.$player['firstSeen'].'</td>';
		echo '<td align="right">'.$player['lastSeen'].'</td>';
		echo '<td align="right">'.$player['hits'].'</td>';
		echo '<td align="right"><a href="favorieten.php?serial='.$player['serial'].'">'.$player['favorites'].'</a></td>';
		echo '<td>'.$player['lang'].'</td>';
		echo '<td>'.$player['userAgent'].'</td>';
		echo "</tr>\n";
	}
	
	echo "</table>\n";

	?>
</body>
</html>