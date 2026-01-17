<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= INSERT TICKET ================= */
    mysqli_query($conn, "
        INSERT INTO tickets (user_id, title, category, description)
        VALUES (
            {$_SESSION['user']['id']},
            '".mysqli_real_escape_string($conn, $_POST['title'])."',
            '".mysqli_real_escape_string($conn, $_POST['category'])."',
            '".mysqli_real_escape_string($conn, $_POST['description'])."'
        )
    ");

    $ticket_id = mysqli_insert_id($conn);

    if (!$ticket_id) {
        die("Gagal membuat ticket");
    }

    /* ================= ATTACHMENTS ================= */
    if (!empty($_FILES['files']['name'][0])) {

        foreach ($_FILES['files']['name'] as $i => $name) {

            if (!$name) continue;

            $size = $_FILES['files']['size'][$i];
            $tmp  = $_FILES['files']['tmp_name'][$i];

            // Max 10 MB
            if ($size > 10 * 1024 * 1024) {
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // Blokir exe
            if ($ext === 'exe') {
                continue;
            }

            $safeName = uniqid('att_') . '.' . $ext;
            $uploadDir = "../uploads/";
            $fullPath  = $uploadDir . $safeName;

            if (!move_uploaded_file($tmp, $fullPath)) {
                continue;
            }

            // SIMPAN PATH RELATIF DARI ROOT
            $dbPath = "/cr/uploads/" . $safeName;

            mysqli_query($conn, "
                INSERT INTO ticket_attachments (ticket_id, filename)
                VALUES ($ticket_id, '$dbPath')
            ");
        }
    }

    header("Location: detail.php?id=$ticket_id");
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
        <input name="title" required>
      </div>

      <div class="form-group">
        <label>Category</label>
        <input name="category" required>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" required></textarea>
      </div>

      <div class="form-group">
        <label>Attachments (max 10MB / file)</label>
        <input type="file" name="files[]" multiple>
      </div>

      <button type="submit">Submit Ticket</button>

    </form>
  </div>
</div>

<?php include "../includes/footer.php"; ?>
