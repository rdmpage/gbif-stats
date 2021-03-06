<?php

// GBIF stats

require_once (dirname(__FILE__) . '/couchsimple.php');

// default
$country = 'SB';

if (isset($_GET['country']))
{
	$country = $_GET['country'];
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>GBIF Stats</title>

    <style type="text/css">
      body {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        background-color:rgb(242,242,242);
      }

      #map {
       	margin-right:400px;
        height: 400px;
      }
      
      #details {
		float:right;
		width:400px;
		font-size:12px;
		overflow:auto;
      }
      
      #details {
		float:right;
		width:700px;
		overflow:auto;
		
      }
      
      .widget {
        float:right;
      	width:300px;
      	height:200px;
      	padding:5px;
      	border:1px solid #DDDDDD;
      	border-radius: 5px;
      	margin:5px;
      	background-color:white;
      }
      
      .wide_widget {
        float:right;
      	width:620px;
      	height:400px;
      	padding:5px;
      	border:1px solid #DDDDDD;
      	margin:5px;
      	background-color:white;
      	
      }
 
      
    </style>
    
    <script src="http://www.google.com/jsapi"></script>    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    
<script type="text/javascript">
/*
    function mouse_over(id) 
    {
    	var e = document.getElementById(id);
    	e.style.opacity= "1.0";
	    e.style.filter = "alpha(opacity=100)";
    }
    
    function mouse_out(id) 
    {
    	var e = document.getElementById(id);
    	e.style.opacity=0.6;
        e.style.filter = "alpha(opacity=60)";
    }    
*/
</script>
    

    <script type="text/javascript">
    
		var map;
		var country = '<?php echo $country; ?>';
		var marker = null;
	
		google.load('maps', '3', {
			other_params: 'sensor=false'
		  });
		google.setOnLoadCallback(initialize);
		
      google.load("visualization", "1", {packages:["corechart"]});
      google.load("visualization", "1", {packages:["treemap"]});
      google.load('visualization', '1', {packages:['table']}); 
      
      //--------------------------------------------------------------------------------------------
      function show_publisher_decade(geohash)
      {
			$("#organisation").html("");
			var url = "api.php?country=" + country + "&publishingOrgKey&decade&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  sortAscending: false,
							  sortColumn: 2,
							  height: 400,
							  width: 620,
							  allowHtml: true
							};
					
							var chart = new google.visualization.Table(document.getElementById('publisherDecade'));
							chart.draw(data, options);								
						}
					}
				});
	 }		
      
      
      //--------------------------------------------------------------------------------------------
      function show_collection_dates(geohash)
      {
			$("#collectionDate").html("");
			var url = "api.php?country=" + country + "&date&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  title: 'Collection dates',
							  legend: { position: 'none'},
							  hAxis: { title : 'Year' },
							  vAxis: { title : 'Number of occurrences' }
							  
							};
					
							var chart = new google.visualization.AreaChart(document.getElementById('collectionDate'));
							chart.draw(data, options);							
							
						}
					}
				});
	 }	
	 
      //--------------------------------------------------------------------------------------------
      function show_host_country(country)
      {
			$("#hostCountry").html("");
			var url = "api.php?country=" + country + "&publishingCountry&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								colorAxis: {colors: ['#98D7F0', '#1179BA']}
							};
					
							var chart = new google.visualization.GeoChart(document.getElementById('hostCountry'));
							chart.draw(data, options);
							}
						}
					});
	 }		
	 
	 
      //--------------------------------------------------------------------------------------------
      function show_identification_level(country)
      {
			$("#identificationLevel").html("");
			var url = "api.php?country=" + country + "&identificationLevel&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Taxonomic rank of identification',
								  pieHole: 0.4,
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('identificationLevel'));
							chart.draw(data, options);
							}
						}
					});
	 }		
	 
      //--------------------------------------------------------------------------------------------
      function show_date_precision(country)
      {
			$("#identificationLevel").html("");
			var url = "api.php?country=" + country + "&date_precision&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Date precision',
								  pieHole: 0.4,
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('datePrecision'));
							chart.draw(data, options);
							}
						}
					});
	 }		

      //--------------------------------------------------------------------------------------------
      function show_spatial_precision(country)
      {
			$("#identificationLevel").html("");
			var url = "api.php?country=" + country + "&spatial_precision&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Spatial precision',
								  pieHole: 0.4,
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('spatialPrecision'));
							chart.draw(data, options);
							}
						}
					});
	 }		
	 
	 
      //--------------------------------------------------------------------------------------------
      function show_basis_of_record(country)
      {
			$("#basisOfRecord").html("");
			var url = "api.php?country=" + country + "&basisOfRecord&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Basis of record',
								  pieHole: 0.4,
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('basisOfRecord'));
							chart.draw(data, options);
							}
						}
					});
	 }		

      //--------------------------------------------------------------------------------------------
      function show_typestatus(country)
      {
			$("#typestatus").html("");
			var url = "api.php?country=" + country + "&typestatus&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Type status',
								  pieHole: 0.4,
								  pieSliceText: 'value'
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('typestatus'));
							chart.draw(data, options);
							}
						}
					});
	 }		

      //--------------------------------------------------------------------------------------------
      function show_issues(country)
      {
			$("#issues").html("");
			var url = "api.php?country=" + country + "&issues&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d);
							
							var options = {
								  title: 'Issues',
								  pieHole: 0.4,
								  pieSliceText: 'value'
								};	
								
							var chart = new google.visualization.PieChart(document.getElementById('issues'));
							chart.draw(data, options);
							}
						}
					});
	 }		
	 
      //--------------------------------------------------------------------------------------------
      function show_classification(country)
      {
			$("#classification").html("");
			var url = "api.php?country=" + country + "&classification&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
														
							var data = google.visualization.arrayToDataTable(d	);
					
							// Create and draw the visualization.
							var tree = new google.visualization.TreeMap(document.getElementById('classification'));
							tree.draw(data, {
							  minColor: '#86CBEB',
							  midColor: '#67B2DB',
							  maxColor: '#54A1D2',
							  headerHeight: 15,
							  fontColor: 'black',
							  maxDepth: 3
							  
							  });
							}
							
							//alert(JSON.stringify(data));
							
        					google.visualization.events.addListener(tree, 'select', 
								function() {
									var selection = tree.getSelection();
									var item = selection[0];
					
									// construct a query 
									
									// Do this for terminal nodes only
									if (data.getFormattedValue(item.row,2) != 0) {
										var key = [];
										
										// Get path to root of tree
										var key = [];
										key.push(data.getFormattedValue(item.row,0));
										
										var ancestor = data.getFormattedValue(item.row,1);
										
										while (ancestor) {
											key.unshift(ancestor);
											
											var f = null;
											for (var i in d) {
												if (d[i][0] == ancestor) {
													f = d[i][1];
													if (f == "Life") {
														f = null;
													}
												}
											}
											
											ancestor = f;
										}
										
										// debugging
										//key.push(data.getFormattedValue(item.row,2));
										
										//alert(JSON.stringify(key));
										
										show_species_list(country,key);

									}
					
								});
								
							
							
						}
					});
	 }	
	 
      //--------------------------------------------------------------------------------------------
      function show_species_list(country, path)
      {
			$("#list").html("");
			$("#list").html('Loading...');
			var url = "api.php?country=" + country + "&species=" + encodeURIComponent(JSON.stringify(path)) + "&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  sortAscending: false,
							  sortColumn: 2,
							  height: 400,
							  width: 620,
							  allowHtml: true
							};
					
							var chart = new google.visualization.Table(document.getElementById('list'));
							chart.draw(data, options);								
						}
					}
				});

	 }	
	 
	 
    //--------------------------------------------------------------------------------------------
      function show_min_collection_dates(country)
      {
			$("#minCollectionDate").html("");
			var url = "api.php?country=" + country + "&minimum_collection_year&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  title: 'Cumulative addition of taxa',
							  legend: { position: 'none'},
							  hAxis: { title : 'Year' },
							  vAxis: { title : 'Cumulative number of taxa' }
							};
					
							var chart = new google.visualization.AreaChart(document.getElementById('minCollectionDate'));
							chart.draw(data, options);							
							
						}
					}
				});
	 }	
	 
	  //--------------------------------------------------------------------------------------------
      function show_vertical(country)
      {
			$("#vertical").html("");
			var url = "api.php?country=" + country + "&vertical&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							// Column titles
							d.unshift(['Depth','Count']);
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  title: 'Collection elevation/depth',
							  legend: { position: 'none'},
							  hAxis: { title : 'Number of occurrences' },
							  vAxis: { title : 'Elevation/depth (m)' } 
							};
					
							var chart = new google.visualization.BarChart(document.getElementById('vertical'));
							chart.draw(data, options);							
							
						}
					}
				});
	 }		

	  //--------------------------------------------------------------------------------------------
      function show_type_shelflife(country)
      {
			$("#vertical").html("");
			var url = "api.php?country=" + country + "&typeshelflife&callback=?";
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							var html = '';
							
							// chart
							var d = data.results;
							
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  title: 'Type shelflife',
							  legend: { position: 'none'},
							  hAxis: { title : 'Years between collection and typification' },
							  vAxis: { title : 'Number of occurrences' }
							};
					
							var chart = new google.visualization.AreaChart(document.getElementById('typeshelflife'));
							chart.draw(data, options);														
						}
					}
				});
	 }		

	 
 		  
		  
		//------------------------------------------------------------------------------------------
		// Normalizes the coords that tiles repeat across the x axis (horizontally)
		// like the standard Google map tiles.
		function getNormalizedCoord(coord, zoom) {
		  var y = coord.y;
		  var x = coord.x;
		
		  // tile range in one direction range is dependent on zoom level
		  // 0 = 1 tile, 1 = 2 tiles, 2 = 4 tiles, 3 = 8 tiles, etc
		  var tileRange = 1 << zoom;
		
		  // don't repeat across y-axis (vertically)
		  if (y < 0 || y >= tileRange) {
			return null;
		  }
		
		  // repeat across x-axis
		  if (x < 0 || x >= tileRange) {
			x = (x % tileRange + tileRange) % tileRange;
		  }
		
		  return {
			x: x,
			y: y
		  };
		}
			  
      
		//--------------------------------------------------------------------------------------------
		/** @constructor */
		function BoldMapType(tileSize) {
		  this.tileSize = tileSize;
		}
		
		//--------------------------------------------------------------------------------------------
		BoldMapType.prototype.getTile = function(coord, zoom, ownerDocument) {	
		  	var div = ownerDocument.createElement('div');
		  
			var normalizedCoord = getNormalizedCoord(coord, zoom);
			  if (!normalizedCoord) {
				return null;
			  }  
		  
		  	// Get tile from CouchDB
		  	var url = 'couchtile.php?x=' + normalizedCoord.x 
		  		+ '&y=' + normalizedCoord.y 
		  		+ '&z=' + zoom
		  		+ '&country=' + country;
		  
			div.innerHTML = '<img src="' + url + '"/>';
		  	div.style.width = this.tileSize.width + 'px';
		 	div.style.height = this.tileSize.height + 'px';
		  
		  	return div;
		};      
		
		//--------------------------------------------------------------------------------------------
		// handle user click on map
		function placeMarker(position, map) {

			if (marker) {
			   marker.setMap(null);
			   marker = null;
			}
			
			
  			 marker = new google.maps.Marker({
      			position: position,
      			map: map
  			});
  			
		
			// handle hit here...
			$('#list').html('');
			
			$('#list').html('Loading...');
			
			// http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
			// Compute the tile the user has clicked on, and the relative position of the click
			// within that tile
			var tile_size = 256;
			var pixels = 4;
			var zoom = map.getZoom();
			
			var x_pos = (parseFloat(position.lng()) + 180)/360 * Math.pow(2, zoom);
			var x = Math.floor(x_pos);
			
			// position within tile
			var relative_x = Math.round(tile_size * (x_pos - x));
		
			var y_pos = (1-Math.log(Math.tan(parseFloat (position.lat())*Math.PI/180) + 1/Math.cos(parseFloat(position.lat())*Math.PI/180))/Math.PI)/2 *Math.pow(2,zoom);
			var y = Math.floor(y_pos);
			
			// position within tile
			var relative_y = Math.round(tile_size * (y_pos - y));
		
			// cluster into square defined by pixel size
			relative_x = Math.floor(relative_x / pixels) * pixels;
		    relative_y = Math.floor(relative_y / pixels) * pixels;
			
			var url = "couchtilehit.php"
				+ "?x=" + x
				+ "&y=" + y
				+ "&z=" + zoom
				+ "&rx=" + relative_x
				+ "&ry=" + relative_y
				+ "&countryCode=" + country
				+ "&callback=?";
				
			//alert(url);
			
			$.getJSON(url,
				function(data){
					if (data.status == 200) {
						if (data.results.length != 0) {
							/*
							var html = '';
							
							html += '<div>' + '<b>Number of occurrences: ' + data.results.length + '</b>' + '</div>';
							
							html += '<span class="explain">Tile [x,y,z,rx,ry] = [' + x + ',' + y + ',' + zoom + ',' + relative_x + ',' + relative_y + ']</span>';
							
							for (var i in data.results) {
							   html += '<div>';
							   html += data.results[i]._id + ' ';
							   html += '</div>';
							   
							}
							
							$('#list').html(html);
							*/
							
							var html = '';
							
							// chart
							var d = data.results;
							
							
							var data = google.visualization.arrayToDataTable(d	);

							var options = {
							  sortAscending: false,
							  sortColumn: 2,
							  height: 400,
							  width: 620,
							  allowHtml: true
							};
					
							var chart = new google.visualization.Table(document.getElementById('list'));
							chart.draw(data, options);								
							
							
							
						} else {
						    $('#list').html('No occurrences here (try clicking again)');
						}
						
					}
				});			

		}

      //--------------------------------------------------------------------------------------------
      function initialize() {
    	
		var center = new google.maps.LatLng(0,0);
		
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 2,
          center: center,
          mapTypeId: google.maps.MapTypeId.TERRAIN,
          draggableCursor: 'auto'
        });
        
        // hit test
		google.maps.event.addListener(map, 'click', function(e) {
    		placeMarker(e.latLng, map);
  			});
        
		// Insert this overlay map type as the first overlay map type at
		// position 0. Note that all overlay map types appear on top of
		// their parent base map.
		map.overlayMapTypes.insertAt(
		  0, new BoldMapType(new google.maps.Size(256, 256)));
      
		/* http://stackoverflow.com/questions/6762564/setting-div-width-according-to-the-screen-size-of-user */
		$(window).resize(function() { 
			var windowHeight = $(window).height();
			$('#map').css({'height':windowHeight });
			$('#details').css({'height':windowHeight});
		});	
		
		
		// move map to display country
			
		// http://wiki.openstreetmap.org/wiki/User:Ewmjc/Country_bounds
		if (country != '') {
			new_bounds = new google.maps.LatLngBounds();
			switch (country) {
				case 'SB':
					// Solomon Islands	SB	BP	155.516667	-12.883333	170.2	-5.166667	162.8583335	-9.025	
					new_bounds.extend(new google.maps.LatLng(-12.883333, 155.516667));
					new_bounds.extend(new google.maps.LatLng(-5.166667, 170.2));
					map.fitBounds(new_bounds); 
					break;
					
				default:
					break;
			}
		}

		
		show_collection_dates (country);
		show_basis_of_record(country);
		show_host_country(country);
		show_identification_level(country);
		show_classification(country);
		show_min_collection_dates(country);
		show_date_precision(country);
		show_vertical(country);
		show_publisher_decade(country);
		show_typestatus(country);
		show_type_shelflife(country);
		show_issues(country);
		show_spatial_precision(country);
	
      }
      
    </script>
  </head>
  <body onload="$(window).resize()">
   	<div style="position:relative;">
 	    <div id="details">
 	    	<div class="widget" id="classification"></div>
 	    	<div class="widget" id="identificationLevel"></div>
 	    	<div class="wide_widget" id="list"></div>
 	    	<div class="widget" id="collectionDate"></div>
 	    	<div class="widget" id="minCollectionDate"></div>
 	    	<div class="widget" id="basisOfRecord"></div>
 	    	<div class="widget" id="hostCountry"></div>
 	    	<div class="widget" id="vertical"></div>
 	    	<div class="widget" id="datePrecision"></div>
 	    	<div class="widget" id="typestatus"></div>
 	    	<div class="widget" id="typeshelflife"></div>
 	    	<div class="widget" id="issues"></div>
 	    	<div class="widget" id="spatialPrecision"></div>
 	    	<div class="wide_widget" id="publisherDecade"></div>
 	    </div>
   		<div id="map"></div>
	</div>
  </body>
</html>