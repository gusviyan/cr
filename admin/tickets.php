<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

include "../includes/header.php";
include "../includes/sidebar.php";

/* ambil semua admin (untuk dropdown assign) */
$admins = mysqli_query($conn,"
    SELECT id, name FROM users WHERE role='admin'
");
$adminList = [];
while ($a = mysqli_fetch_assoc($admins)) {
    $adminList[] = $a;
}

/* ambil semua tiket */
$q = mysqli_query($conn, "
    SELECT 
        t.*, 
        u.name AS user_name,
        t.assigned_by
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    ORDER BY t.created_at DESC
");
?>

<div class="card">
    <h3>Admin - Semua Ticket</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>User</th>
            <th>Status</th>
            <th>Assign To</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>

        <?php while ($t = mysqli_fetch_assoc($q)): ?>
        <tr>
            <td><?= $t['id'] ?></td>

            <td><?= htmlspecialchars($t['title']) ?></td>

            <td><?= htmlspecialchars($t['user_name']) ?></td>

            <!-- INLINE STATUS -->
            <td>
                <select onchange="updateStatus(this, <?= $t['id'] ?>)">
                    <?php foreach(['New','In Progress','Resolved','Closed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $t['status']===$s?'selected':'' ?>>
                            <?= $s ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="status-msg-<?= $t['id'] ?>" style="font-size:12px"></span>
            </td>

            <!-- ASSIGN TO (ADMIN ONLY) -->
            <td>
                <select onchange="assignTo(this, <?= $t['id'] ?>)">
                    <option value="">-</option>
                    <?php foreach ($adminList as $ad): ?>
                        <option value="<?= $ad['id'] ?>"
                            <?= $t['assigned_by']==$ad['id']?'selected':'' ?>>
                            <?= htmlspecialchars($ad['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="assign-msg-<?= $t['id'] ?>" style="font-size:12px"></span>
            </td>

            <td><?= $t['created_at'] ?></td>

            <td>
                <a href="/cr/tickets/detail.php?id=<?= $t['id'] ?>">
                    Lihat Detail
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function updateStatus(select, ticketId) {
    const status = select.value;
    const msg = document.getElementById('status-msg-' + ticketId);

    msg.innerHTML = '⏳';

    fetch('/cr/admin/update_status_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id=' + ticketId + '&status=' + encodeURIComponent(status)
    })
    .then(r => r.text())
    .then(res => {
        if (res === 'OK') {
            msg.innerHTML = '✔';
            msg.style.color = 'green';
        } else {
            msg.innerHTML = '✖';
            msg.style.color = 'red';
        }
    });
}

function assignTo(select, ticketId) {
    const adminId = select.value;
    const msg = document.getElementById('assign-msg-' + ticketId);

    msg.innerHTML = '⏳';

    fetch('/cr/admin/assign_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'ticket_id=' + ticketId + '&admin_id=' + encodeURIComponent(adminId)
    })
    .then(r => r.text())
    .then(res => {
        if (res === 'OK') {
            msg.innerHTML = '✔';
            msg.style.color = 'green';
        } else {
            msg.innerHTML = '✖';
            msg.style.color = 'red';
        }
    });
}
</script>

<?php include "../includes/footer.php"; ?>
