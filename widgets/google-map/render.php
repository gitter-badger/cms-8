<?php
$latitude = $widget_parameters['lat'] ? $widget_parameters['lat'] : 0;
$longtitude = $widget_parameters['lng'] ? $widget_parameters['lng'] : 0;
?>

<div class="map-container"></div>
<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&q=Kerkgracht+60,1354+AM+Almere" defer></script>
<script>
  function initMap() {
    var mapDiv = document.querySelector('[data-widget-id={$widget_id}] .map-container');
    var map = new google.maps.Map(mapDiv, {
      center: {
        lat: <?= $latitude ?>,
        lng: <?= $longtitude ?>
      },
      zoom: 15,
      zoomControl: true,
      scaleControl: false,
      scrollwheel: false,
      disableDoubleClickZoom: true,
      disableDefaultUI: true,
      draggable: false,
      styles: [
        {
          "featureType": "administrative",
          "elementType": "labels.text.fill",
          "stylers": [
            {
              "color": "#444444"
            }
          ]
        },
        {
          "featureType": "landscape",
          "elementType": "all",
          "stylers": [
            {
              "color": "#f2f2f2"
            }
          ]
        },
        {
          "featureType": "poi",
          "elementType": "all",
          "stylers": [
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "road",
          "elementType": "all",
          "stylers": [
            {
              "saturation": -100
            },
            {
              "lightness": 45
            }
          ]
        },
        {
          "featureType": "road.highway",
          "elementType": "all",
          "stylers": [
            {
              "visibility": "simplified"
            }
          ]
        },
        {
          "featureType": "road.arterial",
          "elementType": "labels.icon",
          "stylers": [
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "transit",
          "elementType": "all",
          "stylers": [
            {
              "visibility": "off"
            }
          ]
        },
        {
          "featureType": "water",
          "elementType": "all",
          "stylers": [
            {
              "color": "#46bcec"
            },
            {
              "visibility": "on"
            }
          ]
        }
      ]
    });

    new google.maps.Marker({
      position: {
        lat: <?= $latitude ?>,
        lng: <?= $longtitude ?>
      },
      map: map,
      title: 'Iraanse Kerk'
    });
  }
</script>