<?php
    
    #Enable display errors
	error_reporting(E_WARNING);
    require_once '../lib/lib_storage.php';
	
	$serial = "";
	
	if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
		echo "<p>POST</p>\n";
		
		$serial = $_POST['serial'];
		
		if( isRegistered($serial) )
		{
			//echo "<p>$serial Gevonden</p>\n";
			registerBrowser($serial);
			header('Location: ?error=0'); 
		}
		else
		{
			header('Location: ?error=1'); 
		}
    }
    
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Registreer</title>
	<link href="application.css" media="screen" rel="stylesheet" type="text/css" />

</head>
<body>

	<form name="favorite" method="post">
        <input type="hidden" name="do"/>
        <input type="hidden" name="provider" value="uitzendinggemist"/>
        <input type="hidden" name="type" value="programma"/>
        <input type="hidden" name="refid"/>
    </form>
	
    <script type="text/javascript">
    function removeFromFavorite(refid)
    {
        var form = document.favorite;
        form.do.value = 'delete';
        form.refid.value = refid;
        form.submit();
    }
    
    function save(programId)
    {
        var form = document.favorite;
        form.do.value = 'save';
        form.programid.value = programId;
        form.submit();
    }
    </script>
    <div id="header"><h1>Registreer</h1></div>
	<a href="../dune/favorites.php"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
<p>
Op deze pagina kun je een koppeling tussen maken je browser waarmee je de favorieten beheert en jouw Dune HD waarmee je je favorieten op afspeelt.
</p>
<p>Dit pas mogelijk nadat je Dune HD Media player minstens één maal verbonden is geweest met deze Uitzending Gemist App.</p>

<?php 
	function getMessage($error)
	{
		switch($error)
		{
			case 0: return 'Koppeling tussen browser en Dune HD gemaakt.';
			case 1: return 'Opgegeven Dune HD serial niet gevonden, maak eerst verbinding met uw Dune HD media speler.';
		}
		return '????';
	}
	
	
	if( isset($_GET['error'] ) )
	{
		$error = $_GET['error'];
		echo "<div class=\"".($error == 0 ? 'success' : 'error')."\">\n";
		echo '<p>'.getMessage($error)."</p>\n";
		echo "</div>\n";
	}
?>

<form method="post">
	<table>
		<tr><td>Jouw IP adres:</td><td><?php echo getRemoteIp(); ?></td></tr>
		<tr><td>Dune HD Serie nummer:</td><td><input name="serial" type="text" size="39" value="<?php echo $serial; ?>"></input></td></tr>
		<tr><td colspan="2"><button type="submit">Maak koppeling</button></td></tr>
	<table>
</form>

</body>
</html>