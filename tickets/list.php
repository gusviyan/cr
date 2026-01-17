<?php
include "../config/database.php";
include "../config/auth.php";
include "../config/app.php";

include "../includes/header.php";
include "../includes/sidebar.php";

/* ambil tiket milik user + admin assigned */
$q = mysqli_query($conn,"
    SELECT 
        t.id,
        t.title,
        t.status,
        a.name AS assigned_name
    FROM tickets t
    LEFT JOIN users a ON a.id = t.assigned_by
    WHERE t.user_id = ".$_SESSION['user']['id']."
    ORDER BY t.created_at DESC
");
?>

<div class="card">
    <h3>My Tickets</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Assigned To</th>
        </tr>

        <?php while($t = mysqli_fetch_assoc($q)): ?>
        <tr>
            <td><?= $t['id'] ?></td>

            <td>
                <a href="detail.php?id=<?= $t['id'] ?>">
                    <?= htmlspecialchars($t['title']) ?>
                </a>
            </td>

            <td>
                <span class="badge <?= str_replace(' ', '-', strtolower($t['status'])) ?>">
                    <?= $t['status'] ?>
                </span>
            </td>

            <td>
                <?= $t['assigned_name'] 
                    ? htmlspecialchars($t['assigned_name']) 
                    : '-' ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
