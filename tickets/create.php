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
    if (!$ticket_id) die("Gagal membuat ticket");

    /* ================= ATTACHMENTS ================= */
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['name'] as $i => $name) {

            if (!$name) continue;

            $size = $_FILES['files']['size'][$i];
            $tmp  = $_FILES['files']['tmp_name'][$i];

            if ($size > 10 * 1024 * 1024) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext === 'exe') continue;

            $safe = uniqid('att_') . '.' . $ext;
            if (!move_uploaded_file($tmp, "../uploads/$safe")) continue;

            mysqli_query($conn, "
                INSERT INTO ticket_attachments (ticket_id, filename)
                VALUES ($ticket_id, '/cr/uploads/$safe')
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

    <form method="post" enctype="multipart/form-data" id="ticketForm">

      <div class="form-group">
        <label>Title</label>
        <input name="title" required>
      </div>

        <div class="form-group">
          <label>Category</label>
                    <span class="form-hint">
            (Silahkan pilih Perubahan atau Penambahan pada fitur SIMRS
          </span>
          <select name="category" required>
            <option value="perubahan">Perubahan</option>
            <option value="penambahan">Penambahan</option>
          </select>
        </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" required></textarea>
      </div>

      <!-- ================= DRAG & DROP ================= -->
      <div class="form-group">
        <label>
          Attachments
          <span class="form-hint">
            (drag & drop / klik · max 10MB · kecuali .exe)
          </span>
        </label>

        <div class="drop-zone" id="dropZone">
          <input type="file" name="files[]" id="attachments" multiple hidden>
          <div class="drop-text">
            📎 Drag & drop file di sini<br>
            <small>atau klik untuk memilih file</small>
          </div>
        </div>

        <div id="file-count" class="file-count">Belum ada file dipilih</div>
        <ul id="file-list" class="file-list"></ul>
      </div>

      <button type="submit">Submit Ticket</button>
    </form>
  </div>
</div>

<?php include "../includes/footer.php"; ?>

<!-- ================= JS DRAG DROP + REMOVE ================= -->
<script>
const input = document.getElementById('attachments');
const dropZone = document.getElementById('dropZone');
const fileList = document.getElementById('file-list');
const fileCount = document.getElementById('file-count');

let filesArr = [];

/* ===== RENDER LIST ===== */
function renderList() {
    fileList.innerHTML = '';
    fileCount.textContent = filesArr.length
        ? filesArr.length + ' file dipilih'
        : 'Belum ada file dipilih';

    filesArr.forEach((file, i) => {
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        const li = document.createElement('li');
        li.innerHTML = `
            <span class="file-name">📄 ${file.name}</span>
            <span class="file-size">${sizeMB} MB</span>
            <button type="button" class="file-remove" onclick="removeFile(${i})">✖</button>
        `;
        fileList.appendChild(li);
    });

    const dt = new DataTransfer();
    filesArr.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

/* ===== REMOVE FILE ===== */
function removeFile(index) {
    filesArr.splice(index, 1);
    renderList();
}

/* ===== VALIDATE & ADD ===== */
function addFiles(newFiles) {
    Array.from(newFiles).forEach(file => {
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext === 'exe') return;
        if (file.size > 10 * 1024 * 1024) return;
        filesArr.push(file);
    });
    renderList();
}

/* ===== INPUT CHANGE ===== */
input.addEventListener('change', e => addFiles(e.target.files));

/* ===== DROP EVENTS ===== */
dropZone.addEventListener('click', () => input.click());

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () =>
    dropZone.classList.remove('dragover')
);

dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    addFiles(e.dataTransfer.files);
});
</script>
