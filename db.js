let map = null;
let heatLayer = null;
let mapLoaded = false;
let activeBtn = null;

// ACTIVE BUTTON
function setActive(btn){

    if(activeBtn){
        activeBtn.classList.remove('active');
    }

    btn.classList.add('active');
    activeBtn = btn;
}

// SECTION SWITCH
function showSection(sectionId, btn){

    document.querySelectorAll('.section')
    .forEach(s => s.classList.add('hidden'));

    document.getElementById(sectionId)
    .classList.remove('hidden');

    document.getElementById('pageTitle')
    .innerText = sectionId.charAt(0).toUpperCase() + sectionId.slice(1);

    if(btn){
        setActive(btn);
    }

    // FIX MAP RESIZE BUG
    if(sectionId === "gis"){
        setTimeout(() => {
            initMap();
        }, 200);
    }
}

// INIT MAP (FIXED)
function initMap(){

    if(!map){

        map = L.map('map').setView([13.8445, 121.2060], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'BFP GIS System'
        }).addTo(map);

        loadIncidents();

        mapLoaded = true;

    } else {
        setTimeout(() => {
            map.invalidateSize();
        }, 200);
    }
}

// LOAD INCIDENTS
function loadIncidents(){

    fetch("http://127.0.0.1:5000/incidents")
    .then(res => res.json())
    .then(data => {

        let heat = [];

        map.eachLayer(layer => {
            if(layer instanceof L.CircleMarker){
                map.removeLayer(layer);
            }
        });

        data.forEach(i => {

            let color =
                i.severity === "High" ? "red" :
                i.severity === "Medium" ? "orange" : "green";

            L.circleMarker([i.lat, i.lng], {
                radius: 8,
                color: color,
                fillOpacity: 0.8
            })
            .addTo(map)
            .bindPopup(i.description);

            heat.push([i.lat, i.lng, 0.5]);

        });

        if(heatLayer){
            map.removeLayer(heatLayer);
        }

        heatLayer = L.heatLayer(heat, {
            radius: 25
        }).addTo(map);

    });
}

// SIDEBAR TOGGLE
function toggleSidebar(){

    const sidebar = document.getElementById('sidebar');

    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-20');
    sidebar.classList.toggle('sidebar-collapsed');
}

// DEFAULT LOAD
window.onload = function(){
    const homeBtn = document.querySelector(".menuBtn");
    showSection('home', homeBtn);
};