<?php
// Provide access to store Media Player information in MySQL database

function readDuneSerialFromHeader()
{
    //return 'FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF';
    
	$headers = apache_request_headers();
    return $headers['X-Dune-Serial-Number'];
}

function lookupDuneSerial()
{
    $duneSerial = readDuneSerialFromHeader();
    if($duneSerial == null) $duneSerial = findSerialByIP();
    return $duneSerial;
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
    $duneSerial = readDuneSerialFromHeader();
    
    if(!$duneSerial) return;
    
    $mysqli = connectToDb();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("REPLACE INTO dunehd_player (duneSerial, ipAddress, lastSeen, lang) VALUES(?, ?, UTC_TIMESTAMP(), ?)") )
    {
        $stmt->bind_param('sis', $duneSerial, ip2long(getRemoteIp()), $duneLang);

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
	$numIp = ip2long(getRemoteIp());
	
	if($numIp > -1062731776 && $numIp < -1062666241) // Private range: 192.168.0.0 .. 192.168.255.255
		$players = getPlayersByRange(-1062731776, -1062666241);
	else if($numIp > 167772160 && $numIp < 184549375) // Private range: 10.0.0.0 ..  10.255.255.255
		$players = getPlayersByRange(167772160, 184549375);
	else if($numIp > -1408237568 && $numIp < -1407188993) // Private range: 172.16.0.0 ..  172.31.255.255
		$players = getPlayersByRange(-1408237568, -1407188993);
	else // Public range
		$players = getPlayersByRange($numIp, $numIp);
	return isset($players[0]) ? $players[0] : null;
}

function getPlayers()
{
	// 128.0.0.0=-2147483648 255.255.255=2147483647
	return getPlayersByRange(-2147483648, 2147483647);
}

function getPlayersByRange($firstIp, $lastIp)
{
    $mysqli = connectToDb();
	$result = array();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT mp.duneSerial, ipAddress, lastSeen, lang, favorites FROM dunehd_player mp LEFT JOIN( SELECT duneSerial, COUNT(*) favorites FROM favorite GROUP BY duneSerial ) fav ON fav.duneSerial=mp.duneSerial WHERE ipAddress>=? AND ipAddress<=? ORDER BY lastSeen") )
    {
        $stmt->bind_param('ii', $firstIp, $lastIp);

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($duneSerial, $ipAddress, $lastSeen, $lang, $favorites);
        
        $player = null;
        
        while($stmt->fetch())
        {
            $player = array();
            $player['serial'] = $duneSerial;
            $player['ip'] = $ipAddress;
            $player['lastSeen'] = $lastSeen;
            $player['lang'] = $lang;
			$player['favorites'] = $favorites;
			$result[] = $player;
        }

        $stmt->close();
        
        // if($result == null) return 'FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF';
        
        return $result;
    }
    else trigger_error('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function getRemoteIp()
{
    return $_SERVER['REMOTE_ADDR'];
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