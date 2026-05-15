let data = [];

/* LOAD */
let messages = [];

function loadMessages() {

fetch("http://127.0.0.1:5000/messages")
.then(res => res.json())
.then(res => {

messages = res;
renderMessages();
msgCount();

});

}

/* RENDER */
function renderMessages() {

let box = document.getElementById("messageList");

if (!box) return;

box.innerHTML = "";

if (messages.length === 0) {
box.innerHTML = `<div class="card">No messages yet.</div>`;
return;
}

messages.forEach(m => {

box.innerHTML += `
<div class="notification-card">
<div class="notification-type">
${m.sender} → ${m.receiver}
</div>

<div class="notification-desc">
${m.message}
</div>

<div class="notification-date">
${formatDate(m.created_at)}
</div>
</div>
`;

});

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
