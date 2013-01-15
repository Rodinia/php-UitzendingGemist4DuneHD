<html>
<head>
  <title>Programma's A-Z</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	require_once '../lib/lib_ugemist.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
	$cols = 8;
	$nr = 0;
	echo "<h1>Programma's A-Z</h1>\n";
	
	echo '<a href="http://www.uitzendinggemist.nl/programmas"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
	echo '<a href="../dune/programmas_az.php"><img src="img/dune_hd_logo.png" alt="Dune HD" title="Toon Dune HD meta data (DUNE_FOLDER.TXT MECHANISM)"/></a>'."\n";
	
	
	echo "<table class=\"touch\">\n";
	echo "<tr>\n";
	#
	foreach (wgetProgramPrefixLinks() as $prefix)
	{
		if($nr++%$cols == 0)
		{
			if($nr>1)
				echo "</tr>\n";
			echo "<tr>\n";
		}
		echo '<td><a href="programmas.php?suffix='.urlencode($prefix).'">'.strtoupper($prefix)."</a></td>\n";
	}
	echo "</tr>\n";
	echo "</table>\n";
?>

</body>