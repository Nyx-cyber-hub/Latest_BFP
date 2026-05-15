
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
    data.filter(r => r.status === "CONFIRMED").length;

    document.getElementById("rejectedReports").innerText =
    data.filter(r => r.status === "REJECTED").length;

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
                ${r.status === 'PENDING' ? 'bg-yellow-500' :
                r.status === 'CONFIRMED' ? 'bg-green-600' :
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
// PREDICT RISK
// ======================
async function predictRisk(){

    const severity =
    document.getElementById("severity").value;

    const incident_count =
    document.getElementById("incident_count").value;

    if(incident_count === ""){

        alert("Enter Incident Count");
        return;

    }

    try{

        const response = await fetch(
        "http://127.0.0.1:5000/predict_risk",
        {

            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({

                severity:severity,
                incident_count:incident_count

            })

        });

        const data = await response.json();

        if(data.success){

            document.getElementById(
            "predictionResult"
            ).innerHTML = data.prediction;

        }else{

            alert(data.message);

        }

    }catch(err){

        console.error(err);

        alert("Prediction Error");

    }

}

// ======================
// SEND MESSAGE
// ======================
async function sendMessage(){

    const receiver =
    document.getElementById("receiver").value;

    const message =
    document.getElementById("message").value;

    if(message.trim() === ""){

        alert("Enter Message");
        return;

    }

    try{

        const response = await fetch(
        "http://127.0.0.1:5000/send_message",
        {

            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({

                sender:"ADMIN",
                receiver:receiver,
                message:message

            })

        });

        const data = await response.json();

        if(data.success){

            document.getElementById("message").value = "";

            loadMessages();

        }else{

            alert(data.message);

        }

    }catch(err){

        console.error(err);

        alert("Message Error");

    }

}


// ======================
// LOAD MESSAGES
// ======================
async function loadMessages(){

    try{

        const response = await fetch(
        "http://127.0.0.1:5000/messages"
        );

        const data = await response.json();

        let container =
        document.getElementById("messageList");

        container.innerHTML = "";

        data.forEach(msg => {

            container.innerHTML += `

            <div class="bg-gray-100 p-4 rounded-xl">

                <div class="flex justify-between mb-2">

                    <span class="font-bold text-red-600">
                        ${msg.sender}
                    </span>

                    <span class="text-sm text-gray-500">
                        TO: ${msg.receiver}
                    </span>

                </div>

                <div class="text-gray-700">
                    ${msg.message}
                </div>

            </div>

            `;

        });

    }catch(err){

        console.error(err);

    }

}

// ======================
// INIT
// ======================
window.onload = function(){

    const homeBtn = document.querySelector(".menuBtn");

    showSection('home', homeBtn);

    loadReports();

    loadMessages();

    setInterval(loadReports, 5000);

    setInterval(loadMessages, 3000);
}
