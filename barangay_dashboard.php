<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "barangay") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Barangay Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f3f4f6;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background:#111827;
    color:white;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    display:flex;
    flex-direction:column;
    border-right:4px solid #dc2626;
}

.logo{
    padding:25px 20px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

.logo-top{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:22px;
    font-weight:bold;
    color:#ef4444;
}

.logo-sub{
    font-size:13px;
    margin-top:8px;
    color:#d1d5db;
}

/* MENU */
.menu{
    padding:15px 10px;
    display:flex;
    flex-direction:column;
    height:100%;
}

.top-menu{
    display:flex;
    flex-direction:column;
}

.bottom-menu{
    margin-top:auto;
}

/* SIDEBAR BUTTONS */
.menu a{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 15px;
    color:white;
    cursor:pointer;
    border-radius:14px;
    margin-bottom:10px;
    transition:0.3s;
    font-weight:bold;
    text-decoration:none;
}

.menu a:hover{
    background:#1f2937;
}

.menu a.active{
    background:white;
    color:#111827;
}

.logout-btn{
    background:#dc2626;
}

.logout-btn:hover{
    background:#b91c1c !important;
    color:white !important;
}

/* MAIN */
.main{
    margin-left:260px;
    padding:30px;
}

/* DASHBOARD BANNER */
.dashboard-banner{
    background:linear-gradient(135deg,#111827,#1f2937);
    color:white;
    border-radius:25px;
    padding:35px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    position:relative;
    overflow:hidden;
}

.dashboard-banner::after{
    content:'';
    position:absolute;
    width:260px;
    height:260px;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    right:-80px;
    top:-80px;
}

.banner-left{
    position:relative;
    z-index:2;
}

.banner-badge{
    background:rgba(239,68,68,0.2);
    color:#fca5a5;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 15px;
    border-radius:50px;
    font-size:13px;
    margin-bottom:18px;
    font-weight:bold;
}

.dashboard-banner h1{
    font-size:38px;
    margin-bottom:10px;
}

.dashboard-banner p{
    color:#d1d5db;
    max-width:520px;
    line-height:1.7;
}

.banner-date{
    margin-top:20px;
    display:flex;
    align-items:center;
    gap:10px;
    color:#f3f4f6;
    font-size:15px;
}

.banner-right{
    position:relative;
    z-index:2;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:20px;
}

.live-circle{
    width:110px;
    height:110px;
    border-radius:50%;
    background:#22c55e;
    box-shadow:0 0 25px #22c55e;
    animation:pulse 1.5s infinite;
}

.live-box{
    background:#22c55e;
    color:white;
    padding:10px 18px;
    border-radius:14px;
    font-weight:bold;
    display:flex;
    align-items:center;
    gap:8px;
}

@keyframes pulse{

0%{
transform:scale(1);
opacity:1;
}

50%{
transform:scale(1.1);
opacity:0.7;
}

100%{
transform:scale(1);
opacity:1;
}

}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.stat-card{
    background:white;
    padding:25px;
    border-radius:22px;
    position:relative;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    transition:0.3s;
}

.stat-card:hover{
    transform:translateY(-5px);
}

.stat-card::after{
    content:'';
    position:absolute;
    width:120px;
    height:120px;
    border-radius:50%;
    top:-40px;
    right:-40px;
    background:rgba(0,0,0,0.03);
}

.stat-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.stat-icon{
    width:60px;
    height:60px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:24px;
    color:white;
}

.pending-bg{
    background:#eab308;
}

.confirmed-bg{
    background:#16a34a;
}

.rejected-bg{
    background:#dc2626;
}

.stat-card h3{
    margin-top:20px;
    color:#6b7280;
    font-size:16px;
}

.stat-card h1{
    font-size:42px;
    margin-top:10px;
    color:#111827;
}

/* CARD */
.card{
    background:white;
    border-radius:20px;
    padding:22px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    margin-bottom:20px;
}

/* SEARCH */
.searchbar{
    width:100%;
    padding:13px;
    border-radius:14px;
    border:1px solid #d1d5db;
    margin:20px 0;
    outline:none;
    font-size:14px;
}

.searchbar:focus{
    border-color:#16a34a;
}

/* COUNTS */
.count{
    background:#dc2626;
    color:white;
    padding:3px 9px;
    border-radius:50px;
    font-size:12px;
}

/* HIDDEN */
.hidden{
    display:none;
}

/* NOTIFICATIONS */
.notification-container{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.notification-card{
    background:white;
    border-radius:20px;
    padding:22px;
    border-left:6px solid #dc2626;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    transition:0.3s;
}

.notification-card:hover{
    transform:translateY(-3px);
}

.notification-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.notification-type{
    font-size:20px;
    font-weight:bold;
    color:#111827;
}

.notification-user{
    color:#4b5563;
    margin-bottom:10px;
}

.notification-desc{
    color:#374151;
    line-height:1.6;
    margin-top:10px;
}

.notification-date{
    margin-top:12px;
    color:#6b7280;
    font-size:13px;
}

/* BUTTONS */
.notification-actions{
    display:flex;
    gap:10px;
    margin-top:18px;
}

.btn{
    padding:11px 18px;
    border:none;
    border-radius:12px;
    color:white;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.03);
}

.confirm{
    background:#16a34a;
}

.reject{
    background:#dc2626;
}

/* BADGES */
.badge{
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:bold;
    display:inline-block;
    margin-top:15px;
}

.confirmed{
    background:#16a34a;
    color:white;
}

.rejected{
    background:#dc2626;
    color:white;
}

/* SEVERITY */
.severity{
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:bold;
    color:white;
}

.low{
    background:#22c55e;
}

.medium{
    background:#eab308;
    color:black;
}

.high{
    background:#f97316;
}

.critical{
    background:#dc2626;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="logo">

<div class="logo-top">
<i class="fa-solid fa-fire"></i>
<span>BFP SYSTEM</span>
</div>

<div class="logo-sub">
Barangay Panel
</div>

</div>

<div class="menu">

<div class="top-menu">

<a id="nav-home" class="active" onclick="switchTab('home')">
<span><i class="fa-solid fa-house"></i> Home</span>
</a>

<a id="nav-notif" onclick="switchTab('notifications')">
<span><i class="fa-solid fa-bell"></i> Notifications</span>
<span class="count" id="notifCount">0</span>
</a>

<a id="nav-reports" onclick="switchTab('reports')">
<span><i class="fa-solid fa-folder"></i> Reports</span>
<span class="count" id="reportCount">0</span>
</a>

</div>

<div class="bottom-menu">

<a href="logout.php" class="logout-btn">
<span><i class="fa-solid fa-right-from-bracket"></i> Logout</span>
</a>

</div>

</div>

</div>

<!-- MAIN -->
<div class="main">

<!-- HOME -->
<div id="home">

<div class="dashboard-banner">

<div class="banner-left">

<div class="banner-badge">
<i class="fa-solid fa-tower-broadcast"></i>
REALTIME MONITORING
</div>

<h1>Barangay Dashboard</h1>

<p>
Monitor emergency reports, notifications, and incident activities in real-time.
</p>

<div class="banner-date">
<i class="fa-solid fa-calendar-days"></i>
<span id="currentDate"></span>
</div>

</div>

<div class="banner-right">

<div class="live-circle"></div>

<div class="live-box">
<i class="fa-solid fa-signal"></i>
LIVE SYSTEM
</div>

</div>

</div>

<div class="stats">

<div class="stat-card">

<div class="stat-top">
<div class="stat-icon pending-bg">
<i class="fa-solid fa-clock"></i>
</div>
</div>

<h3>Pending Reports</h3>
<h1 id="statPending">0</h1>

</div>

<div class="stat-card">

<div class="stat-top">
<div class="stat-icon confirmed-bg">
<i class="fa-solid fa-circle-check"></i>
</div>
</div>

<h3>Confirmed Reports</h3>
<h1 id="statConfirmed">0</h1>

</div>

<div class="stat-card">

<div class="stat-top">
<div class="stat-icon rejected-bg">
<i class="fa-solid fa-circle-xmark"></i>
</div>
</div>

<h3>Rejected Reports</h3>
<h1 id="statRejected">0</h1>

</div>

</div>

</div>

<!-- NOTIFICATIONS -->
<div id="notifications" class="hidden">

<div class="card">

<h2>Pending Notifications</h2>

<div id="notifList" class="notification-container"></div>

</div>

</div>

<!-- REPORTS -->
<div id="reports" class="hidden">

<div class="card">

<h2>Records / Reports</h2>

<input 
class="searchbar" 
id="searchInput" 
placeholder="Search report type, severity, reporter..."
onkeyup="filterReports()"
>

<div id="reportList" class="notification-container"></div>

</div>

</div>

</div>

<script>

let data = [];

/* LOAD */
function load(){

fetch("http://127.0.0.1:5000/reports")
.then(res => res.json())
.then(res => {

data = res;
render();

});

}

/* RENDER */
function render(){

home();
notif();
reports();
counts();

}

/* HOME */
function home(){

document.getElementById("statPending").innerText =
data.filter(r => r.status === "Pending").length;

document.getElementById("statConfirmed").innerText =
data.filter(r => r.status === "CONFIRMED").length;

document.getElementById("statRejected").innerText =
data.filter(r => r.status === "REJECTED").length;

}

/* SEVERITY */
function severityClass(severity){

if(!severity) return "medium";

severity = severity.toLowerCase();

if(severity === "low") return "low";
if(severity === "medium") return "medium";
if(severity === "high") return "high";
if(severity === "critical") return "critical";

return "medium";

}

/* FORMAT DATE */
function formatDate(date){

if(!date) return "No Date";

return new Date(date).toLocaleString();

}

/* NOTIFICATIONS */
function notif(){

let box = document.getElementById("notifList");

box.innerHTML = "";

let pending = data.filter(r => r.status === "Pending");

if(pending.length === 0){

box.innerHTML = `
<div class="card">
No pending notifications.
</div>
`;

return;

}

pending.forEach(r => {

box.innerHTML += `

<div class="notification-card">

<div class="notification-top">

<div class="notification-type">
${r.type || formatDate(r.created_at)}
</div>

<span class="severity ${severityClass(r.severity)}">
${r.severity || "Medium"}
</span>

</div>

<div class="notification-user">
<b>Reporter:</b> ${r.full_name}
</div>

<div class="notification-desc">
${r.description}
</div>

<div class="notification-date">
<i class="fa-solid fa-clock"></i>
${formatDate(r.created_at)}
</div>

<div class="notification-actions">

<button class="btn confirm" onclick="update(${r.id}, 'CONFIRMED')">
<i class="fa-solid fa-check"></i> Confirm
</button>

<button class="btn reject" onclick="update(${r.id}, 'REJECTED')">
<i class="fa-solid fa-xmark"></i> Reject
</button>

</div>

</div>

`;

});

}

/* REPORTS */
function reports(){

let box = document.getElementById("reportList");

box.innerHTML = "";

let reports = data.filter(r => r.status !== "Pending");

if(reports.length === 0){

box.innerHTML = `
<div class="card">
No reports available.
</div>
`;

return;

}

reports.forEach(r => {

box.innerHTML += `

<div class="notification-card">

<div class="notification-top">

<div class="notification-type">
${r.type || formatDate(r.created_at)}
</div>

<span class="severity ${severityClass(r.severity)}">
${r.severity || "Medium"}
</span>

</div>

<div class="notification-user">
<b>Reporter:</b> ${r.full_name}
</div>

<div class="notification-desc">
${r.description}
</div>

<div class="notification-date">
<i class="fa-solid fa-clock"></i>
${formatDate(r.created_at)}
</div>

<span class="badge ${r.status === 'CONFIRMED' ? 'confirmed' : 'rejected'}">
${r.status}
</span>

</div>

`;

});

}

/* COUNTS */
function counts(){

document.getElementById("notifCount").innerText =
data.filter(r => r.status === "Pending").length;

document.getElementById("reportCount").innerText =
data.filter(r => r.status !== "Pending").length;

}

/* SEARCH */
function filterReports(){

let input = document.getElementById("searchInput").value.toLowerCase();

document.querySelectorAll("#reportList .notification-card").forEach(card => {

card.style.display =
card.innerText.toLowerCase().includes(input)
? "block"
: "none";

});

}

/* UPDATE */
function update(id, status){

fetch("http://127.0.0.1:5000/update_status",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({
id:id,
status:status
})

})
.then(() => load());

}

/* SWITCH TAB */
function switchTab(tab){

document.querySelectorAll(".top-menu a").forEach(btn => {
btn.classList.remove("active");
});

document.getElementById("home").classList.add("hidden");
document.getElementById("notifications").classList.add("hidden");
document.getElementById("reports").classList.add("hidden");

document.getElementById(tab).classList.remove("hidden");

if(tab === "home"){
document.getElementById("nav-home").classList.add("active");
}

if(tab === "notifications"){
document.getElementById("nav-notif").classList.add("active");
}

if(tab === "reports"){
document.getElementById("nav-reports").classList.add("active");
}

}

/* REALTIME DATE */
function updateDate(){

const now = new Date();

document.getElementById("currentDate").innerText =
now.toLocaleString();

}

updateDate();

setInterval(updateDate,1000);

/* INIT */
load();

setInterval(load, 4000);

</script>

</body>
</html>