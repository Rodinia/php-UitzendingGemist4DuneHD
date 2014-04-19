<?php
// Provide access to store Media Player information in MySQL database

require_once dirname(__FILE__).'/dune.php';

function lookupDuneSerial()
{
    $duneSerial = getDuneSerial();
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
    $duneSerial = getDuneSerial();
    
    if(!$duneSerial) return;
	
	$mysqli = connectToDb();
	if( isRegistered($duneSerial) )
	{
		$stmt = $mysqli->prepare("UPDATE dunehd_player SET ipAddress=?, lastSeen=UTC_TIMESTAMP(), lang=?, userAgent=?, hits=hits+1 WHERE duneSerial=?");
		if(!$stmt) throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
		$stmt->bind_param('ssss', getRemoteIp(), getDuneLang(), $_SERVER['HTTP_USER_AGENT'], $duneSerial);
	}
	else
	{
		$stmt = $mysqli->prepare("INSERT INTO dunehd_player (duneSerial, ipAddress, firstSeen, lastSeen, lang, userAgent) VALUES(?, ?, UTC_TIMESTAMP(), firstSeen, ?, ?)");
        $stmt->bind_param('ssss', $duneSerial, getRemoteIp(), getDuneLang(), $_SERVER['HTTP_USER_AGENT']);
		if(!stmt) throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);

    }
	/* execute query */
	$stmt->execute();
	$stmt->close();
    $mysqli->close();

    // initialize favorites
}

function registerBrowser($duneSerial, $remoteIp = null)
{
    if(!$duneSerial) throw Exception("Cannot register browser without DuneHD serial");
	
	if($remoteIp == null)
	{
		$remoteIp = getRemoteIp();
	}
	
	echo "<p>serial=$duneSerial, ip=$remoteIp</p>\n";
        
	$mysqli = connectToDb();
	$stmt = $mysqli->prepare("INSERT INTO browser(remoteIp, duneSerial, firstSeen, lastSeen) VALUES(?, ?, UTC_TIMESTAMP(), UTC_TIMESTAMP())");
	$stmt->bind_param('ss', $remoteIp, $duneSerial);
	if(!stmt) throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
	
	/* execute query */
	$stmt->execute();
	$stmt->close();
    $mysqli->close();

    // initialize favorites
}

function isRegistered($duneSerial)
{
    if(!$duneSerial) return false;
    
    $mysqli = connectToDb();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT ipAddress FROM dunehd_player WHERE duneSerial=?") )
    {
        $stmt->bind_param('s', $duneSerial);
        /* execute query */
        $stmt->execute();
		$found = $stmt->fetch();
        $stmt->close();
		return $found;
    }
    else die('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function findSerialByIP()
{
    $player = getPlayerByIP();
	if(isset($player['serial']))
	{
		return $player['serial'];
	}
	return getSerialByBrowser();
}
    
function getPlayerByIP($remoteIp = null)
{
    if($remoteIp == null)
	{
		$remoteIp = getenv('REMOTE_ADDR');
	}

    $mysqli = connectToDb();
	$result = array();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT mp.duneSerial, ipAddress, firstSeen, lastSeen, hits, lang, favorites, userAgent FROM dunehd_player mp LEFT JOIN( SELECT duneSerial, COUNT(*) favorites FROM favorite GROUP BY duneSerial ) fav ON fav.duneSerial=mp.duneSerial WHERE ipAddress=? ORDER BY lastSeen DESC") )
    {
        $stmt->bind_param('s', $remoteIp);

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($duneSerial, $ipAddress, $firstSeen, $lastSeen, $hits, $lang, $favorites, $userAgent);
        
        $player = null;
        
        while($stmt->fetch())
        {
            $player = array();
            $player['serial'] = $duneSerial;
            $player['ip'] = $ipAddress;
            $player['firstSeen'] = $firstSeen;
			$player['lastSeen'] = $lastSeen;
			$player['hits'] = $hits;
            $player['lang'] = $lang;
			$player['favorites'] = $favorites;
			$player['userAgent'] = $userAgent;
			$result[] = $player;
        }

        $stmt->close();
        
        // if($result == null) return 'FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF-FFFF';
        
        return $result;
    }
    else throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function getPlayers()
{
    $mysqli = connectToDb();
	$result = array();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT mp.duneSerial, ipAddress, firstSeen, lastSeen, hits, lang, favorites, userAgent FROM dunehd_player mp LEFT JOIN( SELECT duneSerial, COUNT(*) favorites FROM favorite GROUP BY duneSerial ) fav ON fav.duneSerial=mp.duneSerial ORDER BY lastSeen DESC") )
    {
        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($duneSerial, $ipAddress, $firstSeen, $lastSeen, $hits, $lang, $favorites, $userAgent);
        
        $player = null;
        
        while($stmt->fetch())
        {
            $player = array();
            $player['serial'] = $duneSerial;
            $player['ip'] = $ipAddress;
            $player['firstSeen'] = $firstSeen;
			$player['lastSeen'] = $lastSeen;
			$player['hits'] = $hits;
            $player['lang'] = $lang;
			$player['favorites'] = $favorites;
			$player['userAgent'] = $userAgent;
			$result[] = $player;
        }

        $stmt->close();
        
        return $result;
    }
    else throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function getSerialByBrowser($remoteIp = null)
{
    if($remoteIp == null)
	{
		$remoteIp = getenv('REMOTE_ADDR');
	}

    $mysqli = connectToDb();
	$result = array();
    
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT duneSerial FROM browser WHERE remoteIp=?") )
    {
        $stmt->bind_param('s', $remoteIp);

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($duneSerial);
        
        $player = null;
        
        while($stmt->fetch())
        {
            return $duneSerial;
        }

        $stmt->close();
        
        return null;
    }
    else throw Exception('Prepare statement error (' . $mysqli->errno . ') '. $mysqli->error);
    
    $mysqli->close();
}

function getRemoteIp()
{
	return getenv('REMOTE_ADDR');
}

function ip2long32($ipstr)
{
    $ip = ip2long($ipstr);
    if (PHP_INT_SIZE == 8)
    {
        if ($ip>0x7FFFFFFF)
        {
            $ip-=0x100000000;
        }
    }
    return $ip;
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
    
function deleteFromFavorite($provider, $type, $refid)
{
    $duneSerial = lookupDuneSerial() or die('Dune HD serial number cannot be null');
    
    $mysqli = connectToDb();
 
   /* create a prepared statement */
    if( $stmt = $mysqli->prepare("DELETE FROM favorite WHERE duneSerial=? AND provider=? AND type=? AND refid=?") )
    {
        $stmt->bind_param('ssss', $duneSerial, $provider, $type, $refid);

        /* execute query */
        $stmt->execute() or die('# Query Error (' . $mysqli->errno . ') '.  $mysqli->error);

        $stmt->close();
    }
    else trigger_error('# Prepare statement error (' .  $mysqli->errno . ') '.  $mysqli->error);
    
    $mysqli->close();
}
    
function readFavorites($provider, $type, $duneSerial = false)
{
    $mysqli = connectToDb();
    
    require_once dirname(__FILE__).'/dune.php';
    
    if(!$duneSerial) $duneSerial = lookupDuneSerial();
    if(!$duneSerial) die('Failed to resolve Dune HD Serial');
  
    $favorites = array();
        
    /* create a prepared statement */
    if( $stmt = $mysqli->prepare("SELECT provider, refid, title, img FROM favorite WHERE duneSerial=? AND provider=? AND type=? ORDER by title") )
    {
        $stmt->bind_param('sss', $duneSerial, $provider, $type);

        /* execute query */
        $stmt->execute();
        
        $stmt->bind_result($provider, $refid, $title, $img);
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