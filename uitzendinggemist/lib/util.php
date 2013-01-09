<?php
    # Suppress DOM warnings
    libxml_use_internal_errors(true);
    
    function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }
    
    // Work arround for PHP since character encoding does not working properly loading html into DOM
    function loadHtmlAsDom($url)
	{
		$body = file_get_contents($url);
		
		// Insert a head and meta tag immediately after the opening <html> to force UTF-8 encoding
		$insertPoint = false;
		if (preg_match("/<html.*?>/is", $body, $matches, PREG_OFFSET_CAPTURE)) {
			$insertPoint = mb_strlen( $matches[0][0] ) + $matches[0][1];
		}
		if ($insertPoint) {
			$body = mb_substr(
				$body,
				0,
				$insertPoint
			) . "<head><meta http-equiv='Content-type' content='text/html; charset=UTF-8' /></head>" . mb_substr(
				$body,
				$insertPoint
			);
		}
		
		$dom = new DOMDocument;
		$dom->loadHTML($body);
		return $dom;
	}
?>