<?php
include "../config/database.php";
include "../config/auth.php";

$id = intval($_POST['id']);
$user = $_SESSION['user'];

$where = $user['role'] === 'admin'
    ? "role='admin'"
    : "role='user' AND user_id=".$user['id'];

mysqli_query($conn,"
    UPDATE notifications
    SET is_read = 1
    WHERE id = $id AND $where
");

echo "OK";
