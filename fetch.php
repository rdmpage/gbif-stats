<?php

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/lib.php');

if (0)
{
	// GBIF defaults
	define('API_RECORDS_LIMIT', 1000000);
	define('API_PAGE_LIMIT', 300);
}
else
{
	// debugging
	define('API_RECORDS_LIMIT', 100000); // Solomon Islands has 89,364 records http://www.gbif.org/country/SB
	define('API_PAGE_LIMIT', 300);
}

// Construct search URL
$base_url = 'http://api.gbif.org/v0.9/occurrence/search?';

$parameters = array(
	'limit' => API_PAGE_LIMIT,
	'offset' => 0	
	);

// Country search
$country = 'SB'; // Solomon Islands
$parameters['country'] = $country;

// Do search
$endOfRecords 	= false;
$count 			= 0;


while (!$endOfRecords && ($count < API_RECORDS_LIMIT))
{
	$url = $base_url . http_build_query($parameters);

	$json = get($url);

	$obj = json_decode($json);
	
	$count += count($obj->results);
	$endOfRecords = $obj->endOfRecords;
	
	$parameters['offset'] += $parameters['limit'];
	
	// CouchDB
	$docs = new stdclass;
	$docs->docs = array();


	foreach ($obj->results as $occurrence)
	{
		
		// id
		$occurrence->_id = 'occurrence/' . $occurrence->key;
		$docs->docs[] = $occurrence;
		
		//print_r($occurrence);
		
		echo "CouchDB...\n";
		//$couch->add_update_or_delete_document($occurrence, $occurrence->_id);
	}
	
	//$docs->new_edits = false;
	
	//print_r($docs);
	
	echo "CouchDB...";
	$resp = $couch->send("POST", "/" . $config['couchdb_options']['database'] . '/_bulk_docs', json_encode($docs));
	//var_dump($resp);
	echo "\n";
	
	
	
}
?>