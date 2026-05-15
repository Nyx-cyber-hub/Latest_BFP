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

<link rel="stylesheet" href="admin.css">

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

            <button onclick="showSection('notifications', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-bell"></i>
                <span class="menuText">Notifications</span>

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

            <button onclick="showSection('prediction', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-brain"></i>
                <span class="menuText">Prediction</span>

            </button>

            <button onclick="showSection('communication', this)"
            class="menuBtn w-full flex items-center gap-3">

                <i class="fas fa-comments"></i>
                <span class="menuText">Communication</span>

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


        <!-- PREDICTION -->
<div id="prediction" class="section hidden p-6">

    <div class="card">

        <h1 class="text-2xl font-bold mb-5">
            Decision Tree Predictive Analytics
        </h1>

        <p class="text-gray-500 mb-5">
            Predict fire risk levels using
            severity and incident frequency.
        </p>

        <div class="grid grid-cols-2 gap-5">

            <!-- INPUT -->
            <div>

                <label class="font-semibold">
                    Severity Level
                </label>

                <select
                id="severity"
                class="border p-3 rounded-xl w-full mt-2 mb-5">

                    <option value="1">Low</option>
                    <option value="2">Medium</option>
                    <option value="3">High</option>

                </select>

                <label class="font-semibold">
                    Incident Count
                </label>

                <input
                type="number"
                id="incident_count"
                placeholder="Enter Incident Count"
                class="border p-3 rounded-xl w-full mt-2 mb-5">

                <button
                onclick="predictRisk()"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold w-full">

                    Predict Fire Risk

                </button>

            </div>

            <!-- RESULT -->
            <div
            class="bg-gray-100 rounded-2xl p-10 flex flex-col justify-center items-center">

                <i class="fas fa-chart-line text-6xl text-red-600 mb-5"></i>

                <h2 class="text-2xl font-bold mb-3">
                    Prediction Result
                </h2>

                <div
                id="predictionResult"
                class="text-4xl font-bold text-red-600">

                    WAITING...

                </div>

            </div>

        </div>

    </div>

</div>

<!-- COMMUNICATION -->
<div id="communication" class="section hidden p-6">

    <div class="card">

        <h1 class="text-2xl font-bold mb-5">
            Emergency Communication Module
        </h1>

        <div class="grid grid-cols-2 gap-5">

            <!-- SEND -->
            <div>

                <label class="font-semibold">
                    Receiver
                </label>

                <select
                id="receiver"
                class="border p-3 rounded-xl w-full mt-2 mb-4">

                    <option value="BFP Personnel">
                        BFP Personnel
                    </option>

                    <option value="Barangay">
                        Barangay
                    </option>

                    <option value="Responder Team">
                        Responder Team
                    </option>

                </select>

                <label class="font-semibold">
                    Message
                </label>

                <textarea
                id="message"
                rows="6"
                class="border p-3 rounded-xl w-full mt-2 mb-4"
                placeholder="Enter emergency coordination message..."></textarea>

                <button
                onclick="sendMessage()"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl font-bold w-full">

                    Send Message

                </button>

            </div>

            <!-- MESSAGES -->
            <div>

                <h2 class="font-bold text-xl mb-4">
                    Live Communication Feed
                </h2>

                <div
                id="messageList"
                class="space-y-3 max-h-[500px] overflow-y-auto">

                </div>

            </div>

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

<script src="admin.js" defer></script>

</body>
</html>
