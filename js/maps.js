$("#info_wrapper").hide();

$('#hide_info_wrapper').click(function(){
  $("#info_wrapper").hide();
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

var layers = [
  new ol.layer.Tile({
    source: new ol.source.TileWMS({
      url: 'http://geoportal.cuzk.cz/WMS_ORTOFOTO_PUB/WMService.aspx',
      params: {'LAYERS': 'GR_ORTFOTORGB', 'TILED': true},
      serverType: 'geoserver'
    })
  }),
  new ol.layer.Vector({
    title: 'Earthquakes',
    source: new ol.source.Vector({
      url: 'test.json',
      format: new ol.format.GeoJSON()
    }),
    style: new ol.style.Style({
      image: new ol.style.Circle({
        radius: 5,
        fill: new ol.style.Fill({color: 'red'})
      })
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
    infoElement.innerHTML = feature.get('title');
  }
});