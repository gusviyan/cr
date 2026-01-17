<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$ticket_id = intval($_POST['ticket_id']);
$admin_id  = $_POST['admin_id'] !== '' ? intval($_POST['admin_id']) : NULL;

/* validasi admin_id */
if ($admin_id !== NULL) {
    $cek = mysqli_query($conn,"
        SELECT id FROM users 
        WHERE id=$admin_id AND role='admin'
    ");
    if (mysqli_num_rows($cek) === 0) {
        http_response_code(400);
        exit;
    }
}

/* update assign */
if ($admin_id === NULL) {
    mysqli_query($conn,"
        UPDATE tickets SET assigned_by=NULL WHERE id=$ticket_id
    ");
} else {
    mysqli_query($conn,"
        UPDATE tickets SET assigned_by=$admin_id WHERE id=$ticket_id
    ");
}

echo "OK";
