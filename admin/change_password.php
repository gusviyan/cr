<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old     = $_POST['old_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$old || !$new || !$confirm) {
        $error = "Semua field wajib diisi";
    } elseif ($new !== $confirm) {
        $error = "Password baru dan konfirmasi tidak sama";
    } elseif (strlen($new) < 6) {
        $error = "Password minimal 6 karakter";
    } else {

        $uid = $_SESSION['user']['id'];

        $q = mysqli_query($conn, "
            SELECT password FROM users WHERE id = $uid
        ");
        $user = mysqli_fetch_assoc($q);

        if (!$user || !password_verify($old, $user['password'])) {
            $error = "Password lama salah";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);

            mysqli_query($conn, "
                UPDATE users SET password = '$hash' WHERE id = $uid
            ");

            $message = "Password berhasil diperbarui";
        }
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<div class="card" style="max-width:480px;margin:auto;">
    <h3>Ganti Password</h3>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="password-form">

        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="old_password" required>
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit">Simpan Perubahan</button>

    </form>
</div>

<?php include "../includes/footer.php"; ?>
