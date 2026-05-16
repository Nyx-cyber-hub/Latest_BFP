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

<title>Resident Incident Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    background: #0b1220;
}

/* SIDEBAR */
.sidebar {
    width: 340px;
    height: 100vh;
    background: #0f172a;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    color: white;
    position: fixed;
    overflow-y: auto;
}

.logo {
    font-size: 22px;
    font-weight: bold;
    color: #ef4444;
}

.card {
    background: #111c33;
    padding: 15px;
    border-radius: 12px;
}

/* FORM ELEMENTS */
input, select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: #1e293b;
    color: white;
    outline: none;
}

label {
    font-size: 12px;
    color: #cbd5e1;
}

/* BUTTONS */
button {
    padding: 10px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

.submit-btn {
    background: #ef4444;
    color: white;
}

.submit-btn:hover {
    background: #dc2626;
}

.logout {
    background: black;
    color: white;
    border: 1px solid #ef4444;
    width: 100%;
}

/* MAP */
#map {
    margin-left: 340px;
    width: calc(100% - 340px);
    height: 100vh;
}

/* CAMERA */
video {
    width: 100%;
    border-radius: 10px;
    background: black;
}

canvas {
    display: none;
}

#preview {
    width: 100%;
    border-radius: 10px;
    margin-top: 10px;
    border: 1px solid #334155;
}

.hidden {
    display: none;
}

.camera-btn {
    background: #3b82f6;
    color: white;
}

.capture-btn {
    background: #10b981;
    color: white;
}
</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">🚨 Incident Reporting</div>

    <div class="card">
        👤 <b><?= $_SESSION['full_name']; ?></b><br>
        <small style="color:#94a3b8;">Resident Account</small>
    </div>

    <div class="card">
        📍 Click map to select location
    </div>

    <!-- FORM -->
    <form id="reportForm" class="card" style="display:flex;flex-direction:column;gap:10px;">

        <label>Description</label>
        <input type="text" id="description" placeholder="Enter incident details..." required>

        <label>Severity</label>
        <select id="severity">
            <option value="1">Low</option>
            <option value="2">Medium</option>
            <option value="3">High</option>
        </select>

        <!-- CAMERA -->
        <label>Photo Evidence</label>

        <video id="camera" autoplay playsinline></video>

        <button type="button" id="openCamera" class="camera-btn">
            Open Camera
        </button>

        <button type="button" id="capturePhoto" class="capture-btn">
            Capture Photo
        </button>

        <canvas id="canvas"></canvas>

        <img id="preview" class="hidden">

        <button type="submit" class="submit-btn">Submit Report</button>

    </form>

    <a href="logout.php">
        <button class="logout">Logout</button>
    </a>

</div>

<!-- MAP -->
<div id="map"></div>

<script>
/* ================= MAP ================= */
let map = L.map('map').setView([13.8445, 121.2060], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Incident GIS System'
}).addTo(map);

let selectedLatLng = null;
let marker = null;

map.on('click', function(e) {
    selectedLatLng = e.latlng;

    if (marker) map.removeLayer(marker);

    marker = L.marker([e.latlng.lat, e.latlng.lng])
        .addTo(map)
        .bindPopup("Selected Location")
        .openPopup();
});

/* ================= CAMERA ================= */
let video = document.getElementById("camera");
let canvas = document.getElementById("canvas");
let capturedImage = null;

/* OPEN CAMERA */
document.getElementById("openCamera").addEventListener("click", async function() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" }
        });

        video.srcObject = stream;

    } catch (err) {
        alert("Camera access denied or not supported.");
        console.error(err);
    }
});

/* CAPTURE PHOTO */
document.getElementById("capturePhoto").addEventListener("click", function() {

    let ctx = canvas.getContext("2d");

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    capturedImage = canvas.toDataURL("image/png");

    let preview = document.getElementById("preview");
    preview.src = capturedImage;
    preview.classList.remove("hidden");
});

/* ================= SUBMIT ================= */
document.getElementById("reportForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    if (!selectedLatLng) {
        alert("Please select location on map");
        return;
    }

    let formData = new FormData();
    formData.append("user_id", <?= $_SESSION['user_id']; ?>);
    formData.append("description", document.getElementById("description").value);
    formData.append("severity", document.getElementById("severity").value);
    formData.append("lat", selectedLatLng.lat);
    formData.append("lng", selectedLatLng.lng);
    formData.append("type", "Accident Report");

    if (capturedImage) {
        formData.append("photo_base64", capturedImage);
    }

    try {
        const res = await fetch("http://localhost:5000/report", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (!res.ok) throw new Error(data.message || "Server error");

        alert("Report submitted successfully!");

        document.getElementById("reportForm").reset();
        document.getElementById("preview").classList.add("hidden");

        selectedLatLng = null;
        marker = null;

    } catch (err) {
        alert("Error: " + err.message);
    }
});
</script>

</body>
</html>