<?php
include "../config/database.php";
include "../config/auth.php";

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

include "../includes/header.php";
include "../includes/sidebar.php";

/* ================= PARAM ================= */
$search = $_GET['q'] ?? '';
$sort   = $_GET['sort'] ?? 'created_at';
$order  = $_GET['order'] ?? 'desc';
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

/* ================= SORT WHITELIST ================= */
$allowedSort = [
    'id'         => 't.id',
    'title'      => 't.title',
    'status'     => 't.status',
    'created_at' => 't.created_at',
    'user'       => 'u.name'
];

if (!isset($allowedSort[$sort])) {
    $sort = 'created_at';
}

$order = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

/* ================= SEARCH ================= */
$where = '';
if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where = "WHERE t.title LIKE '%$s%' OR u.name LIKE '%$s%'";
}

/* ================= TOTAL DATA ================= */
$totalQ = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    $where
");
$totalData = mysqli_fetch_assoc($totalQ)['total'];
$totalPage = ceil($totalData / $limit);

/* ================= ADMIN LIST ================= */
$admins = mysqli_query($conn,"SELECT id, name FROM users WHERE role='admin'");
$adminList = [];
while ($a = mysqli_fetch_assoc($admins)) {
    $adminList[] = $a;
}

/* ================= DATA TICKETS ================= */
$q = mysqli_query($conn,"
    SELECT t.*, u.name AS user_name
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    $where
    ORDER BY {$allowedSort[$sort]} $order
    LIMIT $limit OFFSET $offset
");

/* ================= SORT LINK ================= */
function sortLink($label, $key, $sort, $order, $search) {
    $newOrder = ($sort === $key && $order === 'ASC') ? 'desc' : 'asc';
    $icon = ($sort === $key)
        ? ($order === 'ASC' ? ' ▲' : ' ▼')
        : '';
    return "<a href='?sort=$key&order=$newOrder&q=".urlencode($search)."'>$label$icon</a>";
}
?>

<div class="card">
    <h3>Admin - Semua Ticket</h3>

    <!-- ================= SEARCH ================= -->
    <form method="get" class="ticket-search">
        <input type="text" name="q" placeholder="Search by Title / User..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>

        <?php if ($search): ?>
            <a href="tickets.php" class="reset-btn">Reset</a>
        <?php endif; ?>
    </form>

    <!-- ================= TABLE ================= -->
    <div class="table-wrapper">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th><?= sortLink('ID','id',$sort,$order,$search) ?></th>
                    <th><?= sortLink('Title','title',$sort,$order,$search) ?></th>
                    <th><?= sortLink('User','user',$sort,$order,$search) ?></th>
                    <th><?= sortLink('Status','status',$sort,$order,$search) ?></th>
                    <th>Assign To</th>
                    <th><?= sortLink('Date','created_at',$sort,$order,$search) ?></th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($t = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= htmlspecialchars($t['user_name']) ?></td>

                    <td>
                        <select onchange="updateStatus(this, <?= $t['id'] ?>)">
                            <?php foreach(['New','In Progress','Resolved','Closed'] as $s): ?>
                                <option value="<?= $s ?>" <?= $t['status']===$s?'selected':'' ?>>
                                    <?= $s ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span id="status-msg-<?= $t['id'] ?>" class="inline-msg"></span>
                    </td>

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
                        <span id="assign-msg-<?= $t['id'] ?>" class="inline-msg"></span>
                    </td>

                    <td><?= $t['created_at'] ?></td>

                    <td>
                        <a href="/cr/tickets/detail.php?id=<?= $t['id'] ?>">
                            Detail
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ================= PAGINATION ================= -->
    <?php if ($totalPage > 1): ?>
    <div class="pagination">
        <?php for ($i=1; $i<=$totalPage; $i++): ?>
            <a class="<?= $i==$page?'active':'' ?>"
               href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= strtolower($order) ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(select, ticketId) {
    const msg = document.getElementById('status-msg-' + ticketId);
    msg.textContent = '⏳';

    fetch('/cr/admin/update_status_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id=' + ticketId + '&status=' + encodeURIComponent(select.value)
    })
    .then(r => r.text())
    .then(res => {
        msg.textContent = res === 'OK' ? '✔' : '✖';
        msg.style.color = res === 'OK' ? '#22c55e' : '#ef4444';
    });
}

function assignTo(select, ticketId) {
    const msg = document.getElementById('assign-msg-' + ticketId);
    msg.textContent = '⏳';

    fetch('/cr/admin/assign_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'ticket_id=' + ticketId + '&admin_id=' + encodeURIComponent(select.value)
    })
    .then(r => r.text())
    .then(res => {
        msg.textContent = res === 'OK' ? '✔' : '✖';
        msg.style.color = res === 'OK' ? '#22c55e' : '#ef4444';
    });
}
</script>

<?php include "../includes/footer.php"; ?>
