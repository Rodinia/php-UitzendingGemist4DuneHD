<?php
	include_once '../lib_ugemist.php';
    include_once 'dune.php';
    
    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

    header('Content-type: text/plain; charset=utf-8');

    $suffix = $_GET['suffix'];

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    
    $num = 0;

?>use_icon_view = yes
num_cols = 9
num_rows = 4
async_icon_loading = yes
background_order=before_all
<?php
	echo "background_path=$baseurl/dune-wide.jpg\n";

	echo "# A-Z list\n";
	
	foreach (wgetProgramPrefixLinks() as $prefix)
	{
		$url = $baseurl.'/programmas.php?suffix='.urlencode($prefix);
		writeItem($num++, strtoupper($prefix), 'dune_'.$url);
	}
?>
