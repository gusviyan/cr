<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

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
    'id'          => 't.id',
    'status'      => 't.status',
    'title'       => 't.title',
    'created_at'  => 't.created_at',
    'assigned_at' => 't.assigned_at',
    'solved_at'   => 't.solved_at'
];

if (!isset($allowedSort[$sort])) {
    $sort = 'created_at';
}

$order = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

/* ================= SEARCH ================= */
$where = "WHERE t.user_id = ".$_SESSION['user']['id'];

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (
        t.title LIKE '%$s%' OR
        t.status LIKE '%$s%' OR
        a.name LIKE '%$s%'
    )";
}

/* ================= TOTAL DATA ================= */
$totalQ = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM tickets t
    LEFT JOIN users a ON a.id = t.assigned_by
    $where
");
$totalData = mysqli_fetch_assoc($totalQ)['total'];
$totalPage = ceil($totalData / $limit);

/* ================= DATA TICKETS ================= */
$q = mysqli_query($conn,"
    SELECT 
        t.id,
        t.title,
        t.status,
        t.created_at,
        t.assigned_at,
        t.solved_at,
        a.name AS assigned_name
    FROM tickets t
    LEFT JOIN users a ON a.id = t.assigned_by
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
    <h3>My Tickets</h3>

    <!-- ================= SEARCH ================= -->
    <form method="get" class="ticket-search">
        <input type="text" name="q"
               placeholder="Search by title / status / admin..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>

        <?php if ($search): ?>
            <a href="list.php" class="reset-btn">Reset</a>
        <?php endif; ?>
    </form>

    <!-- ================= TABLE ================= -->
    <div class="table-wrapper">
        <table class="ticket-table">
            <thead>
            <tr>
                <th><?= sortLink('ID','id',$sort,$order,$search) ?></th>
                <th><?= sortLink('Status','status',$sort,$order,$search) ?></th>
                <th><?= sortLink('Title','title',$sort,$order,$search) ?></th>
                <th><?= sortLink('Created Date','created_at',$sort,$order,$search) ?></th>
                <th>Assigned To</th>
                <th><?= sortLink('Assigned Date','assigned_at',$sort,$order,$search) ?></th>
                <th><?= sortLink('Solved Date','solved_at',$sort,$order,$search) ?></th>
            </tr>
            </thead>

            <tbody>
            <?php if (mysqli_num_rows($q) == 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center;opacity:.7">
                        Tidak ada ticket
                    </td>
                </tr>
            <?php endif; ?>

            <?php while($t = mysqli_fetch_assoc($q)): ?>
            <tr>

                <!-- ID -->
                <td><?= $t['id'] ?></td>

                <!-- STATUS -->
                <td>
                    <span class="badge <?= str_replace(' ', '-', strtolower($t['status'])) ?>">
                        <?= $t['status'] ?>
                    </span>
                </td>

                <!-- TITLE -->
                <td>
                    <a href="detail.php?id=<?= $t['id'] ?>"
                       class="ticket-title-link">
                        <?= htmlspecialchars($t['title']) ?>
                    </a>
                </td>

                <!-- CREATED DATE -->
                <td>
                    <?= $t['created_at']
                        ? date('d-m-Y', strtotime($t['created_at']))
                        : '-' ?>
                </td>

                <!-- ASSIGNED TO -->
                <td>
                    <?= $t['assigned_name']
                        ? htmlspecialchars($t['assigned_name'])
                        : '-' ?>
                </td>

                <!-- ASSIGNED DATE -->
                <td>
                    <?= $t['assigned_at']
                        ? date('d-m-Y', strtotime($t['assigned_at']))
                        : '-' ?>
                </td>

                <!-- SOLVED DATE -->
                <td>
                    <?= $t['solved_at']
                        ? date('d-m-Y', strtotime($t['solved_at']))
                        : '-' ?>
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

<?php include "../includes/footer.php"; ?>
