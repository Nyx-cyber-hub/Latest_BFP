<?php
session_start();
include 'database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

        if ($user['role'] == "admin") {
        header("Location: admin_dashboard.php");
        } 
        elseif ($user['role'] == "barangay") {
            header("Location: barangay_dashboard.php");
        } 
        elseif ($user['role'] == "resident") {
            header("Location: resident_dashboard.php");
        }
        exit();
            exit();

        } else {
            $message = "Invalid password!";
        }

    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BFP Login</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gradient-to-br from-red-100 via-white to-red-200 flex items-center justify-center h-screen">

<!-- CARD -->
<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border-t-8 border-red-600">

    <!-- HEADER -->
    <h1 class="text-3xl font-extrabold text-center text-red-600 mb-2">
        BFP SYSTEM
    </h1>

    <p class="text-center text-gray-500 mb-6 text-sm">
        Fire Incident Reporting & Monitoring Login
    </p>

    <!-- ERROR MESSAGE -->
    <?php if($message): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm text-center">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" class="space-y-4">

        <div>
            <label class="text-sm text-gray-600">Username</label>
            <input type="text"
                   name="username"
                   required
                   placeholder="Enter your username"
                   class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label class="text-sm text-gray-600">Password</label>
            <input type="password"
                   name="password"
                   required
                   placeholder="Enter your password"
                   class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div class="text-right">
            <a href="forgot-password.php" class="text-sm text-red-600 hover:underline">
                Forgot Password?
            </a>
        </div>

        <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white p-3 rounded-lg font-semibold transition">
            Login
        </button>

    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        No account?
        <a href="register.php" class="text-red-600 font-semibold hover:underline">
            Register here
        </a>
    </p>

</div>

</body>
</html>