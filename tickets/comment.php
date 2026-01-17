<?php
include "../config/database.php";
include "../config/auth.php";

$ticket_id = intval($_POST['ticket_id']);
$user_id   = $_SESSION['user']['id'];
$role      = $_SESSION['user']['role'];

/* ambil status ticket */
$t = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT status FROM tickets WHERE id = $ticket_id
"));

if (!$t) {
    die("Ticket tidak ditemukan");
}

/* JIKA CLOSED & BUKAN ADMIN → TOLAK */
if ($t['status'] === 'Closed' && $role !== 'admin') {
    die("Komentar ditutup untuk ticket yang sudah Closed.");
}

/* simpan komentar */
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

mysqli_query($conn,"
    INSERT INTO ticket_comments (ticket_id, user_id, comment)
    VALUES ($ticket_id, $user_id, '$comment')
");

header("Location: detail.php?id=$ticket_id");
exit;
