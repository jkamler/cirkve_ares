$( document ).ready(function() {
  $('#orto').prop( "checked", true );
  $('#basic').prop( "checked", false );

  $("#info_wrapper").hide();

  $('#hide_info_wrapper').click(function(){
    $("#info_wrapper").hide();
  });

  $("#display").val("Název církve nebo města");

  $("#display").click(function() {
      $("#display").val("");
  });
});

//projection S-JTSK
var projectionSJTSK = new ol.proj.Projection({
  code: 'EPSG:5514',
//  code: 'EPSG:4326',
  units: 'm'
});

//projection WGS84
var projectionWGS84 = new ol.proj.Projection({
    code: 'EPSG:4326',
    units: 'm',
    extent: [-180.0000, -90.0000, 180.0000, 90.0000]
});

//projection Mercator EPSG:900913
var projectionMercator = new ol.proj.Projection({
//    code: 'EPSG:3857',
    code: 'EPSG:900913',
    units: 'm',
    extent: [-7501981,-1304807, 9178903, 7369995]
//    extent: [-180.0000, -90.0000, 180.0000, 90.0000] //WGS84
});


//register projection at OL
ol.proj.addProjection(projectionSJTSK);
ol.proj.addProjection(projectionWGS84);
ol.proj.addProjection(projectionMercator);


//coordinates
var mousePositionControl = new ol.control.MousePosition({
  coordinateFormat: ol.coordinate.createStringXY(),
//  projection: 'EPSG:4326',
//  projection: 'EPSG:3857', //mercator
//  projection: projectionSJTSK,
  // comment the following two lines to have the mouse position
  // be placed within the map.
  className: 'custom-mouse-position',
  target: document.getElementById('mouse-position'),
  undefinedHTML: '&nbsp;'
});

//styles
var style = new ol.style.Style({
  image: new ol.style.Circle({
    radius: 5,
    fill: new ol.style.Fill({color: 'red'})
  }),
  fill: new ol.style.Fill({
    color: 'rgba(255, 255, 255, 0.6)'
  }),
  stroke: new ol.style.Stroke({
    color: '#319FD3',
    width: 1
  }),
  text: new ol.style.Text({
    font: '12px Calibri,sans-serif',
    offsetY: -15,
    fill: new ol.style.Fill({
      color: '#000'
    }),
    stroke: new ol.style.Stroke({
      color: '#fff',
      width: 3
    })
  })
});

var styleSelectedFeature = new ol.style.Style({
  image: new ol.style.Circle({
    radius: 7,
    fill: new ol.style.Fill({color: 'blue'})
  }),
  fill: new ol.style.Fill({
    color: 'rgba(255, 255, 255, 0.6)'
  }),
  stroke: new ol.style.Stroke({
    color: '#319FD3',
    width: 1
  }),
  text: new ol.style.Text({
    font: '12px Calibri,sans-serif',
    offsetY: -15,
    fill: new ol.style.Fill({
      color: '#000'
    }),
    stroke: new ol.style.Stroke({
      color: '#fff',
      width: 3
    })
  })
});

// map layers
var vectorCirkve = new ol.layer.Vector({
  id: 'cirkevniBody',
  title: 'Body',
  source: new ol.source.Vector({
    url: 'http://localhost/cirkve_ares/app/getjson.php?query=',
//    projection: projectionSJTSK,
    projection: projectionMercator,
    format: new ol.format.GeoJSON()
  }),
  style: function(feature, resolution) {
    style.getText().setText(resolution < 7 ? feature.get('Nazev_CPO') : '');
    return style;
  }
});

vectorCirkve.transform

var ortofotoWMS = new ol.layer.Tile({
  source: new ol.source.TileWMS({
    url: 'http://geoportal.cuzk.cz/WMS_ORTOFOTO_PUB/WMService.aspx',
    params: {'LAYERS': 'GR_ORTFOTORGB', 'TILED': true},
    serverType: 'geoserver',
    projection: projectionMercator //
  })
});
/*
var basicWMS = new ol.layer.Tile({
  visible: false,
  source: new ol.source.TileWMS({
    url: 'http://geoportal.cuzk.cz/WMS_ZM10_PUB/WMService.aspx',
    params: {'LAYERS': 'GR_ZM10', 'TILED': true},
    serverType: 'geoserver'
  })
});
*/
var basicWMS = new ol.layer.Tile({
  source: new ol.source.OSM({
    projection: projectionMercator
  })
});

//seting up interaction
var selectInteraction = new ol.interaction.Select({
  layers: function(layer) {
    return layer.get('selectable') == true;
  },
  style: function(feature, resolution) {
    styleSelectedFeature.getText().setText(feature.get('Nazev_CPO'));
    return styleSelectedFeature;
  }
});

//basic declarations of map
var map = new ol.Map({
controls: ol.control.defaults().extend([mousePositionControl]),
  target: 'map',
  view: new ol.View({
//    center: [-670000, -1080000], // S-JTSK
    center: [17, 50], //WGS84
//    zoom: 9,
    zoom: 5,
//    projection: projectionSJTSK
    projection: projectionMercator
  })
});

map.addLayer(ortofotoWMS);
map.addLayer(basicWMS);
map.addLayer(vectorCirkve);
map.getInteractions().extend([selectInteraction]);

vectorCirkve.set('selectable', true);

//searching in map
var displayFeatureInfo = function(pixel) {
  var features = [];
  map.forEachFeatureAtPixel(pixel, function(feature, layer) {
    features.push(feature);
  });
  var container = document.getElementById('info');
  if (features.length > 0) {
    $("#info_wrapper").show();
    var info = [];
    for (var i = 0, ii = features.length; i < ii; ++i) {
      info.push('<div class="cirkevniContainer"><div class="cirkevNameContainer">' + features[i].get('Nazev_CPO') + '</div><br><div class="cirkevPropertiesContainer">IČ: ' + features[i].get('ICO') + '<br>Ulice: ' + features[i].get('Nazev_ulice') + ' ' + features[i].get('Cislo_do_adresy') + '<br>Obec: ' + features[i].get('Nazev_obce') + '<br>PSČ: ' + features[i].get('PSC') + '<br>Zřizovatel:<br>' + features[i].get('Zrizovatel_text') + '<br>Zvláštní práva:<br>' + features[i].get('Zvlastni_prava') + '</div></div>');
    }
    container.innerHTML = info.join('<hr>') || '(unknown)';
  } else {
    $("#info_wrapper").hide();
    container.innerHTML = '&nbsp;';
  }
};

map.on('click', function(evt) {
//  ortofotoWMS.setVisible(false);
//  basicWMS.setVisible(true);
//  WMSlayerGroup.setVisible(false);
  var pixel = evt.pixel;
  displayFeatureInfo(pixel);
});


//switching maps
$('#controlWrapper').click(function() {
   if($('#orto').is(':checked')) {
    ortofotoWMS.setVisible(true);
    basicWMS.setVisible(false);
   }
   if($('#basic').is(':checked')) {
    ortofotoWMS.setVisible(false);
    basicWMS.setVisible(true);
    alert(basicWMS.getProjection());
   }

});

//dynamic search
$("#display").keyup(function() {
  var s = new ol.source.Vector({
    url: 'http://localhost/cirkve_ares/app/getjson.php'  + '?query=' + $("#display").val(),
    format: new ol.format.GeoJSON()
  });
  l = map.getLayers().getArray()[2];
  l.setSource(s);
});

//map.removeInteraction(interaction)
//map.removeLayer(layer)
