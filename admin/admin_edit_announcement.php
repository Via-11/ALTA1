<?php
include '../session_check.php';
check_session('admin'); // Only allow admins
include 'admin_header.php';
include '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$db = isset($pdo) ? $pdo : $conn;

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header("Location: admin_announcements.php");
    exit();
}

/* ================= FETCH DATA ================= */

if ($db instanceof PDO) {
    $stmt = $db->prepare("SELECT * FROM announcements WHERE id=?");
    $stmt->execute([$id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->prepare("SELECT * FROM announcements WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $announcement = $stmt->get_result()->fetch_assoc();
}

if (!$announcement) {
    header("Location: admin_announcements.php");
    exit();
}

/* ================= UPDATE ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = $_POST['type'];

    $subtitle = $_POST['subtitle'] ?? null;
    $short_desc = $_POST['short_desc'] ?? null;
    $highlights = $_POST['highlights'] ?? null;
    $badge = $_POST['badge'] ?? null;
    $badge_priority = $_POST['badge_priority'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $reg_deadline = $_POST['reg_deadline'] ?? null;
    $location = $_POST['location'] ?? null;
    $button_text = $_POST['button_text'] ?? null;

    if ($db instanceof PDO) {

        $stmt = $db->prepare("
            UPDATE announcements SET
            title=?, subtitle=?, short_desc=?, message=?, highlights=?,
            type=?, badge=?, badge_priority=?, event_date=?, reg_deadline=?,
            location=?, button_text=?
            WHERE id=?
        ");

        $stmt->execute([
            $title,$subtitle,$short_desc,$message,$highlights,
            $type,$badge,$badge_priority,$event_date,$reg_deadline,
            $location,$button_text,$id
        ]);

    } else {

        $stmt = $db->prepare("
            UPDATE announcements SET
            title=?, subtitle=?, short_desc=?, message=?, highlights=?,
            type=?, badge=?, badge_priority=?, event_date=?, reg_deadline=?,
            location=?, button_text=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "ssssssssssssi",
            $title,$subtitle,$short_desc,$message,$highlights,
            $type,$badge,$badge_priority,$event_date,$reg_deadline,
            $location,$button_text,$id
        );

        $stmt->execute();
    }

    $_SESSION['success'] = "Announcement updated successfully.";
    header("Location: admin_announcements.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Announcement</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>

<?php include 'admin_header.php'; ?>

<div class="admin-dashboard">

<h1>Edit Announcement</h1>

<form method="POST" class="admin-form">

<div class="form-group">
<label>Title</label>
<input type="text" name="title" value="<?= htmlspecialchars($announcement['title']) ?>" required>
</div>

<div class="form-group">
<label>Subtitle</label>
<input type="text" name="subtitle" value="<?= htmlspecialchars($announcement['subtitle']) ?>">
</div>

<div class="form-group">
<label>Short Description</label>
<input type="text" name="short_desc" value="<?= htmlspecialchars($announcement['short_desc']) ?>">
</div>

<div class="form-group">
<label>Message</label>
<textarea name="message" rows="5"><?= htmlspecialchars($announcement['message']) ?></textarea>
</div>

<div class="form-group">
<label>Highlights</label>
<textarea name="highlights"><?= htmlspecialchars($announcement['highlights']) ?></textarea>
</div>

<div class="form-group">
<label>Type</label>

<select name="type">
<option value="latest" <?= $announcement['type']=='latest'?'selected':'' ?>>Latest</option>
<option value="ongoing" <?= $announcement['type']=='ongoing'?'selected':'' ?>>Ongoing</option>
<option value="upcoming" <?= $announcement['type']=='upcoming'?'selected':'' ?>>Upcoming</option>
</select>

</div>

<div class="form-group">
<label>Event Date</label>
<input type="text" name="event_date" value="<?= htmlspecialchars($announcement['event_date']) ?>">
</div>

<div class="form-group">
<label>Registration Deadline</label>
<input type="text" name="reg_deadline" value="<?= htmlspecialchars($announcement['reg_deadline']) ?>">
</div>

<div class="form-group">
<label>Location</label>
<input type="text" name="location" value="<?= htmlspecialchars($announcement['location']) ?>">
</div>

<div class="form-group">
<label>Button Text</label>
<input type="text" name="button_text" value="<?= htmlspecialchars($announcement['button_text']) ?>">
</div>

<div class="form-group">
<label>Main Badge</label>
<input type="text" name="badge" value="<?= htmlspecialchars($announcement['badge']) ?>">
</div>

<div class="form-group">
<label>Priority Badge</label>
<input type="text" name="badge_priority" value="<?= htmlspecialchars($announcement['badge_priority']) ?>">
</div>

<button type="submit" class="btn-primary">Update Announcement</button>

</form>

</div>

</body>
</html>