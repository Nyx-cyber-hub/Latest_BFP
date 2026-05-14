<?php
include 'database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $contact_number = trim($_POST['contact_number']);
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : null;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // CHECK USERNAME
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Username already exists!";
    } else {

        $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, contact_number, barangay)
                                VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssss",
            $full_name,
            $username,
            $hashed_password,
            $role,
            $contact_number,
            $barangay
        );

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BFP Register</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gradient-to-br from-red-100 via-white to-red-200 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border-t-8 border-red-600">

    <h1 class="text-3xl font-extrabold text-center text-red-600 mb-2">
        BFP SYSTEM
    </h1>

    <p class="text-center text-gray-500 mb-6 text-sm">
        Create new user account
    </p>

    <?php if($message): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm text-center">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FORM START -->
    <form method="POST" class="space-y-4">

        <input type="text" name="full_name" placeholder="Full Name" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">

        <input type="text" name="username" placeholder="Username" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">

        <input type="password" name="password" placeholder="Password" required
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">

        <input type="text" name="contact_number" placeholder="Contact Number"
               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">

        <!-- ROLE -->
        <select name="role" id="roleSelect" required
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">

            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="barangay">Barangay</option>
            <option value="resident">Resident</option>

        </select>

        <!-- BARANGAY FIELD -->
        <div id="barangayField" class="hidden">
            <input type="text"
                   name="barangay"
                   id="barangayInput"
                   placeholder="Enter Barangay"
                   class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500">
        </div>

        <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white p-3 rounded-lg font-semibold transition">
            Register
        </button>

    </form>
    <!-- FORM END -->

    <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <a href="login.php" class="text-red-600 font-semibold hover:underline">
            Login here
        </a>
    </p>

</div>

<!-- JS FIX -->
<script>
const roleSelect = document.getElementById("roleSelect");
const barangayField = document.getElementById("barangayField");
const barangayInput = document.getElementById("barangayInput");

roleSelect.addEventListener("change", function () {

    if (this.value === "barangay" || this.value === "resident") {
        barangayField.classList.remove("hidden");
        barangayInput.setAttribute("required", "required");
    } else {
        barangayField.classList.add("hidden");
        barangayInput.removeAttribute("required");
        barangayInput.value = "";
    }

});
</script>

</body>
</html>