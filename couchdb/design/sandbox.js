{
   "_id": "_design/sandbox",
   "language": "javascript",
   "views": {
       "fishbase": {
           "map": "function(doc) {\n  if (doc.datasetKey) {\n    // FishBase\n    if (doc.datasetKey == '197908d0-5565-11d8-b290-b8a03c50a862') {\n        emit(doc.catalogNumber,1);\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "time": {
           "map": "function(doc) {\n  // Compte different in time (years) between date of name and date of collection\n  if (doc.country && doc.scientificName && doc.year) {\n    var pattern = /([0-9]{4})\\)?$/;\n \n    var matches = doc.scientificName.match(pattern);\n    if (matches) {\n      var name_year = matches[1];\n      var difference = doc.year - name_year;\n      emit(difference, 1);\n      }\n  }\n\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "typeStatus": {
           "map": "function(doc) {\n  if (doc.countryCode && doc.typeStatus) {\n    emit(doc.typeStatus, 1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "shelflife": {
           "map": "function(doc) {\n  // Compte different in time (years) between date of name and date of collection\n  if (doc.country && doc.scientificName && doc.year && doc.typeStatus) {\n\n    if (doc.typeStatus == 'HOLOTYPE') {\n      var pattern = /([0-9]{4})\\)?$/;\n \n      var matches = doc.scientificName.match(pattern);\n      if (matches) {\n        var name_year = matches[1];\n        var difference = doc.year - name_year;\n        emit(difference, 1);\n        }\n    }\n  }\n\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "locality_lat_lon": {
           "map": "function(doc) {\n  if (doc.locality && doc.countryCode && doc.decimalLatitude && doc.decimalLongitude) {\n  \n    var key = [doc.locality, doc.countryCode, doc.decimalLatitude, doc.decimalLongitude];\n    emit(key,1);\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}