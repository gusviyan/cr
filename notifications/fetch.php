<?php
include "../config/database.php";
include "../config/auth.php";

$user = $_SESSION['user'];

$where = $user['role'] === 'admin'
    ? "role='admin'"
    : "role='user' AND user_id=".$user['id'];

/* ambil 10 terakhir */
$q = mysqli_query($conn,"
    SELECT * FROM notifications
    WHERE $where
    ORDER BY created_at DESC
    LIMIT 10
");

$data = [];
$unread = 0;

while ($n = mysqli_fetch_assoc($q)) {
    if ($n['is_read'] == 0) $unread++;
    $data[] = $n;
}

echo json_encode([
    'unread' => $unread,
    'items'  => $data
]);
