const API = "http://127.0.0.1:5000";

// 🔐 REGISTER
function register(){
    let username = document.getElementById("regUser").value;
    let password = document.getElementById("regPass").value;

    fetch(API + "/register", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        credentials: "include",
        body: JSON.stringify({ username, password })
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) alert(data.error);
        else {
            alert("Registered!");
            window.location.href = "login.html";
        }
    });
}

// 🔐 LOGIN
function login(){
    let username = document.getElementById("loginUser").value;
    let password = document.getElementById("loginPass").value;

    fetch(API + "/login", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        credentials: "include",
        body: JSON.stringify({ username, password })
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) alert(data.error);
        else {
            window.location.href = "index.html";
        }
    });
}

// 🔐 LOGOUT
function logout(){
    fetch(API + "/logout", {
        method: "POST",
        credentials: "include"
    }).then(() => {
        window.location.href = "login.html";
    });
}

// 🔐 PROTECT DASHBOARD
function checkAuth(){
    fetch(API + "/check-auth", {
        credentials: "include"
    })
    .then(res => {
        if(!res.ok){
            window.location.href = "login.html";
        }
    });
}