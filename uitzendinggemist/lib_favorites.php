<?php
	// -------------------- Functions ----------------------

    # Suppress DOM warnings
    libxml_use_internal_errors(true);
    
	function readFavorites($filename)
    {
        $doc = new DOMDocument();
        $doc->loadHtmlFile($filename) || error('Failed to load Favorites XML configuration: $filename');
            
        $result = array();
        
        $num = 0;
        foreach($doc->documentElement->getElementsByTagName('programma') as $programma)
        {
            $entry = array();
            $entry['caption'] = getElementValue($programma, 'caption');
            $entry['banner'] = getElementValue($programma, 'banner');
            $entry['id'] = getElementValue($programma, 'id');
            
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