#!/usr/bin/env php
<?php
$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';

if( !isset( $argv[ 1 ] ) ) {
	echo "You'll have to provide a full url to the script, example:".PHP_EOL;
	echo "./get_links.php http://www.google.com/".PHP_EOL; 
	exit(1);
}

$target_url =  $argv[ 1 ];

$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$html= curl_exec($ch);
if (!$html) {
	echo "<br />cURL error number:" .curl_errno($ch).PHP_EOL;
	echo "<br />cURL error:" . curl_error($ch).PHP_EOL;
	exit(1);
}

// parse the html into a DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML($html);

// grab all the on the page
$xpath = new DOMXPath($dom);
$hrefs = $xpath->evaluate("/html/body//a");

for ($i = 0; $i < $hrefs->length; $i++) {
	$href = $hrefs->item($i);
	$url = $href->getAttribute('href');
	if( substr( $url, 0, 1 ) == '/' ) {
		$url = substr( $url, 1 );
	}
	if ( !stristr( $url, 'http' ) && !stristr( $url, 'mailto' ) && substr( $url, 0, 1) !== "#" )  { 
		echo $url.PHP_EOL;
	}
}