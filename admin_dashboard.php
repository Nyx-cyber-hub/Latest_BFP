<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BFP Admin Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<link rel="stylesheet"
href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<style>

/* BODY */
body{
    overflow:hidden;
    background:#f3f4f6;
}

/* SIDEBAR */
#sidebar{
    background:linear-gradient(180deg,#111,#000);
}

/* MENU */
.menuBtn{
    transition:0.2s ease;
    color:white;
    border-radius:14px;
    padding:14px;
}

.menuBtn:hover{
    background:rgba(255,255,255,0.08);
}

.menuBtn.active{
    background:#dc2626;
}

.menuText{
    transition:0.2s ease;
}

/* COLLAPSE */
.sidebar-collapsed .menuText{
    display:none;
}

.sidebar-collapsed .menuBtn{
    justify-content:center;
}

/* CARD */
.card{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-top:4px solid #dc2626;
}

/* MAP */
#map{
    height:100%;
    width:100%;
    border-radius:12px;
}

/* TABLE */
table{
    border-collapse:collapse;
}

th{
    background:black;
    color:white;
}

th,td{
    padding:14px;
}

/* BUTTONS */
.actionBtn{
    padding:6px 12px;
    border-radius:8px;
    color:white;
    font-size:13px;
    font-weight:bold;
}

.confirmBtn{
    background:#16a34a;
}

.rejectBtn{
    background:#dc2626;
}

/* SECTION */
.section{
    animation:fadeIn .25s ease;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

</head>

<body>

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <div id="sidebar"
    class="w-64 text-white flex flex-col transition-all duration-300">

        <!-- HEADER -->
        <div class="p-5 border-b border-gray-700 flex justify-between items-center">

            <div>
                <h1 class="text-2xl font-bold text-red-500">
                    BFP
                </h1>

                <p class="text-xs text-gray-400">
                    Rosario Batangas
                </p>
            </div>

            <button onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

        </div>

        <!-- MENU -->
        <div class="p-4 space-y-3 flex-1">

            <button onclick="showSection('home', this)"
            class="menuBtn active w-full flex items-center gap-3">

                <i class="fas fa-house"></i>
                <span class="menuText">Home</span>

            </button>

            <button onclick="showSection('reports', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-file-lines"></i>
                <span class="menuText">Reports</span>

            </button>

            <button onclick="showSection('gis', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-map-location-dot"></i>
                <span class="menuText">GIS Map</span>

            </button>

            <button onclick="showSection('notifications', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-bell"></i>
                <span class="menuText">Notifications</span>

            </button>

            <button onclick="showSection('settings', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-gear"></i>
                <span class="menuText">Settings</span>

            </button>

        </div>

        <!-- LOGOUT -->
        <div class="p-5 border-t border-gray-700">

            <a href="logout.php"
            class="block text-center bg-red-600 hover:bg-red-700 py-3 rounded-xl font-bold">

                Logout

            </a>

        </div>

    </div>

    <!-- MAIN -->
    <div class="flex-1 overflow-y-auto">

        <!-- TOPBAR -->
        <div class="bg-white border-b-4 border-red-600 p-5 shadow flex justify-between items-center">

            <div>

                <h1 id="pageTitle"
                class="text-2xl font-bold text-red-600">

                    Welcome <?= $_SESSION['full_name']; ?>

                </h1>

                <p class="text-gray-500 text-sm">
                    BFP Administrator Panel
                </p>

            </div>

            <div class="font-semibold text-gray-600">
                <?= strtoupper($_SESSION['role']); ?>
            </div>

        </div>

        <!-- HOME -->
        <div id="home" class="section p-6">

            <div class="grid grid-cols-4 gap-5">

                <div class="card">
                    <p class="text-gray-500">Total Reports</p>
                    <h1 id="totalReports" class="text-5xl font-bold mt-3">0</h1>
                </div>

                <div class="card">
                    <p class="text-gray-500">Pending</p>
                    <h1 id="pendingReports" class="text-5xl font-bold mt-3">0</h1>
                </div>

                <div class="card">
                    <p class="text-gray-500">Confirmed</p>
                    <h1 id="confirmedReports" class="text-5xl font-bold mt-3">0</h1>
                </div>

                <div class="card">
                    <p class="text-gray-500">Rejected</p>
                    <h1 id="rejectedReports" class="text-5xl font-bold mt-3">0</h1>
                </div>

            </div>

        </div>

        <!-- REPORTS -->
        <div id="reports" class="section hidden p-6">

            <div class="card">

                <h1 class="text-2xl font-bold mb-5">
                    Incident Reports
                </h1>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Description</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody id="reportTable">

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- GIS -->
        <div id="gis" class="section hidden p-6 h-full">

            <div class="card h-[700px] overflow-hidden">

                <div class="border-b pb-3 mb-3 font-bold text-xl">
                    GIS Emergency Map
                </div>

                <div id="map"></div>

            </div>

        </div>

        <!-- NOTIFICATIONS -->
        <div id="notifications" class="section hidden p-6">

            <div class="card">

                <h1 class="text-2xl font-bold mb-5">
                    Notifications
                </h1>

                <div id="notificationList" class="space-y-3">

                </div>

            </div>

        </div>

        <!-- SETTINGS -->
        <div id="settings" class="section hidden p-6">

            <div class="card">

                <h1 class="text-2xl font-bold mb-5">
                    Settings
                </h1>

                <select class="border p-3 rounded-xl">
                    <option>BFP Theme</option>
                </select>

            </div>

        </div>

    </div>

</div>

<script>

let activeBtn = null;
let map = null;
let markers = [];
let heatLayer = null;

// ======================
// ACTIVE BUTTON
// ======================
function setActive(btn){

    if(activeBtn){
        activeBtn.classList.remove('active');
    }

    btn.classList.add('active');
    activeBtn = btn;

}

// ======================
// SECTION SWITCH
// ======================
function showSection(section, btn){

    document.querySelectorAll('.section')
    .forEach(s => s.classList.add('hidden'));

    document.getElementById(section)
    .classList.remove('hidden');

    document.getElementById('pageTitle').innerText =
    section.charAt(0).toUpperCase() + section.slice(1);

    setActive(btn);

    if(section === "gis"){
        setTimeout(initMap, 200);
    }

}

// ======================
// SIDEBAR TOGGLE
// ======================
function toggleSidebar(){

    const sidebar = document.getElementById('sidebar');

    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-20');
    sidebar.classList.toggle('sidebar-collapsed');

}

// ======================
// LOAD REPORTS
// ======================
function loadReports(){

fetch("http://127.0.0.1:5000/reports")
.then(res => res.json())
.then(data => {

    // COUNTS
    document.getElementById("totalReports").innerText = data.length;

    document.getElementById("pendingReports").innerText =
    data.filter(r => r.status === "Pending").length;

    document.getElementById("confirmedReports").innerText =
    data.filter(r => r.status === "Confirmed").length;

    document.getElementById("rejectedReports").innerText =
    data.filter(r => r.status === "Rejected").length;

    // TABLE
    let table = document.getElementById("reportTable");
    table.innerHTML = "";

    // NOTIFICATIONS
    let notif = document.getElementById("notificationList");
    notif.innerHTML = "";

    data.forEach(r => {

        // TABLE
        table.innerHTML += `
        <tr class="border-b">

            <td>${r.id}</td>

            <td>${r.description}</td>

            <td>${r.severity}</td>

            <td>
                <span class="
                px-3 py-1 rounded-full text-white text-xs
                ${r.status === 'Pending' ? 'bg-yellow-500' :
                r.status === 'Confirmed' ? 'bg-green-600' :
                'bg-red-600'}
                ">
                    ${r.status}
                </span>
            </td>

            <td class="space-x-2">

                <button
                onclick="updateStatus(${r.id}, 'Confirmed')"
                class="actionBtn confirmBtn">

                    Confirm

                </button>

                <button
                onclick="updateStatus(${r.id}, 'Rejected')"
                class="actionBtn rejectBtn">

                    Reject

                </button>

            </td>

        </tr>
        `;

        // NOTIFICATIONS
        notif.innerHTML += `
        <div class="bg-gray-100 p-4 rounded-xl">

            🔥 ${r.description}
            <br>

            <span class="text-sm text-gray-500">
                Severity: ${r.severity}
            </span>

        </div>
        `;

    });

    // GIS
    updateMap(data);

});

}

// ======================
// UPDATE STATUS
// ======================
function updateStatus(id, status){

fetch("http://127.0.0.1:5000/update_status", {

    method:"POST",

    headers:{
        "Content-Type":"application/json"
    },

    body:JSON.stringify({
        id:id,
        status:status
    })

})
.then(res => res.json())
.then(data => {

    console.log(data);

    if(data.success){

        alert("Status Updated");

        loadReports();

    }else{

        alert(data.message);

    }

})
.catch(err => {

    console.error(err);

    alert("Server Error");

});

}

// ======================
// INIT MAP
// ======================
function initMap(){

    if(!map){

        map = L.map('map').setView([13.8445, 121.2060], 13);

        L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:'BFP GIS'
        }).addTo(map);

    }

    setTimeout(() => map.invalidateSize(), 200);

}

// ======================
// UPDATE MAP
// ======================
function updateMap(data){

    if(!map) return;

    markers.forEach(m => map.removeLayer(m));
    markers = [];

    let heatPoints = [];

    data
    .filter(r => r.status === "Confirmed")
    .forEach(r => {

        let color =
        r.severity === "High" ? "red" :
        r.severity === "Medium" ? "orange" :
        "green";

        let marker = L.circleMarker([r.lat, r.lng], {

            radius:8,
            color:color,
            fillOpacity:0.8

        })
        .addTo(map)
        .bindPopup(
            "<b>🔥 Incident</b><br>" +
            r.description +
            "<br>Status: " + r.status
        );

        markers.push(marker);

        let intensity =
        r.severity === "High" ? 1 :
        r.severity === "Medium" ? 0.6 : 0.3;

        heatPoints.push([r.lat, r.lng, intensity]);

    });

    if(heatLayer){
        map.removeLayer(heatLayer);
    }

    heatLayer = L.heatLayer(heatPoints, {
        radius:25,
        blur:15
    }).addTo(map);

}

// ======================
// INIT
// ======================
window.onload = function(){

    const homeBtn = document.querySelector(".menuBtn");

    showSection('home', homeBtn);

    loadReports();

    setInterval(loadReports, 5000);

}

</script>

</body>
</html>
