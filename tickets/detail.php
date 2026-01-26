<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$id = intval($_GET['id']);

/* ================= TICKET ================= */
$t = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        t.*, 
        u.name AS user_name,
        a.name AS assigned_name
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    LEFT JOIN users a ON a.id = t.assigned_by
    WHERE t.id = $id
"));

if (!$t) die("Ticket tidak ditemukan");

/* ================= AKSES ================= */
if ($_SESSION['user']['role'] !== 'admin' && $t['user_id'] != $_SESSION['user']['id']) {
    die("Akses ditolak");
}

/* ================= ATTACHMENTS ================= */
$attachments = mysqli_query($conn,"
    SELECT * FROM ticket_attachments
    WHERE ticket_id = $id
");

/* ================= TIMELINE ================= */
$logs = mysqli_query($conn,"
    SELECT * FROM ticket_status_logs
    WHERE ticket_id = $id
    ORDER BY created_at ASC
");

/* ================= KOMENTAR ================= */
$comments = mysqli_query($conn,"
    SELECT c.*, u.name, u.role
    FROM ticket_comments c
    JOIN users u ON u.id = c.user_id
    WHERE c.ticket_id = $id
    ORDER BY c.created_at ASC
");
?>

<div class="detail-grid">

<!-- ================= LEFT ================= -->
<div class="detail-card">

<h3><?= htmlspecialchars($t['title']) ?></h3>
<hr>

<p><?= nl2br(htmlspecialchars($t['description'])) ?></p>
<hr>

<p>
    <b>Status:</b> <?= $t['status'] ?><br>
    <b>Dibuat oleh:</b> <?= htmlspecialchars($t['user_name']) ?><br>
    <b>Tanggal:</b> <?= date('d-m-Y', strtotime($t['created_at'])) ?><br>
    <b>Assigned To:</b> <?= $t['assigned_name'] ?? '-' ?>
</p>

<hr>

<h4>Attachments</h4>
<?php if (mysqli_num_rows($attachments)==0): ?>
    <em>Tidak ada lampiran</em>
<?php endif; ?>

<?php while($a=mysqli_fetch_assoc($attachments)): ?>
    <div class="attachment-item">
        📎 <a href="<?= htmlspecialchars($a['filename']) ?>" target="_blank">
            <?= basename($a['filename']) ?>
        </a>
    </div>
<?php endwhile; ?>

<hr>

<h4>Timeline Status</h4>
<?php if (mysqli_num_rows($logs)==0): ?>
    <em>Belum ada perubahan status</em>
<?php endif; ?>

<?php while($l=mysqli_fetch_assoc($logs)): ?>
    <?= $l['old_status'] ?> → <?= $l['new_status'] ?>
    (<?= $l['created_at'] ?>)<br>
<?php endwhile; ?>

</div>

<!-- ================= RIGHT ================= -->
<div class="detail-card">

<h3>Diskusi / Komentar</h3>

<?php if (mysqli_num_rows($comments)==0): ?>
    <em>Belum ada komentar</em>
<?php endif; ?>

<?php while($c=mysqli_fetch_assoc($comments)): ?>

<?php
$ca = mysqli_query($conn,"
    SELECT * FROM ticket_comment_attachments
    WHERE comment_id = ".$c['id']
);
?>

<div class="comment-box <?= $c['role']=='admin'?'admin':'' ?>">
    <div class="comment-author">
        <?= htmlspecialchars($c['name']) ?>
        <?= $c['role']=='admin'?'(Admin)':'' ?>
    </div>

    <div class="comment-text">
        <?= nl2br(htmlspecialchars($c['comment'])) ?>
    </div>

    <?php if (mysqli_num_rows($ca)>0): ?>
        <div class="comment-attachments">
            <?php while($f=mysqli_fetch_assoc($ca)): ?>
                📎 <a href="<?= htmlspecialchars($f['filename']) ?>" target="_blank">
                    <?= basename($f['filename']) ?>
                </a><br>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <div class="comment-time"><?= $c['created_at'] ?></div>
</div>
<?php endwhile; ?>

<?php if ($t['status']!=='Closed' || $_SESSION['user']['role']==='admin'): ?>

<form method="post"
      action="comment.php"
      enctype="multipart/form-data"
      class="comment-form">

<input type="hidden" name="ticket_id" value="<?= $id ?>">

<textarea name="comment" placeholder="Tulis komentar..." required></textarea>

<div class="comment-attach">
<label>Attachment (drag & drop / klik)</label>

<div class="drop-zone small" id="commentDropZone">
    <input type="file" name="files[]" id="commentFiles" multiple hidden>
    <div class="drop-text">
        📎 Drag & drop file<br><small>atau klik</small>
    </div>
</div>

<div id="comment-file-count">Belum ada file</div>
<ul id="comment-file-list"></ul>
</div>

<button type="submit">Kirim Komentar</button>
</form>

<?php else: ?>
<em>Komentar ditutup (ticket Closed)</em>
<?php endif; ?>

</div>
</div>

<script>
const cInput=document.getElementById('commentFiles');
const cDrop=document.getElementById('commentDropZone');
const cList=document.getElementById('comment-file-list');
const cCount=document.getElementById('comment-file-count');
let files=[];

function render(){
    cList.innerHTML='';
    cCount.textContent=files.length?files.length+' file':'Belum ada file';
    const dt=new DataTransfer();
    files.forEach((f,i)=>{
        dt.items.add(f);
        const li=document.createElement('li');
        li.innerHTML=`📄 ${f.name}
        <button type="button" onclick="remove(${i})">✖</button>`;
        cList.appendChild(li);
    });
    cInput.files=dt.files;
}

function remove(i){files.splice(i,1);render();}

function add(fs){
    Array.from(fs).forEach(f=>{
        if(f.size>10*1024*1024) return;
        if(f.name.split('.').pop().toLowerCase()==='exe') return;
        files.push(f);
    });
    render();
}

cInput.addEventListener('change',e=>add(e.target.files));
cDrop.addEventListener('click',()=>cInput.click());
cDrop.addEventListener('dragover',e=>{e.preventDefault();cDrop.classList.add('dragover');});
cDrop.addEventListener('dragleave',()=>cDrop.classList.remove('dragover'));
cDrop.addEventListener('drop',e=>{
    e.preventDefault();
    cDrop.classList.remove('dragover');
    add(e.dataTransfer.files);
});
</script>

<?php include "../includes/footer.php"; ?>
