<?php
include "../config/database.php";
include "../config/auth.php";
if($_SESSION['user']['role']!='admin') exit;

$id=$_GET['id'];

if($_POST){
  $old=mysqli_fetch_assoc(mysqli_query($conn,"SELECT status FROM tickets WHERE id=$id"))['status'];
  $new=$_POST['status'];

  mysqli_query($conn,"UPDATE tickets SET status='$new' WHERE id=$id");
  mysqli_query($conn,"INSERT INTO ticket_status_logs
  (ticket_id,old_status,new_status,changed_by)
  VALUES($id,'$old','$new',".$_SESSION['user']['id'].")");
  header("Location: tickets.php");
  exit;
}

include "../includes/header.php";
?>
<div class="card">
<h3>Update Status</h3>
<form method="post">
<select name="status">
<option>New</option>
<option>In Progress</option>
<option>Resolved</option>
<option>Closed</option>
</select>
<button>Update</button>
</form>
</div>
<?php include "../includes/footer.php"; ?>
