$("#info_wrapper").hide();

$('#hide_info_wrapper').click(function(){
  $("#info_wrapper").hide();
});

$("#display").val("Název církve nebo města");

$("#display").click(function() {
    $("#display").val("");
});

var projection = new ol.proj.Projection({
  code: 'EPSG:5514',
  units: 'm'
});

ol.proj.addProjection(projection);

var mousePositionControl = new ol.control.MousePosition({
  coordinateFormat: ol.coordinate.createStringXY(),
  projection: 'EPSG:5514',
  // comment the following two lines to have the mouse position
  // be placed within the map.
  className: 'custom-mouse-position',
  target: document.getElementById('mouse-position'),
  undefinedHTML: '&nbsp;'
});

var text = new ol.style.Text ({text: 'text'});

//$(document).ready(function() {
//  var urlJSON = $("#display").keyup(function() {
//    var myurl = 'http://localhost/cirkve_ares/app/getjson.php'  + '?query=' + $("#display").val();
//    return myurl;
//  });
//});

var layers = [
  new ol.layer.Tile({
    source: new ol.source.TileWMS({
      url: 'http://geoportal.cuzk.cz/WMS_ORTOFOTO_PUB/WMService.aspx',
      params: {'LAYERS': 'GR_ORTFOTORGB', 'TILED': true},
      serverType: 'geoserver'
    })
  }),
  new ol.layer.Vector({
    title: 'Body',
    source: new ol.source.Vector({
//      url: urlJSON,
      url: 'http://localhost/cirkve_ares/app/getjson.php?query=',
      format: new ol.format.GeoJSON()
//    }),
    }),
    style: new ol.style.Style({
      image: new ol.style.Circle({
        radius: 5,
        fill: new ol.style.Fill({color: 'red'})
      }),
      text: text
    })
  })
];


var map = new ol.Map({
controls: ol.control.defaults().extend([mousePositionControl]),
  layers: layers,
  target: 'map',
  view: new ol.View({
    center: [0,0],
    zoom: 4,
    projection: projection
  })
});

map.on('singleclick', function(e) {
  var feature = map.forEachFeatureAtPixel(e.pixel, function(feature) {
    return feature;
  });
  if (feature) { // if feature returned, show info
    $("#info_wrapper").show();
    var infoElement = document.getElementById('info');
    infoElement.innerHTML = 'Název: ' + feature.get('Nazev_CPO') + '<br>Ulice: ' + feature.get('Nazev_ulice') + ' ' + feature.get('Cislo_do_adresy') + '<br>Obec: ' + feature.get('Nazev_obce') + '<br>PSČ: ' + feature.get('PSC') + '<br>Zřizovatel:<br>' + feature.get('Zrizovatel_text') + '<br>Zvláštní práva:<br>' + feature.get('Zvlastni_prava');
  }
});

$("#display").keyup(function() {
  var s = new ol.source.Vector({
    url: 'http://localhost/cirkve_ares/app/getjson.php'  + '?query=' + $("#display").val(),
    format: new ol.format.GeoJSON()
  });

/*
OSM je problem, zkus tam hodit
http://geoportal.cuzk.cz/(S(t4ycgo0kcztcimrqfvbpqkvd))/Default.aspx?menu=3150&mode=TextMeta&side=wmts.uvod&metadataID=CZ-CUZK-WMTS-ZM-P&metadataXSL=metadata.sluzba
  var osm = new ol.source.OSM();
  losm = map.getLayers().getArray()[0];
  losm.setSource(osm);
*/
  l = map.getLayers().getArray()[1];
  l.setSource(s);
});
