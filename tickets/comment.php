<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

$ticket_id = intval($_POST['ticket_id']);
$user_id   = $_SESSION['user']['id'];
$role      = $_SESSION['user']['role'];

/* ================= AMBIL STATUS TICKET ================= */
$t = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT status, user_id 
    FROM tickets 
    WHERE id = $ticket_id
"));

if (!$t) {
    die("Ticket tidak ditemukan");
}

/* ================= CEK CLOSED ================= */
if ($t['status'] === 'Closed' && $role !== 'admin') {
    die("Komentar ditutup untuk ticket yang sudah Closed.");
}

/* ================= SIMPAN KOMENTAR ================= */
$comment = trim($_POST['comment']);

if ($comment === '') {
    die("Komentar tidak boleh kosong");
}

$comment_safe = mysqli_real_escape_string($conn, $comment);

mysqli_query($conn,"
    INSERT INTO ticket_comments (ticket_id, user_id, comment)
    VALUES ($ticket_id, $user_id, '$comment_safe')
");

$comment_id = mysqli_insert_id($conn);

if (!$comment_id) {
    die("Gagal menyimpan komentar");
}

/* ================= UPLOAD ATTACHMENT KOMENTAR ================= */
if (!empty($_FILES['files']['name'][0])) {

    foreach ($_FILES['files']['name'] as $i => $name) {

        if (!$name) continue;

        $size = $_FILES['files']['size'][$i];
        $tmp  = $_FILES['files']['tmp_name'][$i];

        /* MAX 10MB */
        if ($size > 10 * 1024 * 1024) continue;

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        /* BLOK EXT BERBAHAYA */
        if (in_array($ext, ['exe','bat','cmd','js','sh'])) continue;

        $safe = uniqid('cmt_') . '.' . $ext;
        $target = "../uploads/$safe";

        if (move_uploaded_file($tmp, $target)) {
            mysqli_query($conn,"
                INSERT INTO ticket_comment_attachments
                (comment_id, filename)
                VALUES ($comment_id, '/cr/uploads/$safe')
            ");
        }
    }
}

/* ================= NOTIFIKASI ================= */

/* USER → ADMIN */
if ($role === 'user') {
    mysqli_query($conn,"
        INSERT INTO notifications (role, type, message, link)
        VALUES (
            'admin',
            'comment',
            'Komentar baru pada ticket #$ticket_id',
            '/cr/tickets/detail.php?id=$ticket_id'
        )
    ");
}

/* ADMIN → USER */
if ($role === 'admin') {
    mysqli_query($conn,"
        INSERT INTO notifications (user_id, role, type, message, link)
        VALUES (
            {$t['user_id']},
            'user',
            'comment',
            'Admin membalas ticket #$ticket_id',
            '/cr/tickets/detail.php?id=$ticket_id'
        )
    ");
}

/* ================= REDIRECT ================= */
header("Location: detail.php?id=$ticket_id");
exit;
