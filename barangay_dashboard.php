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

<link rel="stylesheet" href="barangay.css">

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

<a id="nav-messages" onclick="switchTab('messages')">
<span><i class="fa-solid fa-message"></i> Messages</span>
<span class="count" id="msgCount">0</span>
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

<!-- MESSAGES -->
<div id="messages" class="hidden">

<div class="card">

<h2>Communication (Messages)</h2>

<div id="messageList" class="notification-container"></div>

</div>

<div class="card">

<input id="msgText" placeholder="Type message...">

<select id="receiver">
<option value="resident">Resident</option>
<option value="admin">Admin</option>
</select>

<button onclick="sendMessage()">Send</button>

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


<div id="reportList" class="notification-container"></div>

</div>

</div>

</div>

<script src="barangay.js" defer></script>

</body>
</html>