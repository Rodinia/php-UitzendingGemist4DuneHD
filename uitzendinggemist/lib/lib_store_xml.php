<?php
// -------------------- XML Storage Functions ----------------------

function findSerialByIP()
{
    return null;
}

# Suppress DOM warnings
libxml_use_internal_errors(true);

function readFavorites($provider, $type)
{
    $xmlFile = dirname(dirname(__FILE__)).'/favorieten_uitzendinggemist.xml';
    return readXmlFavorites($xmlFile, $provider, $type);
}

function readXmlFavorites($filename, $provider, $type)
{
    $dom = new DOMDocument();
    $dom->load($filename) or user_error('Failed to load Favorites XML configuration: $filename');

    $xpath = new DOMXpath($dom);

    $result = array();
    foreach($xpath->query("/ugconfig/favorites/favorite[provider='$provider' and type='$type']") as $favorite)
    {
        $entry = array();
        $entry['title'] = getElementValue($favorite, 'title');
        $entry['img'] = getElementValue($favorite, 'img');
        $entry['refid'] = getElementValue($favorite, 'refid');

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
    $xmlFile = dirname(dirname(__FILE__)) . '/favorieten_uitzendinggemist.xml';

    $dom = new DOMDocument();
    $dom->load($xmlFile) or error('Failed to load Favorites XML configuration: $filename');
    $dom->formatOutput = true;

    $elemFavorite = $dom->createElement('favorite');
    $elemFavorite->appendChild($dom->createElement('provider', $provider));
    $elemFavorite->appendChild($dom->createElement('type', $type));
    $elemFavorite->appendChild($dom->createElement('refid', $refid));
    $elemFavorite->appendChild($dom->createElement('title', $title));
    if($img) $elemFavorite->appendChild($dom->createElement('img', $img));
    $dom->documentElement->getElementsByTagName('favorites')->item(0)->appendChild($elemFavorite);
    $dom->save($xmlFile);
}

function deleteFromFavorite($provider, $type, $refid)
{
    $xmlFile = dirname(dirname(__FILE__)) . '/favorieten_uitzendinggemist.xml';

    $dom = new DOMDocument();
    $dom->load($xmlFile) or user_error('Failed to load Favorites XML configuration: $filename');
    $dom->formatOutput = true;
    $favorites = $dom->documentElement->getElementsByTagName('favorites')->item(0);
    foreach($favorites->getElementsByTagName('favorite') as $favorite)
    {
        if($refid == getElementValue($favorite, 'refid') && $provider == getElementValue($favorite, 'provider') && $type == getElementValue($favorite, 'type'))
        {
            $favorites->removeChild($favorite);
        }
    }
    $dom->save($xmlFile);
}

function getPlayers()
{
    die("Not supported in XML mode, requires MySQL mode.");
}

?>