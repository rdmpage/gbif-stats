<?php

// CouchDB hit lookup

// Query is tile key [zoom, x, y, countryCode, rx, ry] where x and y are tile numbers, and rx and ry
// are the locations of hit within that tile (i.e., rx and ry range from 0-256 for 256 pixel tiles)

// Return a list of CouchDB documents corresponding to the location on map

require_once (dirname(__FILE__) . '/api_utils.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

$callback = '';

//print_r($_GET);

// If no query parameters 
if (count($_GET) == 0)
{
	//default_display();
	echo 'hi';
	exit(0);
}

if (isset($_GET['callback']))
{	
	$callback = $_GET['callback'];
}


if (isset($_GET['x']))
{
	$x = (Integer)$_GET['x'];
}

if (isset($_GET['y']))
{
	$y = (Integer)$_GET['y'];
}

if (isset($_GET['z']))
{
	$zoom = (Integer)$_GET['z'];
}


if (isset($_GET['countryCode']))
{
	$countryCode = $_GET['countryCode'];
}

if (isset($_GET['rx']))
{
	$rx = (Integer)$_GET['rx'];
}

if (isset($_GET['ry']))
{
	$ry = (Integer)$_GET['ry'];
}



// Find all points in this tile
$startkey = array($zoom, $x, $y, $countryCode, $rx, $ry);
$endkey = array($zoom, $x, $y, $countryCode, $rx, $ry, "zzz");


$url = '_design/country/_view/tile?startkey=' . urlencode(json_encode($startkey))
	. '&endkey=' .  urlencode(json_encode($endkey))
	. '&reduce=false'
	. '&include_docs=true';
	
//echo urldecode($url);exit();

		
if ($config['stale'])
{
	$url .= '&stale=ok';
}	
	
$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$response_obj = json_decode($resp);

$obj = new stdclass;
$obj->status = 200;

$obj->results = array();
$obj->results[] = array('OccurrenceID', 'Scientific name', 'Locality', 'Issues');

foreach ($response_obj->rows as $row)
{
	$summary = array();
	
	$summary[] = '<a href="http://www.gbif.org/occurrence/' . $row->doc->key . '" target="_new">' . $row->doc->key . '</a>';
	$summary[] = $row->doc->scientificName;
	$summary[] = $row->doc->locality;
	$summary[] = join(", ", $row->doc->issues);

	$obj->results[] = $summary;				
}

api_output($obj, $callback);

?>