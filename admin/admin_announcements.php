<?php
include '../db.php';
session_start();

/* ================= ADMIN CHECK ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$db = isset($pdo) ? $pdo : $conn;


/* =====================================================
   HANDLE FORM ACTIONS
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= ADD ANNOUNCEMENT ================= */
    if (isset($_POST['add_announcement'])) {

        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? 'ongoing';

        $subtitle = $_POST['subtitle'] ?? null;
        $short_desc = $_POST['short_desc'] ?? null;
        $highlights = $_POST['highlights'] ?? null;

        $badge = $_POST['badge'] ?? null;
        $badge_priority = $_POST['badge_priority'] ?? null;

        $event_date = $_POST['event_date'] ?? null;
        $reg_deadline = $_POST['reg_deadline'] ?? null;
        $location = $_POST['location'] ?? null;

        $button_text = $_POST['button_text'] ?? 'Register Now';

        $image_path = null;

        /* ===== IMAGE UPLOAD ===== */
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {

            $upload_dir = "../uploads/announcements/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_tmp = $_FILES['image']['tmp_name'];
            $file_info = getimagesize($file_tmp);

            if ($file_info !== false) {

                $allowed = ['jpg','jpeg','png','webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {

                    $file_name = uniqid('ann_', true) . '.' . $ext;
                    $target = $upload_dir . $file_name;

                    if (move_uploaded_file($file_tmp, $target)) {
                        $image_path = "uploads/announcements/" . $file_name;
                    }
                }
            }
        }

        if ($title) {

            if ($db instanceof PDO) {

                $stmt = $db->prepare("
                    INSERT INTO announcements
                    (title, subtitle, short_desc, highlights, type,
                     badge, badge_priority, event_date, reg_deadline,
                     location, button_text, image, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
                ");

                $stmt->execute([
                    $title, $subtitle, $short_desc, $highlights,
                    $type, $badge, $badge_priority,
                    $event_date, $reg_deadline,
                    $location, $button_text,
                    $image_path
                ]);

            } else {

                $stmt = $db->prepare("
                    INSERT INTO announcements
                    (title, subtitle, short_desc, highlights, type,
                     badge, badge_priority, event_date, reg_deadline,
                     location, button_text, image, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
                ");

                $stmt->bind_param(
                    "sssssssssssss",
                    $title, $subtitle, $short_desc, $highlights,
                    $type, $badge, $badge_priority,
                    $event_date, $reg_deadline,
                    $location, $button_text,
                    $image_path
                );

                $stmt->execute();
            }

            $_SESSION['success'] = "Announcement added successfully.";

        } else {

            $_SESSION['error'] = "Title is required.";

        }
    }


    /* ================= UPDATE ANNOUNCEMENT ================= */
    if (isset($_POST['update_announcement'])) {

        $id = intval($_POST['announcement_id']);

        $title = trim($_POST['title']);

        $subtitle = $_POST['subtitle'] ?? null;
        $short_desc = $_POST['short_desc'] ?? null;
        $highlights = $_POST['highlights'] ?? null;

        $type = $_POST['type'];
        $badge = $_POST['badge'] ?? null;
        $badge_priority = $_POST['badge_priority'] ?? null;

        $event_date = $_POST['event_date'] ?? null;
        $reg_deadline = $_POST['reg_deadline'] ?? null;
        $location = $_POST['location'] ?? null;

        $button_text = $_POST['button_text'] ?? 'Register Now';

        $image_path = null;

        /* IMAGE UPLOAD */
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {

            $upload_dir = "../uploads/announcements/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir,0755,true);
            }

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (in_array($ext,$allowed)) {

                $file_name = uniqid('ann_', true) . '.' . $ext;
                $target = $upload_dir.$file_name;

                if(move_uploaded_file($_FILES['image']['tmp_name'],$target)){
                    $image_path = "uploads/announcements/".$file_name;
                }

            }
        }

        if ($db instanceof PDO) {

            if ($image_path) {

                $stmt = $db->prepare("
                    UPDATE announcements SET
                    title=?, subtitle=?, short_desc=?, highlights=?,
                    type=?, badge=?, badge_priority=?,
                    event_date=?, reg_deadline=?, location=?,
                    button_text=?, image=?
                    WHERE id=?
                ");

                $stmt->execute([
                    $title,$subtitle,$short_desc,$highlights,
                    $type,$badge,$badge_priority,
                    $event_date,$reg_deadline,$location,
                    $button_text,$image_path,$id
                ]);

            } else {

                $stmt = $db->prepare("
                    UPDATE announcements SET
                    title=?, subtitle=?, short_desc=?, highlights=?,
                    type=?, badge=?, badge_priority=?,
                    event_date=?, reg_deadline=?, location=?,
                    button_text=?
                    WHERE id=?
                ");

                $stmt->execute([
                    $title,$subtitle,$short_desc,$highlights,
                    $type,$badge,$badge_priority,
                    $event_date,$reg_deadline,$location,
                    $button_text,$id
                ]);
            }

        } else {

            if ($image_path) {

                $stmt = $db->prepare("
                    UPDATE announcements SET
                    title=?, subtitle=?, short_desc=?, highlights=?,
                    type=?, badge=?, badge_priority=?,
                    event_date=?, reg_deadline=?, location=?,
                    button_text=?, image=?
                    WHERE id=?
                ");

                $stmt->bind_param(
                    "sssssssssssssi",
                    $title,$subtitle,$short_desc,$highlights,
                    $type,$badge,$badge_priority,
                    $event_date,$reg_deadline,$location,
                    $button_text,$image_path,$id
                );

            } else {

                $stmt = $db->prepare("
                    UPDATE announcements SET
                    title=?, subtitle=?, short_desc=?, highlights=?,
                    type=?, badge=?, badge_priority=?,
                    event_date=?, reg_deadline=?, location=?,
                    button_text=?
                    WHERE id=?
                ");

                $stmt->bind_param(
                    "ssssssssssssi",
                    $title,$subtitle,$short_desc,$highlights,
                    $type,$badge,$badge_priority,
                    $event_date,$reg_deadline,$location,
                    $button_text,$id
                );
            }

            $stmt->execute();
        }

        $_SESSION['success'] = "Announcement updated successfully.";
    }


    /* ================= DELETE ANNOUNCEMENT ================= */
    if (isset($_POST['delete_announcement'])) {

        $id = intval($_POST['announcement_id']);

        if ($db instanceof PDO) {

            $stmt = $db->prepare("SELECT image FROM announcements WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

        } else {

            $stmt = $db->prepare("SELECT image FROM announcements WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
        }

        if (!empty($row['image']) && file_exists("../".$row['image'])) {
            unlink("../".$row['image']);
        }

        if ($db instanceof PDO) {

            $stmt = $db->prepare("DELETE FROM announcements WHERE id=?");
            $stmt->execute([$id]);

        } else {

            $stmt = $db->prepare("DELETE FROM announcements WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
        }

        $_SESSION['success'] = "Announcement deleted successfully.";
    }


    /* ================= TOGGLE STATUS ================= */
    if (isset($_POST['toggle_status'])) {

        $id = intval($_POST['announcement_id']);
        $current = $_POST['current_status'];

        $new = $current === 'active' ? 'inactive' : 'active';

        if ($db instanceof PDO) {

            $stmt = $db->prepare("UPDATE announcements SET status=? WHERE id=?");
            $stmt->execute([$new,$id]);

        } else {

            $stmt = $db->prepare("UPDATE announcements SET status=? WHERE id=?");
            $stmt->bind_param("si",$new,$id);
            $stmt->execute();
        }

        $_SESSION['success'] = "Status updated.";
    }

    header("Location: admin_announcements.php");
    exit();
}


/* =====================================================
   FETCH ANNOUNCEMENTS
===================================================== */

if ($db instanceof PDO) {

    $stmt = $db->query("SELECT * FROM announcements ORDER BY created_at DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $stmt = $db->prepare("SELECT * FROM announcements ORDER BY created_at DESC");
    $stmt->execute();
    $announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Announcements - Alta iHUB</title>
    <link rel="stylesheet" href="admin.css">

</head>
<body>

    <?php include 'admin_header.php'; ?>

    <div class="container admin-dashboard">
        <h1>Announcements Management</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="message success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- ================= EXISTING ================= -->
        <section class="announcement-list">
            <h2>Existing Announcements</h2>

            <?php if (!empty($announcements)): ?>
                <table class="announcement-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $row): ?>
                            <tr>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="../<?= $row['image'] ?>" width="60">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= htmlspecialchars($row['event_date']) ?></td>
                                <td><?= ucfirst($row['status']) ?></td>
                                <td class="actions-cell">

                                    <button
                                    class="btn-status edit-btn"

                                    data-id="<?= $row['id'] ?>"
                                    data-title="<?= htmlspecialchars($row['title']) ?>"
                                    data-subtitle="<?= htmlspecialchars($row['subtitle']) ?>"
                                    data-short="<?= htmlspecialchars($row['short_desc']) ?>"
                                    data-type="<?= $row['type'] ?>"
                                    data-date="<?= htmlspecialchars($row['event_date']) ?>"
                                    data-deadline="<?= htmlspecialchars($row['reg_deadline']) ?>"
                                    data-location="<?= htmlspecialchars($row['location']) ?>"
                                    data-badge="<?= htmlspecialchars($row['badge']) ?>"
                                    data-badge_priority="<?= htmlspecialchars($row['badge_priority']) ?>"
                                    data-button="<?= htmlspecialchars($row['button_text']) ?>"
                                    data-image="<?= $row['image'] ?>"

                                    >
                                    Edit
                                </button>

                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="current_status" value="<?= $row['status'] ?>">
                                    <button class="btn-status" name="toggle_status">
                                        <?= $row['status']=='active'?'Deactivate':'Activate' ?>
                                    </button>
                                </form>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this announcement?');">
                                    <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
                                    <button class="btn-delete" name="delete_announcement">Delete</button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>
    </section>

<!-- ================= ADD FORM ================= -->
<section class="form-section">
    <h2>Add New Announcement</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle">
            </div>
            <div class="form-group">
                <label>Short Description</label>
                <input type="text" name="short_desc">
            </div>
        </div>

        <div class="form-group">
            <label>Highlights (Press ENTER for new bullet)</label>

            <textarea 
            name="highlights" 
            rows="4"
            placeholder="Example:

            Networking with industry leaders
            Startup pitch opportunities
            Innovation workshops
            Free mentoring sessions"></textarea>

        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <option value="latest">Latest</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="upcoming" selected>Upcoming</option>
                </select>
            </div>
            <div class="form-group">
                <label>Event Date</label>
                <input type="text" name="event_date" placeholder="e.g., Oct 24-26, 2025">
            </div>
            <div class="form-group">
                <label style="color: #f59e0b;">Registration Deadline</label>
                <input type="text" name="reg_deadline" placeholder="e.g., Feb 20, 2026">
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Main Badge</label>
                <input type="text" name="badge" placeholder="e.g., Event">
            </div>
            <div class="form-group">
                <label>Priority Badge</label>
                <select name="badge_priority">
                    <option value="">None</option>
                    <option value="High Priority">High Priority</option>
                    <option value="Medium Priority">Medium Priority</option>
                    <option value="Low Priority">Low Priority</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location">
            </div>
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="button_text" value="Register Now">
            </div>
        </div>

        <div class="form-group">
            <label>Upload Image</label>
            <input type="file" name="image" accept="image/*" id="image_upload" class="file-input">
            <img id="image_preview_add" style="margin-top:10px;max-width:200px;border-radius:8px;display:none;">
        </div>
        <input type="hidden" name="add_announcement" value="1">
        <button type="submit" class="btn btn-primary btn-block">Add Announcement</button>
    </form>

</section>
</div>
<div id="editModal" class="admin-modal">
    <div class="admin-modal-box">

        <h2>Edit Announcement</h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="announcement_id" id="edit_id">
            <input type="hidden" name="update_announcement" value="1">

            <div class="modal-grid">

                <div class="form-group">
                    <label for="edit_title">Title</label>
                    <input type="text" name="title" id="edit_title">
                </div>

                <div class="form-group">
                    <label for="edit_subtitle">Subtitle</label>
                    <input type="text" name="subtitle" id="edit_subtitle">
                </div>

                <div class="form-group">
                    <label for="edit_short">Short Description</label>
                    <input type="text" name="short_desc" id="edit_short">
                </div>

                <div class="form-group">
                    <label for="edit_type">Type</label>
                    <select name="type" id="edit_type">
                        <option value="latest">Latest</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="upcoming">Upcoming</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="edit_highlights">Highlights (Press ENTER for new bullet)</label>
                    <textarea 
                        name="highlights" 
                        id="edit_highlights"
                        rows="4"
                        placeholder="Press enter to form new line."></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_date">Event Date</label>
                    <input type="text" name="event_date" id="edit_date">
                </div>

                <div class="form-group">
                    <label style="color: #f59e0b;">Registration Deadline</label>
                    <input type="text" name="reg_deadline" id="edit_deadline" placeholder="e.g., Feb 20, 2026">
                </div>

                <div class="form-group">
                    <label for="edit_location">Location</label>
                    <input type="text" name="location" id="edit_location">
                </div>

                <div class="form-group">
                    <label for="edit_badge">Main Badge</label>
                    <input type="text" name="badge" id="edit_badge">
                </div>

                <div class="form-group">
                    <label for="edit_badge_priority">Priority Badge</label>
                    <select name="badge_priority" id="edit_badge_priority">
                        <option value="">None</option>
                        <option value="High Priority">High Priority</option>
                        <option value="Medium Priority">Medium Priority</option>
                        <option value="Low Priority">Low Priority</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_button">Button Text</label>
                    <input type="text" name="button_text" id="edit_button">
                </div>

                <div class="form-group full">
                    <label>Current Image</label>
                    <img id="edit_preview" class="edit-image-preview" alt="Current Announcement Image">
                    <label style="margin-top:10px;">Change Image</label>
                    <input type="file" name="image" id="edit_image_upload" accept="image/*">
                </div>

            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-primary">Update Announcement</button>
                <button type="button" onclick="closeModal()" class="btn-delete">Cancel</button>
            </div>

        </form>

    </div>
</div>
    <script>
/********** MODAL & FORM SCRIPT **********/

        const modal = document.getElementById("editModal");

// Open edit modal and populate fields
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", function(){
                document.getElementById("edit_id").value = this.dataset.id;
                document.getElementById("edit_title").value = this.dataset.title;
                document.getElementById("edit_subtitle").value = this.dataset.subtitle;
                document.getElementById("edit_short").value = this.dataset.short;
                document.getElementById("edit_type").value = this.dataset.type;
                document.getElementById("edit_date").value = this.dataset.date;
                document.getElementById("edit_deadline").value = this.dataset.deadline;
                document.getElementById("edit_location").value = this.dataset.location;
                document.getElementById("edit_badge").value = this.dataset.badge;
                document.getElementById("edit_badge_priority").value = this.dataset.badge_priority;
                document.getElementById("edit_button").value = this.dataset.button;

                const img = this.dataset.image;
                if(img){
                    document.getElementById("edit_preview").src = "../" + img;
                }

                modal.classList.add("open");
            });
        });

// Close modal
        function closeModal(){
            modal.classList.remove("open");
        }

        window.addEventListener("keydown", function(e){
            if(e.key === "Escape") closeModal();
        });

        modal.addEventListener("click", function(e){
            if(e.target === modal) closeModal();
        });

// Prevent form submit on Enter outside textarea
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("keydown", function(e){
                if(e.key === "Enter" && e.target.tagName !== "TEXTAREA"){
                    e.preventDefault();
                }
            });
        });

/********** IMAGE PREVIEW FOR ADD & EDIT **********/
        function setupImagePreview(fileInputId, imgPreviewId){
            const fileInput = document.getElementById(fileInputId);
            const imgPreview = document.getElementById(imgPreviewId);
            if(fileInput){
                fileInput.addEventListener("change", function(){
                    const file = this.files[0];
                    if(file){
                        const reader = new FileReader();
                        reader.onload = function(e){
                            imgPreview.src = e.target.result;
                            imgPreview.style.display = "block";
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        setupImagePreview("image_upload", "image_preview_add");
        setupImagePreview("edit_image_upload", "edit_preview");

/********** HIGHLIGHTS AUTO-BULLET PREVIEW **********/
        function setupHighlightPreview(textareaSelector){
            const textarea = document.querySelector(textareaSelector);
            if(!textarea) return;

    // Create preview element
            let preview = document.createElement('ul');
            preview.style.marginTop = "5px";
            textarea.parentNode.appendChild(preview);

            function updatePreview(){
                const lines = textarea.value.split(/\r?\n/).filter(line => line.trim() !== "");
                preview.innerHTML = "";
                lines.forEach(line => {
                    let li = document.createElement("li");
                    li.textContent = line;
                    preview.appendChild(li);
                });
            }

            textarea.addEventListener("input", updatePreview);
            updatePreview();
        }

setupHighlightPreview('textarea[name="highlights"]');
setupHighlightPreview('#editModal textarea[name="highlights"]'); 
</script>
</body>

</html>