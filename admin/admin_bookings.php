<?php
include 'admin_header.php';
include '../db.php';

// Total pending bookings
$stmt = $pdo->query("
    SELECT COUNT(*) AS total_pending 
    FROM bookings 
    WHERE status = 'pending'
    ");
$total_pending = $stmt->fetch()['total_pending'];

// Total approved bookings
$stmt = $pdo->query("
    SELECT COUNT(*) AS total_approved 
    FROM bookings 
    WHERE status = 'approved'
    ");
$total_approved = $stmt->fetch()['total_approved'];

// Total users
$stmt = $pdo->query("
    SELECT COUNT(*) AS total_users 
    FROM users
    ");
$total_users = $stmt->fetch()['total_users'];
?>
<link rel="stylesheet" href="admin.css">
<main class="page-bg admin-dashboard">

    <section class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Quick overview of bookings and users</p>
    </section>

    <section class="admin-section">
        <div class="dashboard-cards grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            
            <div class="card p-6" style="background-color: var(--dashboard-card-bg); border-radius: var(--radius); color: var(--foreground);">
                <h3 class="mb-2">Pending Bookings</h3>
                <p class="text-2xl font-bold"><?= $total_pending ?></p>
            </div>

            <div class="card p-6" style="background-color: var(--dashboard-card-bg); border-radius: var(--radius); color: var(--foreground);">
                <h3 class="mb-2">Approved Bookings</h3>
                <p class="text-2xl font-bold"><?= $total_approved ?></p>
            </div>

            <div class="card p-6" style="background-color: var(--dashboard-card-bg); border-radius: var(--radius); color: var(--foreground);">
                <h3 class="mb-2">Total Users</h3>
                <p class="text-2xl font-bold"><?= $total_users ?></p>
            </div>

        </div>
    </section>

    <section class="admin-section mt-12">
        <h2 class="mb-4">Recent Bookings</h2>

        <div class="admin-table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Service</th>
                        <th>Location</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT 
                        b.id,
                        b.service_name,
                        b.location,
                        b.appointment_date,
                        b.appointment_time,
                        b.status,
                        u.name AS user_name
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        ORDER BY b.created_at DESC
                        LIMIT 10
                        ");
                    $bookings = $stmt->fetchAll();
                    foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['user_name']) ?></td>
                            <td><?= htmlspecialchars($b['service_name']) ?></td>
                            <td><?= htmlspecialchars($b['location']) ?></td>
                            <td>
                                <?= date('M d, Y', strtotime($b['appointment_date'])) ?>
                                <br>
                                <small><?= date('h:i A', strtotime($b['appointment_time'])) ?></small>
                            </td>
                            <td>
                                <?php if ($b['status'] === 'pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                    <div class="actions-cell" style="margin-top:5px;">
                                        <form method="post" action="admin_update_booking_status.php" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                        </form>
                                        <form method="post" action="admin_update_booking_status.php" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-delete btn-sm">Reject</button>
                                        </form>
                                    </div>
                                <?php elseif ($b['status'] === 'approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>
</body>
</html>