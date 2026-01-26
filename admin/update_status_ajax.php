<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id     = intval($_POST['id'] ?? 0);
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
$admin  = $_SESSION['user']['id'];

if ($id <= 0 || !$status) {
    echo "ERR";
    exit;
}

/* ambil status lama */
$t = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT status FROM tickets WHERE id = $id
"));

if (!$t) {
    echo "ERR";
    exit;
}

/* JIKA STATUS SAMA */
if ($t['status'] === $status) {
    echo "OK";
    exit;
}

/* UPDATE STATUS + SOLVED_AT */
if ($status === 'Resolved') {

    mysqli_query($conn,"
        UPDATE tickets
        SET status = '$status',
            solved_at = NOW()
        WHERE id = $id
    ");

} else {

    mysqli_query($conn,"
        UPDATE tickets
        SET status = '$status',
            solved_at = NULL
        WHERE id = $id
    ");
}

/* LOG STATUS */
mysqli_query($conn,"
    INSERT INTO ticket_status_logs
        (ticket_id, old_status, new_status, changed_by)
    VALUES
        ($id, '{$t['status']}', '$status', $admin)
");

/* NOTIFIKASI USER */
mysqli_query($conn,"
    INSERT INTO notifications
        (user_id, role, type, message, link)
    SELECT
        user_id,
        'user',
        'status',
        CONCAT('Status ticket #', id, ' berubah menjadi $status'),
        CONCAT('/cr/tickets/detail.php?id=', id)
    FROM tickets
    WHERE id = $id
");

echo "OK";
