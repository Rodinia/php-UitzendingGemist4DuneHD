<?php
// -------------------- XML Storage Functions ----------------------

function findSerialByIP()
{
	return null;
}

# Suppress DOM warnings
libxml_use_internal_errors(true);
    
function readFavorites($provider)
{
    $xmlFile = null;
    switch($provider)
    {
        case 'uitzendinggemist':
           $xmlFile = dirname(dirname(__FILE__)).'/favorieten_uitzendinggemist.xml';
            break;
        case  'vara':
            $xmlFile = dirname(dirname(__FILE__)).'/varagemist/favorieten_vara.xml';
            break;
        default:
            die('Unknown provider');
    }
    return readXmlFavorites($xmlFile);
}
    
function readXmlFavorites($filename)
{
    $doc = new DOMDocument();
    $doc->load($filename) or user_error('Failed to load Favorites XML configuration: $filename');
        
    $result = array();
    foreach($doc->documentElement->getElementsByTagName('programma') as $programma)
    {
        $entry = array();
        $entry['title'] = getElementValue($programma, 'caption');
        $entry['img'] = getElementValue($programma, 'banner');
        $entry['refid'] = getElementValue($programma, 'id');
        
        $result[] = $entry;
    }
    return $result;
}
        
function getElementValue($parent, $tagname)
{
    $item = $parent->getElementsByTagName($tagname)->item(0);
    return $item instanceof DOMElement ? $item->nodeValue : null; 
}

// function addToFavorite($provider, $type, $refid, $title, $img = null)
function addToFavorite($provider, $type, $refid, $title, $img = null)
{
	$xmlFile = dirname(dirname(__FILE__)).'/favorieten_uitzendinggemist.xml';
	
	$dom = new DOMDocument();
    $dom->load($xmlFile) or error('Failed to load Favorites XML configuration: $filename');
	$dom->formatOutput = true;
	
	$elemType = $dom->createElement($type);
	$elemType->appendChild( $dom->createElement('provider', $provider) );
	$elemType->appendChild( $dom->createElement('type', $type) );
	$elemType->appendChild( $dom->createElement('id', $refid) );
	$elemType->appendChild( $dom->createElement('caption', $title) );
	if($img)
		$elemType->appendChild( $dom->createElement('banner', $img) );
	$dom->documentElement->appendChild($elemType);
	$dom->save($xmlFile);
}

function deleteFromFavorite($provider, $refid)
{
    $xmlFile = dirname(dirname(__FILE__)).'/favorieten_uitzendinggemist.xml';
    
    $dom = new DOMDocument();
    $dom->load($xmlFile) or user_error('Failed to load Favorites XML configuration: $filename');
    $dom->formatOutput = true;    
    foreach($dom->documentElement->getElementsByTagName('programma') as $programma)
    {
        if($refid == getElementValue($programma, 'id') && $provider == getElementValue($programma, 'provider'))
        {
            $dom->documentElement->removeChild($programma);
        }
    }
    $dom->save($xmlFile);
}

function getPlayers()
{
    die("Not supported in XML mode, requires MySQL mode.");
}

?>