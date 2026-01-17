<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

/* SIMPAN TICKET */
if($_POST){
  mysqli_query($conn,"INSERT INTO tickets(user_id,title,category,description)
  VALUES(
    ".$_SESSION['user']['id'].",
    '".mysqli_real_escape_string($conn,$_POST['title'])."',
    '".mysqli_real_escape_string($conn,$_POST['category'])."',
    '".mysqli_real_escape_string($conn,$_POST['description'])."'
  )");

  mysqli_query($conn,"
    INSERT INTO notifications (role, type, message, link)
    VALUES (
        'admin',
        'ticket',
        'Ticket baru dibuat',
        '/cr/admin/tickets.php'
    )
");


  $ticket_id = mysqli_insert_id($conn);

  foreach($_FILES['files']['name'] as $i=>$name){
    if(!$name) continue;

    $ext  = pathinfo($name, PATHINFO_EXTENSION);
    $safe = uniqid().".".$ext;

    move_uploaded_file(
      $_FILES['files']['tmp_name'][$i],
      "../uploads/$safe"
    );

    mysqli_query($conn,"
      INSERT INTO ticket_attachments(ticket_id,filename)
      VALUES($ticket_id,'uploads/$safe')
    ");
  }

  header("Location: list.php");
  exit;
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<div class="form-wrapper">
  <div class="card">
    <h3>New Ticket</h3>

    <form method="post" enctype="multipart/form-data">

      <div class="form-group">
        <label>Title</label>
        <input name="title" placeholder="Judul tiket" required>
      </div>

      <div class="form-group">
        <label>Category</label>
        <input name="category" placeholder="Kategori" required>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" placeholder="Deskripsi masalah" required></textarea>
      </div>

      <div class="form-group">
        <label>Attachments</label>
        <input type="file" name="files[]" multiple>
      </div>

      <div class="form-actions">
        <button type="submit">Submit Ticket</button>
      </div>

    </form>
  </div>
</div>

<?php include "../includes/footer.php"; ?>
