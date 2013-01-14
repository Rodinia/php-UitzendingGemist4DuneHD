<?php
	error_reporting(E_ALL);
    
    $message = "Waiting for form data...";
    
    if(isset($_POST['programid']))
    {
        require_once dirname(__FILE__).'/../lib/lib_storage.php';
        $programId = $_POST['programid'];
        $title = $_POST['title'];
        $img = $_POST['img'];
        addUgAfleveringToFavorite($programId, $title, $img);
        $message = "Added $programId to favorites.";
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