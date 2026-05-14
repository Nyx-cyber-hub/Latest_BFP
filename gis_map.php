<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BFP GIS MAP</title>

    <!-- LEAFLET -->
    <link rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- HEATMAP -->
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #map {
            height: 100vh;
            width: 100%;
        }
    </style>

</head>

<body>

<div id="map"></div>

<script>

// INIT MAP
var map = L.map('map').setView([13.7565, 121.0583], 13);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    attribution: 'BFP GIS System'
}).addTo(map);

// HEATMAP LAYER
let heatLayer;

// LOAD INCIDENTS ONLY (DISPLAY MODE)
function loadIncidents(){

    fetch("http://127.0.0.1:5000/incidents")

    .then(res => res.json())

    .then(data => {

        // remove old markers
        map.eachLayer(layer => {
            if(layer instanceof L.CircleMarker){
                map.removeLayer(layer);
            }
        });

        let heatPoints = [];

        data.forEach(inc => {

            let color =
                inc.severity === "High" ? "red" :
                inc.severity === "Medium" ? "orange" :
                "green";

            // MARKERS ONLY
            L.circleMarker([inc.lat, inc.lng], {
                radius: 8,
                color: color
            })
            .addTo(map)
            .bindPopup(
                "<b>🔥 Incident</b><br>" +
                inc.description +
                "<br><b>Severity:</b> " +
                inc.severity
            );

            // HEATMAP DATA
            let intensity =
                inc.severity === "High" ? 1 :
                inc.severity === "Medium" ? 0.6 :
                0.3;

            heatPoints.push([
                inc.lat,
                inc.lng,
                intensity
            ]);

        });

        // refresh heatmap
        if(heatLayer){
            map.removeLayer(heatLayer);
        }

        heatLayer = L.heatLayer(heatPoints, {
            radius: 25,
            blur: 15
        }).addTo(map);

    });

}

// INIT LOAD
loadIncidents();

// AUTO REFRESH
setInterval(loadIncidents, 5000);

</script>

</body>
</html>