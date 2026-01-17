<?php
if (!isset($_SESSION)) session_start();
include __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Change Request System</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dark.css?v=<?= time() ?>">
</head>
<body>
<div class="header">
  <div class="logo">
    <img src="<?= BASE_URL ?>/assets/images/logo white.png" alt="CRS Logo">
  </div>

  <div class="notif-wrapper">
  <span id="notif-icon">🔔</span>
  <span id="notif-badge"></span>

  <div id="notif-dropdown" class="notif-dropdown">
    <div class="notif-title">Notifikasi</div>
    <div id="notif-list"></div>
  </div>
</div>

  <div class="title">Change Request SIMRS (Ajuan CR)</div>
</div>



<div class="layout">

