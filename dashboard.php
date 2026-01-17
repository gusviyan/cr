<?php
include "config/database.php";
include "config/auth.php";
include "config/app.php";

include "includes/header.php";
include "includes/sidebar.php";

$isAdmin = $_SESSION['user']['role'] === 'admin';
$userId  = $_SESSION['user']['id'];

$where = $isAdmin ? "1" : "user_id = $userId";

/* total */
$total = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) c FROM tickets WHERE $where")
)['c'];

/* per status */
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
?>

<div class="dashboard-header">
  <h2>Dashboard</h2>
  <span class="dashboard-sub">
    <?= $isAdmin ? 'Overview semua tiket' : 'Ringkasan tiket Anda' ?>
  </span>
</div>

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

<?php include "includes/footer.php"; ?>
