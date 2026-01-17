<?php
include "../config/database.php";
include "../config/auth.php";

/* hanya admin */
if ($_SESSION['user']['role'] !== 'admin') {
    echo "Akses ditolak";
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];
    $pass  = $_POST['password'];

    /* validasi sederhana */
    if ($name === "" || $email === "" || $pass === "") {
        $error = "Semua field wajib diisi";
    } else {

        /* cek email sudah ada */
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar";
        } else {

            /* hash password */
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            /* simpan user */
            mysqli_query($conn, "
                INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$hash', '$role')
            ");

            $success = "User berhasil ditambahkan";
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";

?>

<div class="card">
    <h3>Tambah User Baru</h3>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nama</label>
        <input type="text" name="name" required>

        <label>Username</label>
        <input type="text" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Simpan User</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
