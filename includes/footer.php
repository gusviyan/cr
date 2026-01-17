</div> <!-- content -->
</div> <!-- layout -->

<div class="footer">
  <a href=https://gusviyan.github.io/portofolio/ target="_blank" rel="noopener noreferrer">© 2026 Gusviyan - IT Dept RS Permata Pamulang | All Rights Reserved</a>
</div>

</body>
</html>
<script>
const icon  = document.getElementById('notif-icon');
const box   = document.getElementById('notif-dropdown');
const list  = document.getElementById('notif-list');
const badge = document.getElementById('notif-badge');

icon.onclick = () => box.classList.toggle('show');

function loadNotif() {
  fetch('/cr/notifications/fetch.php')
    .then(r => r.json())
    .then(res => {
      list.innerHTML = '';

      /* BADGE */
      badge.innerText = res.unread > 0 ? res.unread : '';
      badge.style.display = res.unread > 0 ? 'inline-block' : 'none';

      if (!res.items.length) {
        list.innerHTML = '<div class="notif-item empty">Tidak ada notifikasi</div>';
        return;
      }

      res.items.forEach(n => {
        list.innerHTML += `
          <div class="notif-item ${n.is_read==0?'unread':''}"
               onclick="openNotif(${n.id}, '${n.link}')">
            ${n.message}
            <div class="time">${n.created_at}</div>
          </div>`;
      });
    });
}

function openNotif(id, link) {
  fetch('/cr/notifications/read.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id=' + id
  }).then(() => {
    window.location = link;
  });
}

/* INIT */
loadNotif();
setInterval(loadNotif, 15000);
</script>

