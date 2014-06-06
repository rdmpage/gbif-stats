{
   "_id": "_design/country",
   "language": "javascript",
   "views": {
       "classification": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n  var key = [];\n\n  key.push(doc.countryCode);    \n    if (doc.kingdom) {\n     key.push(doc.kingdom);\n    }\n    if (doc.phylum) {\n     key.push(doc.phylum);\n    }\n    if (doc.class) {\n     key.push(doc.class);\n    }\n    if (doc.order) {\n     key.push(doc.order);\n    }\n    if (doc.family) {\n     key.push(doc.family);\n    }\n    if (doc.genus) {\n     key.push(doc.genus);\n    }\n   if (doc.species) {\n     key.push(doc.species);\n    }\n\n    emit(key, 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "publishingCountry": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n    if (doc.publishingCountry) {\n     var key = [];\n     key.push(doc.countryCode);    \n     key.push(doc.publishingCountry);\n     emit(key, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "date": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.year) {\n  var key = [];\n\n  key.push(doc.countryCode);    \n  key.push(doc.year);\n  \n  if (doc.month) {\n    key.push(doc.month);\n\n    if (doc.day) {\n       key.push(doc.day);\n    }\n  }\n    \n    emit(key, 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "identification_level": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n  var level = 'none';\n  \n\n    if (doc.kingdom) {\n     level = 'kingdom';\n    }\n    if (doc.phylum) {\n     level = 'phylum';\n    }\n    if (doc.class) {\n     level = 'class';\n    }\n     if (doc.order) {\n     level = 'order';\n    }\n    if (doc.family) {\n     level = 'family';\n    }\n    if (doc.genus) {\n     level = 'genus';\n    }\n    if (doc.species) {\n     level = 'species';\n    }\n\n  \n    emit([doc.countryCode,level], 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "publishingOrgKey": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.publishingOrgKey) {\n  var key = [];\n\n  key.push(doc.countryCode);    \n  key.push(doc.publishingOrgKey);\n  \n\n    emit(key, 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "basisOfRecord": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n    if (doc.basisOfRecord) {\n     var key = [];\n     key.push(doc.countryCode);    \n     key.push(doc.basisOfRecord);\n     emit(key, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "minimum_collection_year": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.year && doc.taxonKey) {\n  var key = [];\n\n  key.push(doc.countryCode);    \n  key.push(doc.taxonKey);\n  \n   \n    emit(key, doc.year);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    // Return the minumum numeric value.\n    var min = Infinity\n    for(var i = 0; i < values.length; i++)\n        if(typeof values[i] == 'number')\n            min = Math.min(values[i], min)\n    return min;\n}"
       },
       "tile": {
           "map": "function(doc) {\n  var tile_size = 256;\n  var pixels = 4;\n  var max_zoom = 11;\n\n  if (doc.countryCode && doc.decimalLatitude && doc.decimalLongitude) {\n\n    for (var zoom = 0; zoom < max_zoom; zoom++) {\n  \n    var x_pos = (parseFloat(doc.decimalLongitude) + 180)/360 * Math.pow(2, zoom);\n    var x = Math.floor(x_pos);\n    \n    var relative_x = Math.round(tile_size * (x_pos - x));\n  \n    var y_pos = (1-Math.log(Math.tan(parseFloat (doc.decimalLatitude)*Math.PI/180) + 1/Math.cos(parseFloat(doc.decimalLatitude)*Math.PI/180))/Math.PI)/2 *Math.pow(2,zoom);\n    var y = Math.floor(y_pos);\n    var relative_y = Math.round(tile_size * (y_pos - y));\n  \n    relative_x = Math.floor(relative_x / pixels) * pixels;\n    relative_y = Math.floor(relative_y / pixels) * pixels;\n  \n    var tile = [];\n    tile.push(zoom);\n    tile.push(x);\n    tile.push(y);\n    tile.push(doc.countryCode);\n    tile.push(relative_x);\n    tile.push(relative_y);\n    \n     \n    emit(tile, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "date_precision": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n \n  var date_precision = 'missing';\n\n  if (doc.year) {\n    date_precision = 'year'\n  }\n\n  if (doc.month) {\n    date_precision = 'month'\n  }\n\n  if (doc.day) {\n    date_precision = 'day'\n  }\n    \n    emit([doc.countryCode,date_precision], 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "vertical": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n    if (doc.depth || doc.elevation) {\n    \n      var vertical = 0;\n      if (doc.elevation) {\n        vertical = doc.elevation;\n      }\n      if (doc.depth) {\n        if (doc.depth != 0) {\n          vertical = -doc.depth;\n        }\n      }\n      var key = [];\n      key.push(doc.countryCode);\n      key.push(vertical);\n\n      emit(key, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "publishingOrgKey_decade": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.publishingOrgKey && doc.year) {\n    var key = [];\n\n    key.push(doc.countryCode);    \n    key.push(doc.publishingOrgKey);\n    key.push(doc.institutionCode);\n \n    if (isNaN(doc.year)) {\n    } else {\n       var decade = Math.floor(doc.year/10) * 10;\n       key.push(decade);\n       emit(key, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "associatedSequences": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n    if (doc.associatedSequences) {\n      if (doc.associatedSequences.match(/Genbank:\\s+/)) {\n        var sequences = doc.associatedSequences;\n        sequences = sequences.replace(/Genbank:\\s+/, '');\n        var accessions = sequences.split(';');\n        for (var i in accessions) {\n           emit([doc.countryCode,accessions[i].trim()], 1);\n        }\n      }\n    }\n    // Datasets that are actually sequences\n    // EMBL Australia\n    if (doc.datasetKey == 'c1fc2df7-223b-4472-8998-70afb3b749ab') {\n     emit([doc.countryCode,doc.catalogNumber], 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "species": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.speciesKey) {\n  var key = [];\n\n  key.push(doc.countryCode);    \n    if (doc.kingdom) {\n     key.push(doc.kingdom);\n    }\n    if (doc.phylum) {\n     key.push(doc.phylum);\n    }\n    if (doc.class) {\n     key.push(doc.class);\n    }\n    if (doc.order) {\n     key.push(doc.order);\n    }\n    if (doc.family) {\n     key.push(doc.family);\n    }\n    if (doc.genus) {\n     key.push(doc.genus);\n    }\n\n    // For reasons that surpass understanding species names can be different for same\n    // species, e.g occurrences 543471611 and 483375264 so we need to use scientificName\n    if (doc.species) {\n      key.push(doc.scientificName);\n     }\n    \n    // id (output taxonKey so we handle case where it's a subspecies\n    // new\n    if (doc.taxonKey) {\n      key.push(doc.taxonKey);\n    }\n    emit(key, 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "issues": {
           "map": "function(doc) {\n  if (doc.countryCode) {\n    for (var i in doc.issues) {\n      var key = [];\n      key.push(doc.countryCode);\n      key.push(doc.issues[i]);\n      emit(key, 1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}