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
	
	echo "<h2>Media Players</h2>\n";
	echo "<table class=\"matrix\">\n";
	echo '<tr><th>IP Address</th><th>Dune HD Media Player Serial</th><th>Laatst gezien</th></tr>'."\n";

	foreach(getPlayers() as $player)
	{
		echo '<tr><td>'.long2ip($player[ip]).'</td><td>'.$player[serial].'</td><td>'.$player[lastSeen].'</td></tr>'."\n";
	}
	
	echo "</table>\n";

	?>
</body>
</html>