<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

$id        = intval($_GET['id']);
$ticket_id = intval($_GET['ticket_id']);

$a = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM ticket_attachments WHERE id = $id
"));

if ($a) {
    if (file_exists("../".$a['filename'])) {
        unlink("../".$a['filename']);
    }

    mysqli_query($conn,"
        DELETE FROM ticket_attachments WHERE id = $id
    ");
}

header("Location: detail.php?id=".$ticket_id);
exit;
