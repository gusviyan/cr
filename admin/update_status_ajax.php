<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id     = intval($_POST['id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$admin  = $_SESSION['user']['id'];

/* ambil data ticket */
$t = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT status, assigned_by FROM tickets WHERE id=$id")
);

if (!$t) {
    echo "ERR";
    exit;
}

/* update status */
mysqli_query($conn,"
    UPDATE tickets 
    SET status='$status'
    WHERE id=$id
");

/* JIKA BELUM ADA assigned_by → ISI SEKALI SAJA */
if (empty($t['assigned_by'])) {
    mysqli_query($conn,"
        UPDATE tickets
        SET assigned_by=$admin
        WHERE id=$id
    ");
}

/* simpan ke log */
mysqli_query($conn,"
    INSERT INTO ticket_status_logs
    (ticket_id, old_status, new_status, changed_by)
    VALUES ($id, '".$t['status']."', '$status', $admin)
");

echo "OK";

$ticket = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT user_id FROM tickets WHERE id = $id
"));

mysqli_query($conn,"
    INSERT INTO notifications (user_id, role, type, message, link)
    VALUES (
        {$ticket['user_id']},
        'user',
        'status',
        'Status ticket #$id berubah menjadi $status',
        '/cr/tickets/detail.php?id=$id'
    )
");

