<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Barangay Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

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
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */

.sidebar{
    width:280px;
    background:linear-gradient(180deg,#0f172a,#1e293b);
    padding:25px;
    display:flex;
    flex-direction:column;
    gap:15px;
    color:white;
    box-shadow:0 0 25px rgba(0,0,0,0.15);
}

.logo{
    text-align:center;
    font-size:30px;
    font-weight:700;
    margin-bottom:20px;
}

.sidebar button{
    background:rgba(255,255,255,0.08);
    border:none;
    padding:15px;
    border-radius:16px;
    color:white;
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition:0.3s;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.sidebar button:hover{
    background:#2563eb;
}

.sidebar span{
    background:red;
    padding:3px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

.logout-btn{
    margin-top:auto;
    background:#dc2626 !important;
}

/* MAIN */

.main{
    flex:1;
    padding:35px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.topbar h1{
    font-size:34px;
    color:#0f172a;
    font-weight:700;
}

.profile{
    background:white;
    padding:10px 18px;
    border-radius:18px;
    display:flex;
    align-items:center;
    gap:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.profile img{
    width:55px;
    height:55px;
    border-radius:50%;
}

/* STATS */

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    margin-bottom:30px;
}

.card{
    background:white;
    padding:28px;
    border-radius:24px;
    box-shadow:0 10px 30px rgba(0,0,0,0.07);
}

.card h2{
    color:#64748b;
    margin-bottom:10px;
}

.card p{
    font-size:45px;
    font-weight:700;
    color:#0f172a;
}

/* CONTENT */

.content-container{
    background:white;
    padding:30px;
    border-radius:25px;
    box-shadow:0 10px 35px rgba(0,0,0,0.06);
}

.title{
    font-size:26px;
    margin-bottom:25px;
    color:#0f172a;
    font-weight:700;
}

.search{
    width:100%;
    padding:16px;
    border:none;
    background:#f1f5f9;
    border-radius:14px;
    margin-bottom:25px;
    outline:none;
}

.notification-card,
.report-card,
.message-card{
    background:#f8fafc;
    padding:20px;
    border-radius:18px;
    margin-bottom:18px;
    border-left:5px solid #2563eb;
}

.message-box{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.message-box input,
.message-box textarea{
    background:#f1f5f9;
    border:none;
    padding:16px;
    border-radius:15px;
    outline:none;
}

.message-box textarea{
    height:140px;
}

.message-box button{
    background:#2563eb;
    color:white;
    border:none;
    padding:15px;
    border-radius:15px;
    cursor:pointer;
}

.hidden{
    display:none;
}

.action-btn{
    border:none;
    padding:10px 16px;
    border-radius:10px;
    color:white;
    cursor:pointer;
    font-weight:600;
}

.confirm-btn{
    background:#16a34a;
}

.reject-btn{
    background:#dc2626;
}

</style>

</head>

<body onload="loadDashboard()">

<!-- SIDEBAR -->

<div class="sidebar">

    <h1 class="logo">BARANGAY</h1>

    <button onclick="switchTab('home')">
        🏠 Home
    </button>

    <button onclick="switchTab('notifications')">
        🔔 Notifications
        <span id="notifCount">0</span>
    </button>

    <button onclick="switchTab('reports')">
        📄 Reports
        <span id="reportCount">0</span>
    </button>

    <button onclick="switchTab('messages')">
        💬 Messages
    </button>

    <button onclick="logout()" class="logout-btn">
        🚪 Logout
    </button>

</div>

<!-- MAIN -->

<div class="main">

    <div class="topbar">

        <h1>Barangay Dashboard</h1>

        <div class="profile">

            <img src="https://i.imgur.com/2DhmtJ4.png">

            <div>
                <h3>Barangay Officer</h3>
                <p>Administrator</p>
            </div>

        </div>

    </div>

    <!-- HOME -->

    <div id="home" class="tab">

        <div class="stats">

            <div class="card">
                <h2>Pending Reports</h2>
                <p id="statPending">0</p>
            </div>

            <div class="card">
                <h2>Confirmed Reports</h2>
                <p id="statConfirmed">0</p>
            </div>

            <div class="card">
                <h2>Rejected Reports</h2>
                <p id="statRejected">0</p>
            </div>

        </div>

    </div>

    <!-- NOTIFICATIONS -->

    <div id="notifications" class="tab hidden">

        <div class="content-container">

            <h1 class="title">Pending Notifications</h1>

            <div id="notifList"></div>

        </div>

    </div>

    <!-- REPORTS -->

    <div id="reports" class="tab hidden">

        <div class="content-container">

            <h1 class="title">Reports Record</h1>

            <input
            type="text"
            id="searchInput"
            class="search"
            placeholder="Search reports..."
            onkeyup="filterReports()"
            >

            <div id="reportList"></div>

        </div>

    </div>

    <!-- MESSAGES -->

    <div id="messages" class="tab hidden">

        <div class="content-container">

            <h1 class="title">Messages</h1>

            <div class="message-box">

                <input type="text" id="receiver" placeholder="Receiver">

                <textarea id="msgText" placeholder="Type message..."></textarea>

                <button onclick="sendMessage()">
                    Send Message
                </button>

            </div>

            <div id="messageList"></div>

        </div>

    </div>

</div>

<script>

const API = "http://127.0.0.1:5000";

/* SWITCH TABS */

function switchTab(tabId){

    document.querySelectorAll('.tab').forEach(tab=>{

        tab.classList.add('hidden');

    });

    document.getElementById(tabId).classList.remove('hidden');

}

/* LOAD DASHBOARD */

async function loadDashboard(){

    loadNotifications();
    loadReports();
    loadStats();
    loadMessages();

}

/* LOAD NOTIFICATIONS */

async function loadNotifications(){

    try{

        const response = await fetch(`${API}/reports`);
        const reports = await response.json();

        const pendingReports = reports.filter(
            r => r.status === "Pending"
        );

        document.getElementById("notifCount").innerText =
            pendingReports.length;

        let notifList = document.getElementById("notifList");

        notifList.innerHTML = "";

        if(pendingReports.length === 0){

            notifList.innerHTML = `
                <div class="notification-card">
                    No pending reports.
                </div>
            `;

            return;
        }

        pendingReports.forEach(report => {

            let div = document.createElement("div");

            div.classList.add("notification-card");

            div.innerHTML = `

                <h3 style="font-size:20px; font-weight:700;">
                    ${report.type}
                </h3>

                <p style="margin-top:8px;">
                    <strong>Resident:</strong>
                    ${report.full_name || "Unknown"}
                </p>

                <p style="margin-top:8px;">
                    ${report.description}
                </p>

                <p style="
                    margin-top:10px;
                    color:#f59e0b;
                    font-weight:700;
                ">
                    Status: Pending
                </p>

                <div style="
                    margin-top:15px;
                    display:flex;
                    gap:10px;
                ">

                    <button
                        class="action-btn confirm-btn"
                        onclick="updateStatus(${report.id}, 'Confirmed')"
                    >
                        ✅ Confirm
                    </button>

                    <button
                        class="action-btn reject-btn"
                        onclick="updateStatus(${report.id}, 'Rejected')"
                    >
                        ❌ Reject
                    </button>

                </div>

            `;

            notifList.appendChild(div);

        });

    }catch(error){

        console.log(error);

    }

}

/* UPDATE STATUS */

async function updateStatus(id, status){

    try{

        const response = await fetch(`${API}/update_status`, {

            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({
                id:id,
                status:status
            })

        });

        const data = await response.json();

        alert(data.message);

        loadDashboard();

    }catch(error){

        console.log(error);

    }

}

/* LOAD REPORT RECORDS */

async function loadReports(){

    try{

        const response = await fetch(`${API}/reports`);
        const reports = await response.json();

        const records = reports.filter(
            r => r.status !== "Pending"
        );

        document.getElementById("reportCount").innerText =
            records.length;

        let reportList = document.getElementById("reportList");

        reportList.innerHTML = "";

        if(records.length === 0){

            reportList.innerHTML = `
                <div class="report-card">
                    No report records available.
                </div>
            `;

            return;
        }

        records.forEach(report => {

            let color = "#64748b";

            if(report.status === "CONFIRMED"){
                color = "#16a34a";
            }

            if(report.status === "REJECTED"){
                color = "#dc2626";
            }

            let div = document.createElement("div");

            div.classList.add("report-card");

            div.innerHTML = `

                <h3 style="font-size:20px; font-weight:700;">
                    ${report.type}
                </h3>

                <p style="margin-top:8px;">
                    <strong>Resident:</strong>
                    ${report.full_name || "Unknown"}
                </p>

                <p style="margin-top:8px;">
                    ${report.description}
                </p>

                <p style="
                    margin-top:10px;
                    color:${color};
                    font-weight:700;
                ">
                    Status: ${report.status}
                </p>

            `;

            reportList.appendChild(div);

        });

    }catch(error){

        console.log(error);

    }

}

/* LOAD STATS */

async function loadStats(){

    try{

        const response = await fetch(`${API}/reports`);
        const reports = await response.json();

        const pending =
            reports.filter(r => r.status === "Pending").length;

        const confirmed =
            reports.filter(r => r.status === "CONFIRMED").length;

        const rejected =
            reports.filter(r => r.status === "REJECTED").length;

        document.getElementById("statPending").innerText =
            pending;

        document.getElementById("statConfirmed").innerText =
            confirmed;

        document.getElementById("statRejected").innerText =
            rejected;

    }catch(error){

        console.log(error);

    }

}

/* FILTER REPORTS */

function filterReports(){

    let input =
        document.getElementById("searchInput")
        .value
        .toLowerCase();

    let reports =
        document.querySelectorAll(".report-card");

    reports.forEach(report => {

        let text = report.innerText.toLowerCase();

        report.style.display =
            text.includes(input)
            ? "block"
            : "none";

    });

}

/* SEND MESSAGE */

async function sendMessage(){

    let receiver =
        document.getElementById("receiver").value;

    let message =
        document.getElementById("msgText").value;

    if(receiver === "" || message === ""){

        alert("Complete fields");
        return;

    }

    try{

        const response = await fetch(`${API}/send_message`, {

            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({

                sender:"Barangay Officer",
                receiver:receiver,
                message:message

            })

        });

        const data = await response.json();

        alert(data.message);

        document.getElementById("receiver").value = "";
        document.getElementById("msgText").value = "";

        loadMessages();

    }catch(error){

        console.log(error);

    }

}

/* LOAD MESSAGES */

async function loadMessages(){

    try{

        const response = await fetch(`${API}/messages`);
        const messages = await response.json();

        let messageList =
            document.getElementById("messageList");

        messageList.innerHTML = "";

        messages.forEach(msg => {

            let div = document.createElement("div");

            div.classList.add("message-card");

            div.innerHTML = `

                <strong>
                    ${msg.sender}
                </strong><br>

                To: ${msg.receiver}<br><br>

                ${msg.message}

            `;

            messageList.appendChild(div);

        });

    }catch(error){

        console.log(error);

    }

}

/* LOGOUT */

function logout(){

    let confirmLogout =
        confirm("Are you sure you want to logout?");

    if(confirmLogout){

        window.location.href = "logout.php";

    }

}

</script>

</body>
</html>