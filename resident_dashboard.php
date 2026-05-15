<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "resident") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resident Fire Report</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<link rel="stylesheet" href="resident.css">

</head>

<body>

<div class="sidebar">

    <h2>🔥 Fire Report</h2>

    <div class="card">
        👤 <?= $_SESSION['full_name']; ?>
        <br>
        <small>Resident Account</small>
    </div>

    <div class="card">
        📍 Click map to select location
    </div>

    <div class="card">
        <form id="reportForm">

            <label>Description</label>
            <input type="text" id="description" required>

            <label>Severity</label>
            <select id="severity">
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>

            <button type="submit">Submit Report</button>

        </form>
    </div>

    <a href="logout.php">
        <button style="background:black;border:1px solid #dc2626;">Logout</button>
    </a>

</div>

<div id="map"></div>

<script> 
    let map = L.map('map').setView([13.8445, 121.2060], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'FireGIS'
}).addTo(map);

let selectedLatLng = null;
let marker = null;

// MAP CLICK
map.on('click', function(e){

    selectedLatLng = e.latlng;

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map)
        .bindPopup("Selected Location")
        .openPopup();
});

// SUBMIT REPORT
document.getElementById("reportForm").addEventListener("submit", async function(e){
    e.preventDefault();

    if (!selectedLatLng) {
        alert("Please select location on map");
        return;
    }

    let desc = document.getElementById("description").value;
    let severity = document.getElementById("severity").value;

    try {
        const res = await fetch("http://localhost:5000/report", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                user_id: <?= $_SESSION['user_id']; ?>,
                description: desc,
                severity: severity,
                lat: selectedLatLng.lat,
                lng: selectedLatLng.lng,
                type: "Fire Report",
                category: "Fire Incident"
            })
        });

        let data;
        try {
            data = await res.json();
        } catch {
            throw new Error("Server did not return JSON (check Flask)");
        }

        if (!res.ok) {
            console.error(data);
            throw new Error(data.message || "Server Error");
        }

        alert("Report Submitted Successfully!");
        document.getElementById("reportForm").reset();
        selectedLatLng = null;
        marker = null;

    } catch (err) {
        console.error("ERROR:", err);
        alert("Failed to submit report: " + err.message);
    }
});

</script>

</body>
</html>