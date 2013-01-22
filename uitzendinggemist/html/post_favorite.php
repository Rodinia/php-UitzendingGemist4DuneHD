<?php
	error_reporting(E_ALL);
    
    $message = "?";
    
    if(isset($_POST['do']))
    {
		$do = $_POST['do'];
		$message = "Do: $do";
        require_once dirname(__FILE__).'/../lib/lib_storage.php';
        
		//if( !isset($_POST['provider'])) die 'argument provider missing';
		$provider = $_POST['provider'];
        
		$type = $_POST['type'];
        
		$refid = $_POST['refid'];
		
        if($do == 'add')
		{
			$title = $_POST['title'];
			$img = $_POST['img'];
        
			//addUgAfleveringToFavorite($programId, $title, $img);
			addToFavorite($provider, $type, $refid, $title, $img);
			$message = "Added $provider/$type/$refid to favorites.";
		}
		else if($do == 'delete')
		{
			if($do == 'delete')
			{
				deleteFromFavorite($provider, $type, $refid);
			}
			else if($do == 'save')
			{
				echo "do=$do\n";
			}
			$message = "Deleted: $provider/$type/$refid.";
			header( 'Location: '.$_POST['URL'] ) ;
		}
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>Saving Data</title>
</head>
<body>
<form name="favorite" method="post" target="_blank" action="save.php">
    <input type="hidden" name="programid"/>
</form>
    <h1><?php echo $message; ?></h1>
    <input type="button" value="Close" onclick="window.close();">
    <script type="text/javascript">
        window.close();
    </script> 
</body>