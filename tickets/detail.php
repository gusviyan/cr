<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$id = intval($_GET['id']);

/* ================= TICKET ================= */
$t = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        t.*, 
        u.name AS user_name,
        a.name AS assigned_name
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    LEFT JOIN users a ON a.id = t.assigned_by
    WHERE t.id = $id
"));

if (!$t) {
    echo "<div class='card'>Ticket tidak ditemukan</div>";
    include "../includes/footer.php";
    exit;
}

/* ================= AKSES ================= */
if ($_SESSION['user']['role'] !== 'admin' && $t['user_id'] != $_SESSION['user']['id']) {
    echo "<div class='card'>Akses ditolak</div>";
    include "../includes/footer.php";
    exit;
}

/* ================= ATTACHMENTS ================= */
$attachments = mysqli_query($conn,"
    SELECT * FROM ticket_attachments
    WHERE ticket_id = $id
    ORDER BY id ASC
");

/* ================= TIMELINE ================= */
$logs = mysqli_query($conn,"
    SELECT * FROM ticket_status_logs
    WHERE ticket_id = $id
    ORDER BY created_at ASC
");

/* ================= KOMENTAR ================= */
$comments = mysqli_query($conn,"
    SELECT c.*, u.name, u.role
    FROM ticket_comments c
    JOIN users u ON u.id = c.user_id
    WHERE c.ticket_id = $id
    ORDER BY c.created_at ASC
");
?>

<div class="detail-grid">

<!-- ================= KIRI : DETAIL ================= -->
<div class="detail-card">

    <h3><?= htmlspecialchars($t['title']) ?></h3>
    <hr>

    <p><?= nl2br(htmlspecialchars($t['description'])) ?></p>
    <hr>
    <p>
        <b>Status:</b> <?= htmlspecialchars($t['status']) ?><br>
        <b>Dibuat oleh:</b> <?= htmlspecialchars($t['user_name']) ?><br>
        <b>Tanggal:</b> <?= $t['created_at'] ?><br>
        <b>Assigned To:</b>
        <?= $t['assigned_name'] ? htmlspecialchars($t['assigned_name']) : '-' ?>
    </p>

    <hr>

    <!-- ================= ATTACHMENTS ================= -->
    <h4>Attachments</h4>

    <?php if (mysqli_num_rows($attachments) == 0): ?>
        <em class="text-muted">Tidak ada lampiran</em>
    <?php else: ?>
        <div class="attachments">
            <?php while ($a = mysqli_fetch_assoc($attachments)): ?>
                <div class="attachment-item">
                    📎
                    <a href="<?= htmlspecialchars($a['filename']) ?>" target="_blank">
                        <?= basename($a['filename']) ?>
                    </a>

                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="delete_attachment.php?id=<?= $a['id'] ?>&ticket_id=<?= $id ?>"
                           onclick="return confirm('Hapus attachment ini?')"
                           class="delete-attachment">
                           🗑
                        </a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <hr>

    <!-- ================= TIMELINE ================= -->
    <h4>Timeline Status</h4>

    <?php if (mysqli_num_rows($logs) == 0): ?>
        <em>Belum ada perubahan status</em>
    <?php else: ?>
        <?php while ($l = mysqli_fetch_assoc($logs)): ?>
            <?= htmlspecialchars($l['old_status']) ?>
            →
            <?= htmlspecialchars($l['new_status']) ?>
            (<?= $l['created_at'] ?>)<br>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

<!-- ================= KANAN : KOMENTAR ================= -->
<div class="detail-card">

    <h3>Diskusi / Komentar</h3>

    <?php if (mysqli_num_rows($comments) == 0): ?>
        <em>Belum ada komentar</em>
    <?php endif; ?>

    <?php while ($c = mysqli_fetch_assoc($comments)): ?>
        <div class="comment-box <?= $c['role'] === 'admin' ? 'admin' : 'user' ?>">
            <div class="comment-author">
                <?= htmlspecialchars($c['name']) ?>
                <?= $c['role'] === 'admin' ? '(Admin)' : '' ?>
            </div>

            <div class="comment-text">
                <?= nl2br(htmlspecialchars($c['comment'])) ?>
            </div>

            <div class="comment-time">
                <?= $c['created_at'] ?>
            </div>
        </div>
    <?php endwhile; ?>

    <!-- FORM KOMENTAR -->
    <?php if ($t['status'] !== 'Closed' || $_SESSION['user']['role'] === 'admin'): ?>
        <form method="post" action="comment.php">
            <input type="hidden" name="ticket_id" value="<?= $id ?>">
            <textarea name="comment" placeholder="Tulis komentar..." required></textarea>
            <button type="submit">Kirim Komentar</button>
        </form>
    <?php else: ?>
        <div class="card" style="border-left:4px solid #64748b;">
            <em>Komentar ditutup karena ticket sudah <b>Closed</b>.</em>
        </div>
    <?php endif; ?>

</div>

</div>

<?php include "../includes/footer.php"; ?>
