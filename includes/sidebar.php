<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
  <a href="<?= BASE_URL ?>/dashboard.php"
     class="<?= $current=='dashboard.php'?'active':'' ?>">
     📊 <span>Dashboard</span>
  </a>

  <?php if ($_SESSION['user']['role'] !== 'admin'): ?>
    <a href="<?= BASE_URL ?>/tickets/list.php"
       class="<?= $current=='list.php'?'active':'' ?>">
       🎫 <span>My Tickets</span>
    </a>

    <a href="<?= BASE_URL ?>/tickets/create.php"
       class="<?= $current=='create.php'?'active':'' ?>">
       ➕ <span>New Ticket</span>
    </a>
  <?php endif; ?>

  <?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <a href="<?= BASE_URL ?>/admin/tickets.php"
       class="<?= $current=='tickets.php'?'active':'' ?>">
       🛠️ <span>Admin Tickets</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/users_add.php"
       class="<?= $current=='users_add.php'?'active':'' ?>">
       👤 <span>Tambah User</span>
    </a>
  <?php endif; ?>

  <?php if ($_SESSION['user']['role'] === 'admin'): ?>
<li>
    <a href="/cr/admin/change_password.php">
        🔐 Ganti Password
    </a>
</li>
<?php endif; ?>


  <a href="<?= BASE_URL ?>/logout.php">
    🚪 <span>Logout</span>
  </a>
</div>

<div class="content">
