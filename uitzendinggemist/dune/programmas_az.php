<?php
	require_once '../lib_ugemist.php';
    require_once '../lib/dune.php';
    
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
<?php
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";

	echo "# A-Z list\n";
	
	foreach (wgetProgramPrefixLinks() as $prefix)
	{
        $url = $baseurl.'/programmas.php?suffix='.urlencode($prefix);
		$num = 0;
		writeItem($num++, strtoupper($prefix), 'dune_'.$url);
	}
?>
