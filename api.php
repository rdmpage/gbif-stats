<?php

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/api_utils.php');


//--------------------------------------------------------------------------------------------------
function country_species($country, $key, $callback = '')
{
	global $config;
	global $couch;
			
	$startkey = json_decode($key);
	array_unshift($startkey, $country);
	
	$endkey = $startkey;
	$endkey[] = "zzz";
		
	$url = '_design/country/_view/species?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=10';
		
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	
	//echo urldecode($url);
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->counts = array();
			
			$obj->min_decade = 2020;
			$obj->max_decade = 0;
			
			$obj->results = array();
			$obj->results[] = array('EOL image','Species', 'Occurrences');
			
			foreach ($response_obj->rows as $row)
			{
				// If a taxon is missing an ancestor at one or more ranks then it will 
				// have diefrent indices for the name and taxonKey fields, so we compute these
				// individually
				$n = count($row->key);
				
				// Thumbnail (from EOL)
				$summary = array('<img src="gbif_image.php?id=' . $row->key[$n - 1] . '" width="48" />');
				
				// Species name 				
				$summary[] = '<a href="http://www.gbif.org/species/' . $row->key[$n - 1] . '" target="_new">' . $row->key[$n - 2] . '</a>';
				
				// Occurrence count
				$summary[] = $row->value;

				$obj->results[] = $summary;				
			}
					
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_publisher_decade($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/publishingOrgKey_decade?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=4';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->counts = array();
			
			$obj->min_decade = 2020;
			$obj->max_decade = 0;
			
			$obj->institutions = array();
		
			foreach ($response_obj->rows as $row)
			{
				if (!isset($obj->counts[$row->key[1]]))
				{
					$obj->counts[$row->key[1]] = array();
					$obj->institutions[$row->key[1]] = $row->key[2];
				}
				$obj->counts[$row->key[1]][$row->key[3]] = $row->value;
				
				$obj->min_decade = min($obj->min_decade, $row->key[3]);
				$obj->max_decade = max($obj->max_decade, $row->key[3]);
			}
			
			$obj->results = array();
			$obj->results[] = array('Publisher','Collections', 'Total');
			
			foreach ($obj->counts as $k => $v)
			{
				$summary = array('<a href="http://www.gbif.org/publisher/' . $k . '" target="_new">' . $obj->institutions[$k] . '</a>');
				$html = '';
				$total = 0;
				for ($decade = $obj->min_decade; $decade <= $obj->max_decade; $decade += 10)
				{
					
					if (isset($v[$decade]))
					{
						$total += $v[$decade];
						
						$colour = '';
						$n = floor(log10($v[$decade]));
						switch($n)
						{
							case 0:
								$colour = '#86CBEB';
								break;
							case 1:
								$colour = '#67B2DB';
								break;
							case 2:
								$colour = '#54A1D2';
								break;
							default:
								$colour = 'blue';
								break;
						}
						
						$html .= '<div style="display:inline;background-color:' . $colour . ';border:1px solid white;" title="' . $decade . '">&nbsp;</a></div>';
					}
					else
					{
						$html .= '<div style="display:inline;background-color:#EEE;border:1px solid white;">&nbsp;</div>';
					}
				}
				
				$summary[] = $html;
				$summary[] = $total;
				$obj->results[] = $summary;
				
			}
					
		}
	}
	
	api_output($obj, $callback);
}


//--------------------------------------------------------------------------------------------------
function country_host_country($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/publishingCountry?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			$obj->results[] = array('Country', 'Count');
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array((String)$row->key[1], $row->value);
			}
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_identification_level($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/identification_level?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			$obj->results[] = array('Level', 'Count');
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array((string)$row->key[1], $row->value);
			}
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_date_precision($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/date_precision?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			$obj->results[] = array('Level', 'Count');
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array((string)$row->key[1], $row->value);
			}
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_basis_of_record($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/basisOfRecord?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			$obj->results[] = array('Level', 'Count');
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array((string)$row->key[1], $row->value);
			}
		}
	}
	
	api_output($obj, $callback);
}



//--------------------------------------------------------------------------------------------------
function country_collection_dates($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/date?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			$obj->results[] = array('Year', 'Count');
			
			// hack for cases where there's only one value
			if (count($response_obj->rows) == 1)
			{
				$year = $response_obj->rows[0]->key[1];
				$obj->results[] = array((string)($year-1), 0);
			}
			
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array((string)$row->key[1], $row->value);
			}
			
			// hack for cases where there's only one value
			if (count($response_obj->rows) == 1)
			{
				$year = $response_obj->rows[0]->key[1];
				$obj->results[] = array((string)($year+1), 0);
			}
		}
	}
	
	api_output($obj, $callback);
}



//--------------------------------------------------------------------------------------------------
function country_classification($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/classification?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=5';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
		
/*
['Taxon', 'Parent', 'count'],
['Life', null,  0],
['Animalia', 'Life', 0],
['Chordata', 'Animalia', 0],
['Amphibia', 'Chordata', 138],
['Aves', 'Chordata', 70],
['Reptilia', 'Chordata', 51],
['Mollusca', 'Animalia', 0],
['Gastropoda', 'Mollusca', 23],
['Zagmena', 'Mollusca', 5]
*/
		
		
			$obj->status = 200;
			$obj->results = array();
			
			$obj->ancestor = array();
			$obj->ancestor['Life'] = null;
			
			
			foreach ($response_obj->rows as $row)
			{
				$n = count($row->key);
				
				// we may have to skip some names if they occur more than once in the classification
				
				$ok = true;
				
				if (in_array('Jungermanniales', $row->key))
				{
					if (in_array('Bryophyta', $row->key))
					{
						$ok = false;
					}
				}
				if (in_array('Jungermanniopsida', $row->key))
				{
					if (in_array('Bryophyta', $row->key))
					{
						$ok = false;
					}
				}
				if (in_array('Psilotopsida', $row->key))
				{
					if (in_array('Psilophyta', $row->key))
					{
						$ok = false;
					}
				}
				
				
				
				
				if ($ok)
				{
					// get ancestor descendant pairs
					for ($i = $n-1; $i > 1; $i--)
					{
						$obj->ancestor[$row->key[$i]] = $row->key[$i-1];
					}
					
					if (isset($row->key[1]))
					{
						if (!isset($obj->ancestor[$row->key[1]]))
						{
							$obj->ancestor[$row->key[1]] = "Life";
						}
					}	
				}
				
				//$obj->results[] = [(string)$row->key[1], $row->value];
			}
			
			// treemap data
			$obj->results[] = array("Taxon", "Parent", "count");
			
			$obj->missing = array_keys($obj->ancestor);
			
			foreach ($response_obj->rows as $row)
			{
				$ok = true;
				
				
				if (in_array('Jungermanniales', $row->key))
				{
					if (in_array('Bryophyta', $row->key))
					{
						$ok = false;
					}
				}
				if (in_array('Jungermanniopsida', $row->key))
				{
					if (in_array('Bryophyta', $row->key))
					{
						$ok = false;
					}
				}
				if (in_array('Psilotopsida', $row->key))
				{
					if (in_array('Psilophyta', $row->key))
					{
						$ok = false;
					}
				}
				
				
				
				if ($ok)
				{
					$n = count($row->key);
					
					if ($n > 1)
					{
						
						$r = array();
						
						$r[] = $row->key[$n-1];
						$r[] = $obj->ancestor[$row->key[$n-1]];
						$r[] = $row->value;
						
						// Delete node from list of missing
						if(($key = array_search($row->key[$n-1], $obj->missing)) !== false)
						{
							unset($obj->missing[$key]);
						}
					
					
						$obj->results[] = $r;
					}
				}
			}
			
			foreach ($obj->missing as $m)
			{
				$r = array();
				
				$r[] = $m;
				$r[] = $obj->ancestor[$m];
				$r[] = 0;
				
				$obj->results[] = $r;
			
			}
			
			
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_min_collection($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/minimum_collection_year?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	

		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	//echo $resp;
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			$obj->results = array();
			
			$years = array();
						
			foreach ($response_obj->rows as $row)
			{
				if (!$years[$row->value])
				{
					$years[$row->value] = 0;
				}
				$years[$row->value]++;
			}
			
			$cumulative = 0;
			
			$keys = array_keys($years);
			sort($keys);
			
			$obj->results[] = array("Year", "Total");
			
			// hack for cases where there's only one value
			if (count($keys) == 1)
			{
				$year = $keys[0];
				$obj->results[] = array((string)($year-1), 0);
			}
			
			
			foreach ($keys as $year)
			{
				$count = $years[$year];
				$cumulative += $count;
				$obj->results[] = array((string)$year, $cumulative);
				
			}
			
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function country_vertical($country, $callback = '')
{
	global $config;
	global $couch;
		
		
	$startkey = array($country);
	$endkey = array($country, "zzz");
		
	$url = '_design/country/_view/vertical?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	

		
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;
	
	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$zero_count = 0;
			
			$obj->status = 200;
			$obj->results = array();
			foreach ($response_obj->rows as $row)
			{
				$obj->results[] = array($row->key[1], $row->value);
				
				if ($row->key[1] == 0)
				{
					$zero_count++;
				}
			}
			
			if ($zero_count == 0)
			{
				$obj->results[] = array(0,0);
			}
			
		}
	}
	
	api_output($obj, $callback);
}



//--------------------------------------------------------------------------------------------------
function main()
{
	$callback = '';
	$handled = false;
	
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
	
	if (!$handled)
	{
		// Queries based on hash
		if (isset($_GET['country']))
		{	
			$country = $_GET['country'];

			if (!$handled)
			{
				if (isset($_GET['date']))
				{	
					country_collection_dates($country, $callback);
					$handled = true;
				}
				
			}
			
			if (!$handled)
			{
				if (isset($_GET['vertical']))
				{	
					country_vertical($country, $callback);
					$handled = true;
				}
				
			}

			if (!$handled)
			{
				if (isset($_GET['classification']))
				{	
					country_classification($country, $callback);
					$handled = true;
				}
				
			}
			
			if (!$handled)
			{
				if (isset($_GET['publishingCountry']))
				{	
					country_host_country($country, $callback);
					$handled = true;
				}
				
			}
			
			if (!$handled)
			{
				if (isset($_GET['identificationLevel']))
				{	
					country_identification_level($country, $callback);
					$handled = true;
				}
				
			}

			if (!$handled)
			{
				if (isset($_GET['minimum_collection_year']))
				{	
					country_min_collection($country, $callback);
					$handled = true;
				}
				
			}

			if (!$handled)
			{
				if (isset($_GET['publishingOrgKey']))
				{	
					if (isset($_GET['decade']))
					{
						country_publisher_decade($country, $callback);
						$handled = true;					
					}
					
					
					if (!$handled)
					{
						country_organisation($country, $callback);
						$handled = true;
					}
				}
				
			}
			
			if (!$handled)
			{
				if (isset($_GET['basisOfRecord']))
				{	
					country_basis_of_record($country, $callback);
					$handled = true;
				}
				
			}

			if (!$handled)
			{
				if (isset($_GET['date_precision']))
				{	
					country_date_precision($country, $callback);
					$handled = true;
				}
				
			}
			
			if (!$handled)
			{
				if (isset($_GET['species']))
				{	
					country_species($country, $_GET['species'], $callback);
					$handled = true;
				}
				
			}
			

			
			
		}
			
	}

}



main();

