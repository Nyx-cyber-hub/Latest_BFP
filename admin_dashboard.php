<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != "admin"
) {
    header("Location: login.php");
    exit();
}

$full_name = $_SESSION['full_name'] ?? 'Administrator';
$role = $_SESSION['role'] ?? 'admin';
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

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef2ff;
    overflow:hidden;
}

.hidden{
    display:none;
}

/* SIDEBAR */

#sidebar{
    width:280px;
    background:linear-gradient(180deg,#0f172a,#1e293b);
    box-shadow:0 0 30px rgba(0,0,0,0.15);
}

.logo{
    font-size:30px;
    font-weight:700;
    color:#ef4444;
}

.menuBtn{
    width:100%;
    border:none;
    padding:16px;
    border-radius:18px;
    background:rgba(255,255,255,0.06);
    color:white;
    display:flex;
    align-items:center;
    gap:14px;
    cursor:pointer;
    transition:0.3s;
    font-size:15px;
    font-weight:500;
}

.menuBtn:hover{
    background:#dc2626;
    transform:translateX(5px);
}

.menuBtn.active{
    background:#dc2626;
}

/* MAIN */

.main-content{
    flex:1;
    overflow-y:auto;
}

/* TOPBAR */

.topbar{
    background:white;
    padding:25px 35px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    border-bottom:4px solid #dc2626;
}

.profile{
    background:#f8fafc;
    padding:10px 18px;
    border-radius:18px;
    display:flex;
    align-items:center;
    gap:15px;
}

.profile img{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
}

/* CARDS */

.card{
    background:white;
    border-radius:24px;
    padding:28px;
    box-shadow:0 10px 35px rgba(0,0,0,0.06);
}

.stats-card{
    position:relative;
    overflow:hidden;
}

.stats-card::before{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:6px;
    background:#dc2626;
}

.stats-card h1{
    font-size:48px;
    margin-top:15px;
}

/* NOTIFICATIONS */

.notification-card{
    background:#f8fafc;
    border-left:5px solid #dc2626;
    padding:18px;
    border-radius:18px;
    margin-bottom:15px;
    transition:0.3s;
}

.notification-card:hover{
    background:#fef2f2;
}

/* REPORTS */

.report-card{
    background:#f8fafc;
    padding:20px;
    border-radius:18px;
    margin-bottom:18px;
    border:1px solid #e2e8f0;
}

.status{
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

.pending{
    background:#fef3c7;
    color:#b45309;
}

.confirmed{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

.action-btn{
    border:none;
    padding:10px 15px;
    border-radius:12px;
    color:white;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
}

.confirm-btn{
    background:#16a34a;
}

.reject-btn{
    background:#dc2626;
}

/* FORM */

input,
textarea,
select{
    width:100%;
    border:none;
    background:#f1f5f9;
    padding:16px;
    border-radius:15px;
    outline:none;
    font-size:15px;
}

textarea{
    resize:none;
}

.primary-btn{
    background:#dc2626;
    color:white;
    border:none;
    padding:15px;
    border-radius:16px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.primary-btn:hover{
    background:#b91c1c;
}

/* MAP */

#map{
    width:100%;
    height:650px;
    border-radius:20px;
    z-index:1;
}

/* MESSAGE */

.message-card{
    background:#f8fafc;
    padding:18px;
    border-radius:18px;
    border-left:5px solid #2563eb;
    margin-bottom:15px;
}

/* SETTINGS */

.setting-item{
    background:#f8fafc;
    padding:20px;
    border-radius:18px;
    margin-bottom:15px;
}

/* MOBILE */

@media(max-width:900px){

    body{
        flex-direction:column;
    }

    #sidebar{
        width:100%;
    }

    .topbar{
        flex-direction:column;
        gap:20px;
        align-items:flex-start;
    }

}

</style>

</head>

<body>

<div class="flex h-screen">

    <!-- SIDEBAR -->

    <div id="sidebar" class="text-white flex flex-col p-5 gap-4">

        <div class="mb-5">

            <h1 class="logo">BFP</h1>

            <p class="text-slate-400 text-sm mt-1">
                Rosario Batangas
            </p>

        </div>

        <button onclick="showSection('home',this)" class="menuBtn active">
            <i class="fas fa-house"></i>
            Dashboard
        </button>

        <button onclick="showSection('notifications',this)" class="menuBtn">
            <i class="fas fa-bell"></i>
            Notifications
        </button>

        <button onclick="showSection('reports',this)" class="menuBtn">
            <i class="fas fa-file-lines"></i>
            Reports
        </button>

        <button onclick="showSection('gis',this)" class="menuBtn">
            <i class="fas fa-map-location-dot"></i>
            GIS Map
        </button>

        <button onclick="showSection('prediction',this)" class="menuBtn">
            <i class="fas fa-brain"></i>
            Prediction
        </button>

        <button onclick="showSection('communication',this)" class="menuBtn">
            <i class="fas fa-comments"></i>
            Communication
        </button>

        <button onclick="showSection('settings',this)" class="menuBtn">
            <i class="fas fa-gear"></i>
            Settings
        </button>

        <a href="logout.php"
        class="mt-auto bg-red-600 hover:bg-red-700 text-center py-4 rounded-2xl font-bold transition">

            Logout

        </a>

    </div>

    <!-- MAIN -->

    <div class="main-content">

        <!-- TOPBAR -->

        <div class="topbar">

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Welcome <?= htmlspecialchars($full_name); ?>
                </h1>

                <p class="text-slate-500 mt-1">
                    Bureau of Fire Protection Administrator Panel
                </p>

            </div>

            <div class="profile">

                <img src="https://i.imgur.com/2DhmtJ4.png">

                <div>
                    <h3 class="font-bold text-slate-800">
                        <?= strtoupper(htmlspecialchars($role)); ?>
                    </h3>
                    <p class="text-slate-500 text-sm">
                        System Administrator
                    </p>
                </div>

            </div>

        </div>

        <!-- HOME -->

        <div id="home" class="section p-8">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                <div class="card stats-card">
                    <p class="text-slate-500">Total Reports</p>
                    <h1 id="totalReports">0</h1>
                </div>

                <div class="card stats-card">
                    <p class="text-slate-500">Pending Reports</p>
                    <h1 id="pendingReports" class="text-yellow-500">0</h1>
                </div>

                <div class="card stats-card">
                    <p class="text-slate-500">Confirmed Reports</p>
                    <h1 id="confirmedReports" class="text-green-600">0</h1>
                </div>

                <div class="card stats-card">
                    <p class="text-slate-500">Rejected Reports</p>
                    <h1 id="rejectedReports" class="text-red-600">0</h1>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="card">

                    <h2 class="text-2xl font-bold mb-5 text-slate-800">
                        Emergency Operations Overview
                    </h2>

                    <p class="text-slate-500 leading-8">
                        The BFP Emergency Monitoring Dashboard provides
                        real-time management of incident reports,
                        GIS-based monitoring, predictive analytics,
                        inter-agency communication, and emergency
                        coordination between residents, barangay,
                        and fire personnel.
                    </p>

                </div>

                <div class="card">

                    <h2 class="text-2xl font-bold mb-5 text-slate-800">
                        Recent Activity
                    </h2>

                    <div class="space-y-4">

                        <div class="notification-card">
                            🔥 Fire incident reported near public market.
                        </div>

                        <div class="notification-card">
                            🚒 Responder team dispatched to Zone 3.
                        </div>

                        <div class="notification-card">
                            📍 GIS hotspot updated successfully.
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- NOTIFICATIONS -->

        <div id="notifications" class="section hidden p-8">

            <div class="card">

                <h1 class="text-3xl font-bold mb-6 text-slate-800">
                    Notifications
                </h1>

                <div id="notificationList">

                    <div class="notification-card">
                        🔔 New fire incident submitted by Resident.
                    </div>

                    <div class="notification-card">
                        🔔 Barangay confirmed emergency verification.
                    </div>

                    <div class="notification-card">
                        🔔 Emergency response team deployed.
                    </div>

                    <div class="notification-card">
                        🔔 Hazard zone monitoring updated.
                    </div>

                </div>

            </div>

        </div>

        <!-- REPORTS -->

        <div id="reports" class="section hidden p-8">

            <div class="card">

                <div class="flex justify-between items-center mb-6">

                    <h1 class="text-3xl font-bold text-slate-800">
                        Incident Reports
                    </h1>

                    <input
                    type="text"
                    id="searchInput"
                    placeholder="Search reports..."
                    class="w-72">

                </div>

                <div id="reportList">

                    <div class="report-card">

                        <div class="flex justify-between items-center mb-4">

                            <h2 class="text-xl font-bold text-slate-800">
                                Fire Incident Report
                            </h2>

                            <span class="status pending">
                                PENDING
                            </span>

                        </div>

                        <p class="text-slate-500 mb-4">
                            Reported fire near residential area requiring immediate verification.
                        </p>

                        <div class="flex gap-3">

                            <button class="action-btn confirm-btn">
                                Confirm
                            </button>

                            <button class="action-btn reject-btn">
                                Reject
                            </button>

                        </div>

                    </div>

                    <div class="report-card">

                        <div class="flex justify-between items-center mb-4">

                            <h2 class="text-xl font-bold text-slate-800">
                                Electrical Hazard Report
                            </h2>

                            <span class="status confirmed">
                                CONFIRMED
                            </span>

                        </div>

                        <p class="text-slate-500 mb-4">
                            Barangay confirmed exposed electrical wiring.
                        </p>

                    </div>

                    <div class="report-card">

                        <div class="flex justify-between items-center mb-4">

                            <h2 class="text-xl font-bold text-slate-800">
                                Smoke Monitoring Report
                            </h2>

                            <span class="status rejected">
                                REJECTED
                            </span>

                        </div>

                        <p class="text-slate-500 mb-4">
                            Investigation showed no fire hazard present.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- GIS -->

        <div id="gis" class="section hidden p-8">

            <div class="card">

                <div class="flex justify-between items-center mb-6">

                    <h1 class="text-3xl font-bold text-slate-800">
                        GIS Emergency Monitoring
                    </h1>

                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded-xl font-bold">
                        LIVE MAP ACTIVE
                    </div>

                </div>

                <div id="map"></div>

            </div>

        </div>

        <!-- PREDICTION -->

        <div id="prediction" class="section hidden p-8">

            <div class="card">

                <div class="flex justify-between items-center mb-6">

                    <div>

                        <h1 class="text-3xl font-bold text-slate-800">
                            AI Predictive Analytics
                        </h1>

                        <p class="text-slate-500 mt-2">
                            Advanced Decision Tree Risk Assessment Module
                        </p>

                    </div>

                    <div class="bg-red-100 text-red-600 px-5 py-3 rounded-2xl font-bold">
                        MACHINE LEARNING ACTIVE
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div>

                        <label class="font-semibold text-slate-700">
                            Severity Level
                        </label>

                        <select id="severity" class="mt-2 mb-5">

                            <option value="1">Low Severity</option>
                            <option value="2">Medium Severity</option>
                            <option value="3">High Severity</option>

                        </select>

                        <label class="font-semibold text-slate-700">
                            Incident Frequency
                        </label>

                        <input
                        type="number"
                        id="incident_count"
                        placeholder="Enter incident count"
                        class="mt-2 mb-5">

                        <button
                        onclick="predictRisk()"
                        class="primary-btn w-full">

                            Run AI Prediction

                        </button>

                    </div>

                    <div class="bg-slate-50 rounded-3xl p-10 flex flex-col justify-center items-center">

                        <i class="fas fa-brain text-7xl text-red-600 mb-6"></i>

                        <h2 class="text-3xl font-bold mb-4 text-slate-800">
                            Prediction Result
                        </h2>

                        <div id="predictionResult"
                        class="text-5xl font-bold text-red-600">

                            WAITING...

                        </div>

                        <p class="text-slate-500 mt-5 text-center leading-7">
                            The AI system analyzes severity and incident
                            frequency to estimate possible fire risk levels.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- COMMUNICATION -->

        <div id="communication" class="section hidden p-8">

            <div class="card">

                <h1 class="text-3xl font-bold mb-6 text-slate-800">
                    Emergency Communication Module
                </h1>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div>

                        <label class="font-semibold text-slate-700">
                            Receiver
                        </label>

                        <input
                type="text"
                id="receiver"
                placeholder="Enter receiver (e.g. Barangay, BFP Personnel, Response Team)"
                class="mt-2 mb-5" />

                        <label class="font-semibold text-slate-700">
                            Message
                        </label>

                        <textarea
                        rows="7"
                        id="message"
                        class="mt-2 mb-5"
                        placeholder="Type emergency coordination message..."></textarea>

                        <button class="primary-btn w-full">
                            Send Message
                        </button>

                    </div>

                    <div>

                        <h2 class="text-2xl font-bold mb-5 text-slate-800">
                            Live Communication Feed
                        </h2>

                        <div class="message-card">
                            <strong>Barangay Officer:</strong><br>
                            Fire responders are now en route to Zone 2.
                        </div>

                        <div class="message-card">
                            <strong>BFP Team:</strong><br>
                            Emergency vehicles dispatched successfully.
                        </div>

                        <div class="message-card">
                            <strong>Resident:</strong><br>
                            Smoke visibility increasing near highway area.
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- SETTINGS -->

        <div id="settings" class="section hidden p-8">

            <div class="card">

                <h1 class="text-3xl font-bold mb-6 text-slate-800">
                    System Settings
                </h1>

                <div class="setting-item">

                    <h2 class="text-xl font-bold mb-2 text-slate-800">
                        System Information
                    </h2>

                    <p class="text-slate-500 leading-7">
                        BFP Emergency Response and GIS Monitoring System Version 1.0
                    </p>

                </div>

                <div class="setting-item">

                    <h2 class="text-xl font-bold mb-2 text-slate-800">
                        Account Security
                    </h2>

                    <p class="text-slate-500 leading-7">
                        Manage administrator access and authentication settings.
                    </p>

                </div>

                <div class="setting-item">

                    <h2 class="text-xl font-bold mb-2 text-slate-800">
                        GIS Configuration
                    </h2>

                    <p class="text-slate-500 leading-7">
                        Configure GIS map layers and emergency hotspot monitoring.
                    </p>

                </div>

                <div class="setting-item">

                    <h2 class="text-xl font-bold mb-2 text-slate-800">
                        AI Prediction Settings
                    </h2>

                    <p class="text-slate-500 leading-7">
                        Configure Decision Tree predictive analytics thresholds.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

const API_URL = 'http://127.0.0.1:5000';
const ROSARIO_BATANGAS = [13.8458,121.1990];

/* SECTION SWITCH */

function showSection(sectionId, btn){

    document.querySelectorAll('.section').forEach(section=>{
        section.classList.add('hidden');
    });

    document.getElementById(sectionId)
    .classList.remove('hidden');

    document.querySelectorAll('.menuBtn').forEach(button=>{
        button.classList.remove('active');
    });

    btn.classList.add('active');

    if(sectionId === 'gis'){

        setTimeout(()=>{
            map.invalidateSize();
        },300);

    }

}

/* GIS MAP */

var map = L.map('map').setView(ROSARIO_BATANGAS,14);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    attribution:'© OpenStreetMap contributors'
}).addTo(map);

let markers = [];
let heatPoints = [];

/* LOAD REPORTS */

async function loadReports(){

    try{

        const response = await fetch(`${API_URL}/reports`);

        const reports = await response.json();

        let reportHTML = '';
        let notifHTML = '';

        let total = reports.length;
        let pending = 0;
        let confirmed = 0;
        let rejected = 0;

        markers.forEach(marker=>{
            map.removeLayer(marker);
        });

        markers = [];

        reports.forEach(report=>{

            let status = (report.status || 'Pending').toUpperCase();

            if(status === 'PENDING') pending++;
            if(status === 'CONFIRMED') confirmed++;
            if(status === 'REJECTED') rejected++;

            let badge = 'pending';

            if(status === 'CONFIRMED'){
                badge = 'confirmed';
            }

            if(status === 'REJECTED'){
                badge = 'rejected';
            }

            /* REPORTS */

            reportHTML += `

                <div class="report-card">

                    <div class="flex justify-between items-center mb-4">

                        <h2 class="text-xl font-bold text-slate-800">
                            ${report.type || 'Incident Report'}
                        </h2>

                        <span class="status ${badge}">
                            ${status}
                        </span>

                    </div>

                    <p class="text-slate-500 mb-3">
                        ${report.description}
                    </p>

                    <p class="text-sm text-slate-400 mb-4">
                        Reported by: ${report.full_name || 'Resident'}
                    </p>

                    ${status === 'PENDING' ? `

                    <div class="flex gap-3">

                        <button
                        onclick="updateStatus(${report.id},'CONFIRMED')"
                        class="action-btn confirm-btn">

                            Confirm

                        </button>

                        <button
                        onclick="updateStatus(${report.id},'REJECTED')"
                        class="action-btn reject-btn">

                            Reject

                        </button>

                    </div>

                    ` : ''}

                </div>

            `;

            /* NOTIFICATIONS */

            if(status === 'PENDING'){

                notifHTML += `

                    <div class="notification-card">
                        🔔 New ${report.type || 'incident'} report submitted by
                        ${report.full_name || 'Resident'}.
                    </div>

                `;

            }

            /* MAP */

            if(report.lat && report.lng){

                let marker = L.marker([
                    parseFloat(report.lat),
                    parseFloat(report.lng)
                ])
                .addTo(map)
                .bindPopup(`
                    <strong>${report.type || 'Incident'}</strong><br>
                    ${report.description}<br>
                    Status: ${status}
                `);

                markers.push(marker);

            }

        });

        document.getElementById('reportList').innerHTML = reportHTML;

        document.getElementById('notificationList').innerHTML =
            notifHTML || `
                <div class="notification-card">
                    No pending notifications.
                </div>
            `;

        document.getElementById('totalReports').innerText = total;
        document.getElementById('pendingReports').innerText = pending;
        document.getElementById('confirmedReports').innerText = confirmed;
        document.getElementById('rejectedReports').innerText = rejected;

    }

    catch(error){

        console.log(error);

    }

}

/* UPDATE STATUS */

async function updateStatus(id,status){

    const response = await fetch(`${API_URL}/update_status`,{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            id:id,
            status:status
        })

    });

    const data = await response.json();

    alert(data.message);

    loadReports();

}

/* AI PREDICTION */

async function predictRisk(){

    const severity =
        document.getElementById('severity').value;

    const incident_count =
        document.getElementById('incident_count').value;

    const response = await fetch(
        `${API_URL}/predict_risk`,
        {
            method:'POST',
            headers:{
                'Content-Type':'application/json'
            },
            body:JSON.stringify({
                severity,
                incident_count
            })
        }
    );

    const data = await response.json();

    let density = document.getElementById('density').value;
let weather = document.getElementById('weather').value;
let advancedRisk = data.prediction;

if(
    severity == 3 &&
    incident_count >= 10 &&
    density == 'HIGH' &&
    (weather == 'HOT' || weather == 'DRY')
){
    advancedRisk = 'EXTREME RISK';
}

document.getElementById('predictionResult')
.innerText = advancedRisk;

}

/* SEND MESSAGE */

async function sendMessage(){

    const receiver =
        document.getElementById('receiver').value;

    const message =
        document.getElementById('message').value;

    const response = await fetch(`${API_URL}/send_message`,{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            sender:'Admin',
            receiver,
            message
        })

    });

    const data = await response.json();

    alert(data.message);

    document.getElementById('message').value = '';

    loadMessages();

}

/* LOAD MESSAGES */

async function loadMessages(){

    const response = await fetch(`${API_URL}/messages`);

    const messages = await response.json();

    let html = '';

    messages.forEach(msg=>{

        html += `

            <div class="message-card">

                <strong>${msg.sender}</strong><br>

                To: ${msg.receiver}<br><br>

                ${msg.message}

            </div>

        `;

    });

    document.getElementById('messageList').innerHTML = html;

}

/* SEARCH */

document.getElementById('searchInput')
.addEventListener('keyup',function(){

    const value = this.value.toLowerCase();

    document.querySelectorAll('.report-card')
    .forEach(card=>{

        card.style.display =
            card.innerText.toLowerCase().includes(value)
            ? 'block'
            : 'none';

    });

});

/* INITIAL LOAD */

loadReports();
loadMessages();

setInterval(()=>{
    loadReports();
    loadMessages();
},5000);

</script>

</body>
</html>