<?php
include "../config/database.php";
include "../includes/header.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $error = "Email tidak ditemukan";
    } elseif (!password_verify($password, $user['password'])) {
        $error = "Password salah";
    } else {
        $_SESSION['user'] = $user;
        header("Location: ../dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="/cr/assets/css/dark.css">
</head>
<body>

<div class="card" style="max-width:400px;max-height:fit-content;margin:200px auto;">
<h3>Login</h3>

<?php if($error): ?>
<p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post">
<input type="text" name="email" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
</div>

</body>
<?php include "../includes/footer.php"; ?>
</html>
