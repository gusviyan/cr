<?php
include "config/database.php";
include "config/auth.php";
include "config/app.php";

include "includes/header.php";
include "includes/sidebar.php";

$isAdmin = $_SESSION['user']['role'] === 'admin';
$userId  = $_SESSION['user']['id'];

$where = $isAdmin ? "1" : "user_id = $userId";

/* ================= TOTAL TICKET ================= */
$total = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM tickets WHERE $where")
)['c'];

/* ================= PER STATUS ================= */
$statuses = [
  'New' => '🆕',
  'In Progress' => '⏳',
  'Resolved' => '✅',
  'Closed' => '🔒'
];

$data = [];
foreach ($statuses as $s => $icon) {
    $c = mysqli_fetch_assoc(
        mysqli_query($conn,"
          SELECT COUNT(*) c FROM tickets 
          WHERE status='$s' AND $where
        ")
    )['c'];

    $data[] = [
      'label' => $s,
      'count' => $c,
      'icon'  => $icon
    ];
}

/* ================= ADMIN STATS (ADMIN ONLY) ================= */
$adminStats = [];

if ($isAdmin) {
    $qAdmin = mysqli_query($conn, "
        SELECT 
            u.id,
            u.name,
            COUNT(t.id) AS total_ticket
        FROM users u
        LEFT JOIN tickets t ON t.assigned_by = u.id
        WHERE u.role = 'admin'
        GROUP BY u.id, u.name
        ORDER BY u.name ASC
    ");

    while ($row = mysqli_fetch_assoc($qAdmin)) {
        $adminStats[] = $row;
    }
}
?>



<h3 class="dashboard-subtitle">📌 Overview Ticket</h3>

<!-- ================= MAIN DASHBOARD ================= -->
<div class="dashboard-grid">

    <!-- TOTAL -->
    <div class="dash-card total">
        <div class="dash-icon">📊</div>
        <div class="dash-info">
            <div class="dash-label">Total Ticket</div>
            <div class="dash-value"><?= $total ?></div>
        </div>
    </div>

    <?php foreach($data as $d): ?>
    <div class="dash-card <?= strtolower(str_replace(' ','-',$d['label'])) ?>">
        <div class="dash-icon"><?= $d['icon'] ?></div>
        <div class="dash-info">
            <div class="dash-label"><?= $d['label'] ?></div>
            <div class="dash-value"><?= $d['count'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>

</div>

<!-- ================= ADMIN SECTION ================= -->
<?php if ($isAdmin): ?>
<div class="dashboard-admin-section">

    <h3 class="dashboard-subtitle">📌 Ticket Assigned</h3>

    <div class="dashboard-grid admin-grid">
        <?php foreach ($adminStats as $a): ?>
        <div class="dash-card admin">
            <div class="dash-icon">👤</div>
            <div class="dash-info">
                <div class="dash-label"><?= htmlspecialchars($a['name']) ?></div>
                <div class="dash-value"><?= $a['total_ticket'] ?></div>
                <div class="dash-sub">Ticket assigned</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
