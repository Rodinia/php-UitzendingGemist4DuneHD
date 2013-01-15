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
    $doc->loadHtmlFile($filename) || error('Failed to load Favorites XML configuration: $filename');
        
    $result = array();
    
    $num = 0;
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

?>