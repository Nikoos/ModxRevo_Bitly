<?php
/* Retrieve properties from DB */


$Bitly_KEY = $modx->getOption('Bitly_KEY', $scriptProperties, '');
$Bitly_LOGIN = $modx->getOption('Bitly_LOGIN', $scriptProperties, '');
$siteURL = $modx->getOption('site_url');
$docID = $modx->resource->get('id');
$docInfo = $modx->getObject('modResource',$docID);
$docname = $docInfo->get('alias');
$contentType = $docInfo->getOne('ContentType');
$description = $contentType->get('description');
$extension = $contentType->get('file_extensions');

/* Check if it is in folder */
$parent = $docInfo->get('parent');
$path='';

/* Checks if everything is correct */

if (empty($Bitly_KEY)){
$output="-No Bit.ly key, please create an account @ bit.ly website";
return $output;
die();}
else{
$output=$Bitly_KEY;
}

if (empty($Bitly_LOGIN)){
$output="-No Bit.ly key, please create an account @ bit.ly website";
return $output;
die();}
else{
$output=$Bitly_LOGIN;
}

while ($parent != 0) {
$docInfo = $modx->getObject('modResource',$parent);
$alias = $docInfo->get('alias');
$path = $alias . '/' . $path;
$parent = $docInfo->get('parent');
}
$longurl = $siteURL . $path . $docname . $extension;
$bitly = 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.urlencode($longurl).'&login='.$Bitly_LOGIN.'&apiKey='.$Bitly_KEY.'&format=xml';

$response = file_get_contents($bitly);
//credits to davidwalsh.name
//parse depending on desired format
if(strtolower('xml') == 'json')
  {
    $json = @json_decode($response,true);
    return $json['results'][$longurl]['shortUrl'];
  }
  else //xml
  {
    $xml = simplexml_load_string($response);
    return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
  }
