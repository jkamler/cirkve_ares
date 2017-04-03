$( document ).ready(function() {
  $('#orto').prop( "checked", false );
  $('#basic').prop( "checked", true );

  $("#info_wrapper").hide();

  $("#infocontent").hide();

  $('#hide_info_wrapper').click(function(){
    $("#info_wrapper").hide();
  });

  $("#display").val("Název církve nebo města");

  $("#display").click(function() {
      $("#display").val("");
  });
});

//coordinates
var mousePositionControl = new ol.control.MousePosition({
  coordinateFormat: ol.coordinate.createStringXY(),
  // comment the following two lines to have the mouse position
  // be placed within the map.
  className: 'custom-mouse-position',
  target: document.getElementById('mouse-position'),
  undefinedHTML: '&nbsp;'
});

//styles

var style = new ol.style.Style({
  image: new ol.style.RegularShape({
    fill: new ol.style.Fill({
      color: 'red'
    }),
    stroke: new ol.style.Stroke({
      color: 'black',
      width: 2
    }),
    points: 4,
    radius: 5,
    angle: 0
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
  image: new ol.style.RegularShape({
    fill: new ol.style.Fill({
      color: '#33ff33'
    }),
    stroke: new ol.style.Stroke({
      color: 'black',
      width: 2
    }),
    points: 4,
    radius: 7,
    angle: 0
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
    format: new ol.format.GeoJSON({"defaultDataProjection": "EPSG:3857"})
  }),
  style: function(feature, resolution) {
    style.getText().setText(resolution < 7 ? feature.get('Nazev_CPO') : '');
    return style;
  }
});

var ortofotoWMS = new ol.layer.Tile({
  visible: false,
  source: new ol.source.TileWMS({
    url: 'http://geoportal.cuzk.cz/WMS_ORTOFOTO_PUB/WMService.aspx',
    params: {'LAYERS': 'GR_ORTFOTORGB', 'TILED': true},
    serverType: 'geoserver',
  })
});

var basicWMS = new ol.layer.Tile({
  visible: true,
//  visible: false,
//  opacity: 0.5,
  source: new ol.source.OSM({
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

//interaction on hover
var selectPointerMove = new ol.interaction.Select({
  condition: ol.events.condition.pointerMove,
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
    center: [1740211, 6382652],
    projection: 'EPSG:3857', //mercator
    zoom: 8
  })
});

map.addLayer(ortofotoWMS);
map.addLayer(basicWMS);
map.addLayer(vectorCirkve);
map.getInteractions().extend([selectInteraction]);
map.addInteraction(selectPointerMove);

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
      info.push('<div class="cirkevniContainer"><div class="cirkevNameContainer">' + features[i].get('Nazev_CPO') + '</div><br><div class="cirkevPropertiesContainer">IČ: ' + features[i].get('ICO') + '<br>Ulice: ' + features[i].get('Nazev_ulice') + ' ' + features[i].get('Cislo_do_adresy') + '<br>Obec: ' + features[i].get('Nazev_obce') + '<br>PSČ: ' + features[i].get('PSC') + '<br>Zřizovatel:<br>' + features[i].get('Zrizovatel_text') + '<br>Zvláštní práva:<br>' + features[i].get('Zvlastni_prava') + '<br>www: <a href="http://' + features[i].get('web') + '">' + features[i].get('web') + '</a>' + '</div></div>');
    }
    container.innerHTML = info.join('<hr>') || '(unknown)';
  } else {
    $("#info_wrapper").hide();
    container.innerHTML = '&nbsp;';
  }
};

map.on('click', function(evt) {
  var pixel = evt.pixel;
  displayFeatureInfo(pixel);
  if ($("#display").val() == "") {
    $("#display").val("Název církve nebo města");
  }
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
   }

});

//dynamic search
$("#display").keyup(function() {
  var s = new ol.source.Vector({
    url: 'http://localhost/cirkve_ares/app/getjson.php'  + '?query=' + $("#display").val(),
    format: new ol.format.GeoJSON({"defaultDataProjection": "EPSG:3857"})
  });
  l = map.getLayers().getArray()[2];
  l.setSource(s);
});

$("#aboutapp").click(function() {
//  alert("sem tu");
  $("#infocontent").toggle();
});
