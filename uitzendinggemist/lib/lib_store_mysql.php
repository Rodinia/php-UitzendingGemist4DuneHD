<?php
// Provide access to store Media Player information in MySQL database

function lookupDuneSerial()
{
    $headers = apache_request_headers();
    $serial = $headers['X-Dune-Serial-Number'];
    if($serial == null) $serial = findSerialByIP();
    return $serial;
}

function connectToDb()
{
    global $mysql_host, $mysql_user, $mysql_password, $mysql_database;
    // we connect to example.com and port 3307
    $mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
    if ($mysqli->connect_error)
        die('# Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
 
    return $mysqli;
}

function registerMediaPlayer()
{
    $headers = apache_request_headers();
    $duneSerial = $headers['X-Dune-Serial-Number'];
    //$duneSerial = 'FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF';
    
    if(!$duneSerial) return;
    
    $mysqli = connectToDb();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("REPLACE INTO dunehd_player (duneSerial, ipAddress, lastSeen, lang) VALUES(?, ?, NOW(), ?)") )
    {
        $stmt->bind_param('sis', $duneSerial, getRemoteIpAsInt(), $duneLang);

        /* execute query */
        $stmt->execute();

        $stmt->close();
    }
    else die('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function findSerialByIP()
{
    $player = getPlayerByIP();
    return $player['serial'];
}
    
function getPlayerByIP()
{
    $mysqli = connectToDb();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT duneSerial, lastSeen, lang FROM dunehd_player WHERE ipAddress=?") )
    {
        $stmt->bind_param('i', getRemoteIpAsInt());

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($duneSerial, $lastSeen, $lang);
        
        $result = null;
        
        if($stmt->fetch())
        {
            $result = array();
            $result['serial'] = $duneSerial;
            $result['lastSeen'] = $lastSeen;
            $result['lang'] = $lang;
        }

        $stmt->close();
        
        // if($result == null) return 'FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF';
        
        return $result;
    }
    else trigger_error('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function getRemoteIpAsInt()
{
    return ip2long($_SERVER['REMOTE_ADDR']);
}

// ---- Provides access to storage of favorites in MySQL database ---
   
function addToFavorite($provider, $type, $refid, $title, $img = null)
{
    $duneSerial = lookupDuneSerial();
    if( $duneSerial == null) die('addToFavorite(): Dune HD serial number cannot be null');
    
    $mysqli = connectToDb();
    
   /* create a prepared statement */
    if( $stmt = $mysqli->prepare("REPLACE INTO favorite (duneSerial, provider, type, refid, title, img) VALUES(?, ?, ?, ?, ?, ?)") )
    {
        $stmt->bind_param('ssssss', $duneSerial, $provider, $type, $refid, $title, $img);

        /* execute query */
        $stmt->execute() or user_error('# Query Error (' . $mysqli->errno . ') '.  $mysqli->error);

        $stmt->close();
    }
    else trigger_error('Prepare statement error (' .  $mysqli->errno . ') '.  $mysqli->error);
    
    $mysqli->close();
}
    
function deleteFromFavorite($provider, $refid)
{
    $duneSerial = lookupDuneSerial() or die('Dune HD serial number cannot be null');
    
    $mysqli = connectToDb();
 
   /* create a prepared statement */
    if( $stmt = $mysqli->prepare("DELETE FROM favorite WHERE duneSerial=? AND provider=? AND refid=?") )
    {
        $stmt->bind_param('sss', $duneSerial, $provider, $refid);

        /* execute query */
        $stmt->execute() or die('# Query Error (' . $mysqli->errno . ') '.  $mysqli->error);

        $stmt->close();
    }
    else trigger_error('# Prepare statement error (' .  $mysqli->errno . ') '.  $mysqli->error);
    
    $mysqli->close();
}
    
function readFavorites($provider)
{
    $mysqli = connectToDb();
    
    require_once dirname(__FILE__).'/dune.php';
    
    $duneSerial = lookupDuneSerial();
    if($duneSerial == null) die('Failed to resolve Dune HD Serial');
  
    $favorites = array();
        
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT provider, type, refid, title, img FROM favorite WHERE duneSerial=? AND provider=? ORDER by title") )
    {
        $stmt->bind_param('ss', $duneSerial, $provider);

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($provider, $type, $refid, $title, $img);
        while($stmt->fetch())
        {
            $favorite['type'] = $type;
            $favorite['refid'] = $refid;
            $favorite['title'] = $title;
            $favorite['img'] = $img;
            
            $favorites[] = $favorite;
        }

        $stmt->close();
        
    }
    else trigger_error('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);

    $mysqli->close();
    
    return $favorites;
} 
?>