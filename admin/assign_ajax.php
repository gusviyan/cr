<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$ticket_id = intval($_POST['ticket_id'] ?? 0);
$admin_id  = intval($_POST['admin_id'] ?? 0);

if ($ticket_id <= 0) {
    echo "ERR";
    exit;
}

/* ASSIGN ADMIN */
if ($admin_id > 0) {

    mysqli_query($conn,"
        UPDATE tickets
        SET assigned_by = $admin_id,
            assigned_at = NOW()
        WHERE id = $ticket_id
    ");

} else {

    /* UNASSIGN */
    mysqli_query($conn,"
        UPDATE tickets
        SET assigned_by = NULL,
            assigned_at = NULL
        WHERE id = $ticket_id
    ");
}

echo "OK";
