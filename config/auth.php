<?php
if(!isset($_SESSION['user'])){
    header("Location: /cr/auth/login.php");
    exit;
}
