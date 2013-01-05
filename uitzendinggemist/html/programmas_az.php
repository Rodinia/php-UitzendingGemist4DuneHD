<html>
<head>
  <title>Programma's A-Z</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	include_once '../lib_ugemist.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
	$cols = 8;
	$nr = 0;
	echo "<h1>Programma's A-Z</h1>\n";
	
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