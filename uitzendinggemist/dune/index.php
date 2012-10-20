use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
num_rows = 3
async_icon_loading = yes

<?php
	header('Content-type: text/plain');

	$num = 0;

	$dom = new DOMDocument();
	$xml = $dom->loadHtmlFile('../ugconfig.xml');
	if(!xml)
		error('Failed to load Uitzending Gemist XML configuration: ugconfig.xml');

	$num = 0;
	foreach($dom->documentElement->getElementsByTagName('programma') as $programma)
	{
		$caption = getSingleElement($programma, 'caption')->nodeValue;
		$url_icon = getSingleElement($programma, 'banner')->nodeValue;
		$id = getSingleElement($programma, 'id')->nodeValue;

		writeProgramma($num++, $caption, $url_icon, $id);
	}

	function getSingleElement($parent, $name)
	{
		foreach($parent->getElementsByTagName($name) as $element)
			return $element;
		return 0;
	}

	function writeProgramma($num, $caption, $url_icon, $aflevering_key)
	{
		$duneUrl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'afleveringen.php?program='.urlencode($aflevering_key)."\n";

		echo "item.$num.icon_path = $url_icon\n";
		echo "item.$num.scale_factor = 1\n";
		echo "item.$num.caption = $caption\n";
		echo "item.$num.media_url = dune_$duneUrl\n";
	}

?>