<?php
// Provides access to storage of favorites

require_once dirname(__FILE__).'/../config.php';

if($useMySQL)
{
    require_once dirname(__FILE__).'/lib_store_mysql.php'; // MySQL database implementation
}
else
{
    require_once dirname(__FILE__).'/lib_store_xml.php'; // XML storage implementation
}

function addUgAfleveringToFavorite($programId, $title, $img)
{
    addToFavorite('uitzendinggemist', 'programma', $programId, $title, $img);
}

?>