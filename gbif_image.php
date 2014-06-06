<?php

require_once (dirname(__FILE__) . '/lib.php');

// Get EOL thumbnail for GBIF


$id = $_GET['id'];

$url = 'http://eol.org/api/search_by_provider/1.0/' . $id . '.json?hierarchy_id=800';

$url .= '&cache_ttl=';

$json = get($url);

//echo $json;

$obj = json_decode($json);

// No image
$filename = 'images/blank.jpg';

//print_r($obj);
if (isset($obj[0]->eol_page_id))
{
	$filename = 'tmp/' . $obj[0]->eol_page_id .  '.jpg';
	
	if (!file_exists($filename))
	{
		// set default filename (in EOL but no image)
		$filename = 'images/eol.jpg';

		// fetch details
		
		$url = 'http://eol.org/api/pages/1.0/' . $obj[0]->eol_page_id . '.json?details=1&amp;images=10';
		
		//echo $url;
		
		$json = get($url);
		if ($json)
		{
			$page = json_decode($json);
			
			//print_r($page);
			
			if (isset($page->dataObjects))
			{
				$image_url = '';
				$n = count($page->dataObjects);
				$i = 0;
				while (($i < $n) && ($image_url == ''))
				{
					if ($page->dataObjects[$i]->dataType == 'http://purl.org/dc/dcmitype/StillImage')
					{
						$image_url = $page->dataObjects[$i]->eolThumbnailURL;
					}
					$i++;
				}
				
				if ($image_url != '')
				{
					$img = get($image_url);
					
					if ($img != '')
					{
						$filename = 'tmp/' . $obj[0]->eol_page_id .  '.jpg';
						file_put_contents($filename, $img);								
					}
				}
			}
		}
	}
}

$img = file_get_contents($filename);
header("Content-type: image/jpeg");
header("Cache-control: max-age=3600");
echo $img;


?>