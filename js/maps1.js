
var geojsonObject = {
  "type":"FeatureCollection",
  "features": [{
    "type":"Feature",
    "geometry":{
      "type":"Point",
      "coordinates":[1377673.7944994, 6457178.0615443]
    }
    ,"properties":{
      "Nazev_CPO":"2. sbor Bratrsk\u00e9 jednoty baptist\u016f v Chebu",
      "Nazev_obce":"Cheb",
      "Nazev_ulice":"Blanick\u00e1",
      "Zrizovatel_text":"Bratrsk\u00e1 jednota baptist\u016f",
      "Cislo_do_adresy":"107\/26",
      "PSC":35002,
      "Zvlastni_prava":"\nPov\u011b\u0159it osoby vykon\u00e1vaj\u00edc\u00ed duchovenskou \u010dinnost k v\u00fdkonu duchovensk\u00e9 slu\u017eby v m\u00edstech, kde se vykon\u00e1v\u00e1 vazba, trest odn\u011bt\u00ed svobody\n\nPov\u011b\u0159it osoby vykon\u00e1vaj\u00edc\u00ed duchovenskou \u010dinnost k v\u00fdkonu duchovensk\u00e9 slu\u017eby v ozbrojen\u00fdch sil\u00e1ch \u010cR\n\nB\u00fdt financov\u00e1na podle zvl\u00e1\u0161tn\u00edho pr\u00e1vn\u00edho p\u0159edpisu o finan\u010dn\u00edm zabezpe\u010den\u00ed c\u00edrkv\u00ed a n\u00e1bo\u017eensk\u00fdch spole\u010dnost\u00ed\n\nZachov\u00e1vat povinnost ml\u010denlivosti duchovn\u00edmi v souvislosti s v\u00fdkonem zpov\u011bdn\u00edho tajemstv\u00ed nebo s v\u00fdkonem pr\u00e1va obdobn\u00e9ho zpov\u011bdn\u00edmu tajemstv\u00ed\n\nKonat ob\u0159ady, p\u0159i nich\u017e jsou uzav\u00edr\u00e1ny c\u00edrkevn\u00ed s\u0148atky\n\nZ\u0159izovat c\u00edrkevn\u00ed \u0161koly\n",
      "ICO":64840140,
      "Datum_vzniku":"1997-03-17"
    }
  }]
};

var vectorSource = new ol.source.Vector({
  features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
});

var vectorLayer = new ol.layer.Vector({
  source: vectorSource
});

// map layers
var vectorCirkve = new ol.layer.Vector({
  id: "cirkevniBody",
  title: "Body",
  source: new ol.source.Vector({
    url: "http://localhost/cirkve_ares/app/getjson.php?query=",
    format: new ol.format.GeoJSON({"defaultDataProjection": "EPSG:3857"})
//    format: new ol.format.Feature()
  })
//  }),
//  style: function(feature, resolution) {
//  }
});

var ortofotoWMS = new ol.layer.Tile({
  source: new ol.source.TileWMS({
    url: "http://geoportal.cuzk.cz/WMS_ORTOFOTO_PUB/WMService.aspx",
    params: {"LAYERS": "GR_ORTFOTORGB", "TILED": true},
    serverType: "geoserver",
  })
});

var basicWMS = new ol.layer.Tile({
  visible: true,
//  visible: false,
  opacity: 0.5,
  source: new ol.source.OSM({
  })
});

//basic declarations of map
var map = new ol.Map({
  target: "map",
  view: new ol.View({
    center: [1840211, 6382652], //mercator
    zoom: 5,
    projection: "EPSG:3857"
  })
});

map.addLayer(ortofotoWMS);
map.addLayer(basicWMS);
map.addLayer(vectorCirkve);
map.addLayer(vectorLayer);
