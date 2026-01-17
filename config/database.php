<?php
$conn = mysqli_connect("localhost","root","root","cr");
if(!$conn){
    error_log(mysqli_connect_error());
    die("Service unavailable");
}
session_start();
