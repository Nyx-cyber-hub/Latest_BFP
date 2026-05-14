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

<style>
body {
    margin: 0;
    font-family: Arial;
    display: flex;
    height: 100vh;
    background: #f4f6f9;
}

.sidebar {
    width: 320px;
    background: #111;
    color: white;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    border-right: 4px solid #dc2626;
}

.sidebar h2 {
    color: #dc2626;
    font-size: 22px;
}

.userCard {
    background: white;
    color: black;
    padding: 15px;
    border-radius: 14px;
    border-top: 3px solid #dc2626;
}

.card {
    background: white;
    color: black;
    padding: 15px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

input, select {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

input:focus, select:focus {
    border-color: #dc2626;
}

button {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #b91c1c;
}

#map {
    flex: 1;
    height: 100vh;
}

.small {
    font-size: 12px;
    color: gray;
}
</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h2>🔥 Fire Report</h2>

    <div class="userCard">
        👤 <?= $_SESSION['full_name']; ?>
        <div class="small">Resident Account</div>
    </div>

    <div class="card">
        📍 Click map to select location
    </div>

    <!-- REPORT FORM -->
    <div class="card">

        <form id="reportForm">

            <label>Description</label>
            <input type="text" id="description" required>

            <label>Severity</label>
            <select id="severity">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>

            <button type="submit">Submit Report</button>

        </form>

    </div>

    <a href="logout.php">
        <button style="background:black;border:1px solid #dc2626;">
            Logout
        </button>
    </a>

</div>

<!-- MAP -->
<div id="map"></div>

<script>

let map = L.map('map').setView([13.8445, 121.2060], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'FireGIS System'
}).addTo(map);

let selectedLatLng = null;

// MAP CLICK
map.on('click', function(e){

    selectedLatLng = e.latlng;

    L.marker([e.latlng.lat, e.latlng.lng]).addTo(map)
        .bindPopup("📍 Selected Location")
        .openPopup();

});

// SUBMIT REPORT
document.getElementById("reportForm").addEventListener("submit", function(e){
    e.preventDefault();

    if(!selectedLatLng){
        alert("Please select location on map");
        return;
    }

    let desc = document.getElementById("description").value;
    let severity = document.getElementById("severity").value;

    fetch("http://127.0.0.1:5000/report", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            user_id: <?= $_SESSION['user_id']; ?>,
            description: desc,
            severity: severity,
            lat: selectedLatLng.lat,
            lng: selectedLatLng.lng
        })
    })
    .then(async res => {
        const data = await res.json();

        if (!res.ok) {
            console.error("Server error:", data);
            throw new Error(data.message || "API Error");
        }

        return data;
    })
    .then(data => {

        console.log("SUCCESS:", data);

        alert("Report Submitted Successfully!");

        document.getElementById("reportForm").reset();
        selectedLatLng = null;
    })
    .catch(err => {
        console.error("FAILED:", err);
        alert("Failed to submit report: " + err.message);
    });

});
</script>

</body>
</html>