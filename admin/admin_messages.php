<?php
session_start();
include '../db.php';
include 'admin_header.php';

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['user_id'];

/*FETCH MESSAGES*/
$stmtMessages = $conn->prepare("
    SELECT m.*, u.name AS sender_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmtMessages->bind_param("i", $adminId);
$stmtMessages->execute();
$resultMessages = $stmtMessages->get_result();

/* Unread */
$stmtUnread = $conn->prepare("
    SELECT m.*, u.name AS sender_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ? AND m.status='unread'
    ORDER BY m.created_at DESC
");
$stmtUnread->bind_param("i", $adminId);
$stmtUnread->execute();
$resultUnread = $stmtUnread->get_result();

/* Read */
$stmtRead = $conn->prepare("
    SELECT m.*, u.name AS sender_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ? AND m.status='read'
    ORDER BY m.created_at DESC
");
$stmtRead->bind_param("i", $adminId);
$stmtRead->execute();
$resultRead = $stmtRead->get_result();

/* Sent */
$stmtSent = $conn->prepare("
    SELECT m.*, u.name AS receiver_name
    FROM messages m
    LEFT JOIN users u ON m.receiver_id = u.user_id
    WHERE m.sender_id = ?
    ORDER BY m.created_at DESC
");
$stmtSent->bind_param("i", $adminId);
$stmtSent->execute();
$resultSent = $stmtSent->get_result();
?>

<link rel="stylesheet" href="../styles/dashboard.css">

<div class="min-h-screen bg-background py-8 container">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Admin Messages</h1>
            <p class="text-gray-400">Manage and reply to messages from users</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="dashboardOpenComposeModal()">
            <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Message
        </button>
    </div>
    <!-- TABS -->
    <div class="flex gap-2 mb-6">
        <button class="tab-btn active" data-tab="unreadTab"> All</button>
        <button class="tab-btn active" data-tab="unreadTab">📩 Unread</button>
        <button class="tab-btn" data-tab="readTab">✅ Read</button>
        <button class="tab-btn" data-tab="sentTab">📤 Sent</button>
    </div>

    <div class="grid lg-grid-cols-3 gap-6">
        <!-- All Message List -->
        <div id="allTab" class="tab-content">
            <div class="messages-card">
                <div class="messages-card-content p-4">
                    <div class="mb-4">
                        <input type="text" id="message-search" class="form-input" placeholder="Search messages...">
                    </div>
                    <div class="space-y-2">
                        <?php if ($resultMessages->num_rows > 0): ?>
                        <?php while ($msg = $resultMessages->fetch_assoc()): ?>
                            <div class="messages-list-item <?= ($msg['status'] === 'unread') ? 'unread' : '' ?>"
                                data-id="<?= $msg['id'] ?>"
                                data-sender="<?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>"
                                data-subject="<?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>"
                                data-message="<?= htmlspecialchars($msg['message']) ?>"
                                data-date="<?= date("M d, Y h:i A", strtotime($msg['created_at'])) ?>">
                                <div class="flex items-start gap-3">
                                    <div class="avatar">
                                        <?= strtoupper(substr($msg['sender_name'] ?? 'US', 0, 2)) ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold text-white text-sm">
                                                <?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-white mb-1">
                                            <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?= htmlspecialchars(substr($msg['message'], 0, 50)) ?>...
                                        </p>
                                        <span class="text-xs text-gray-500">
                                            <?= date("M d", strtotime($msg['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-400">No messages yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>

        <!-- UNREAD -->
        <div id="unreadTab" class="tab-content">
            <div class="messages-card">
                <div class="messages-card-content p-4">
                    <div class="space-y-2">
                        <?php if($resultUnread->num_rows>0):?>
                        <?php while($msg=$resultUnread->fetch_assoc()): ?>
                            <div class="messages-list-item unread"
                            data-id="<?= $msg['id'] ?>"
                            data-sender="<?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>"
                            data-subject="<?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>"
                            data-message="<?= htmlspecialchars($msg['message']) ?>"
                            data-date="<?= date("M d, Y h:i A", strtotime($msg['created_at'])) ?>">
                            <div class="flex items-start gap-3">
                                <div class="avatar">
                                    <?= strtoupper(substr($msg['sender_name'] ?? 'US',0,2)) ?>    
                                </div>
                            <div class="flex-1">
                                <span class="font-semibold text-white text-sm"> 
                                    <?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>
                                </span>
                                <p class="text-sm text-white"> <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?></p>
                                <p class="text-xs text-gray-500"><?= substr($msg['message'],0,50) ?>...</p>
                                <span class="text-xs text-gray-500"><?= date("M d", strtotime($msg['created_at'])) ?></span>
                            </div>
                            </div>
                            </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-400">No unread messages</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- READ -->
        <div id="readTab" class="tab-content" style="display:none;">
            <div class="messages-card">
                <div class="messages-card-content p-4">
                    <div class="space-y-2">
                        <?php if($resultRead->num_rows>0): ?>
                            <?php while($msg=$resultRead->fetch_assoc()):?>
                                <div class="messages-list-item" 
                                data-id="<?= $msg['id'] ?>"
                                data-sender="<?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>"
                                data-subject="<?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>"
                                data-message="<?= htmlspecialchars($msg['message']) ?>"
                                data-date="<?= date("M d, Y h:i A", strtotime($msg['created_at'])) ?>">
                                <div class="flex items-start gap-3">
                                    <div class="avatar">
                                        <?= strtoupper(substr($msg['sender_name'] ?? 'US',0,2)) ?>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-semibold text-white text-sm">
                                            <?= htmlspecialchars($msg['sender_name'] ?? 'User') ?>
                                        </span>
                                        <p class="text-sm text-white">
                                            <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?> 
                                        </p>
                                        <p class="text-xs text-gray-500"><?= substr($msg['message'],0,50) ?>...</p>
                                        <span class="text-xs text-gray-500"><?= date("M d", strtotime($msg['created_at'])) ?> 
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-400">No read messages</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- SENT -->
        <div id="sentTab" class="tab-content" style="display:none;">
            <div class="messages-card">
                <div class="messages-card-content p-4">
                    <div class="space-y-2">
                        <?php if($resultSent->num_rows>0): ?>
                            <?php while($msg=$resultSent->fetch_assoc()): ?>
                                <div class="messages-list-item"
                                data-id="<?= $msg['id'] ?>"
                                data-sender="<?= htmlspecialchars($msg['receiver_name'] ?? 'User') ?>"
                                data-subject="<?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>"
                                data-message="<?= htmlspecialchars($msg['message']) ?>"
                                data-date="<?= date("M d, Y h:i A", strtotime($msg['created_at'])) ?>">
                                <div class="flex items-start gap-3">
                                    <div class="avatar">
                                        <?= strtoupper(substr($msg['receiver_name'] ?? 'US',0,2)) ?>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-semibold text-white text-sm">
                                            <?= htmlspecialchars($msg['receiver_name'] ?? 'User') ?>
                                        </span>
                                        <p class="text-sm text-white">
                                            <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?> 
                                        </p>
                                        <p class="text-xs text-gray-500"> <?= substr($msg['message'],0,50) ?>...
                                        </p>
                                        <span class="text-xs text-gray-500">
                                            <?= date("M d", strtotime($msg['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                                </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-gray-400">No sent messages</p>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Detail -->
        <div class="lg-col-span-2" id="message-detail" style="display: none;">
            <div class="messages-card">
                <div class="messages-card-content p-0">
                    <div class="p-6 border-bottom">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start gap-4">
                                <div class="avatar" id="detail-avatar" style="width:3rem; height:3rem; font-size:1.25rem;">--</div>
                                <div>
                                    <h3 class="font-semibold text-white" id="detail-sender">Sender</h3>
                                    <p class="text-xs text-gray-500 mt-1" id="detail-date">Date</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-outline btn-sm" onclick="dashboardMarkAsRead()">Mark Read</button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="dashboardDeleteMessage()">Delete</button>
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold text-white" id="detail-subject">Subject</h2>
                    </div>

                    <div class="p-6" style="max-height:400px; overflow-y:auto;">
                        <p class="text-gray-300" id="detail-message" style="white-space:pre-wrap; line-height:1.6;">Message content</p>
                    </div>

                    <form class="messages-reply-form" id="reply-form">
                        <input type="hidden" name="message_id" id="message_id_input">
                        <div class="p-6 border-top">
                            <h4 class="font-semibold text-white mb-3">Reply</h4>
                            <textarea name="reply_message" class="messages-form-textarea mb-3" placeholder="Type your reply message..." rows="4" required></textarea>
                            <div class="flex items-center justify-between">
                                <button type="submit" class="btn btn-primary">Send Reply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Modal -->
<div id="compose-modal" style="display:none; position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div class="messages-card" style="max-width:600px;width:90%;">
        <div class="messages-card-content p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Compose New Message</h2>
                <button type="button" onclick="dashboardCloseComposeModal()" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            <form id="messages-compose-form" method="POST" action="admin_send_messages.php">
                <div class="messages-form-group">
                    <label class="messages-form-label">Recipient Username *</label>
                    <input type="text" name="recipient" class="form-input" required>
                </div>
                <div class="messages-form-group">
                    <label class="messages-form-label">Subject *</label>
                    <input type="text" name="subject" class="form-input" required>
                </div>
                <div class="messages-form-group">
                    <label class="messages-form-label">Message *</label>
                    <textarea name="message" class="messages-form-textarea" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
const dashboardMessageItems = document.querySelectorAll('.messages-list-item');
const dashboardMessageDetail = document.getElementById('message-detail');
const dashboardDetailSender = document.getElementById('detail-sender');
const dashboardDetailSubject = document.getElementById('detail-subject');
const dashboardDetailMessage = document.getElementById('detail-message');
const dashboardDetailDate = document.getElementById('detail-date');
const dashboardDetailAvatar = document.getElementById('detail-avatar');
const dashboardMessageIdInput = document.getElementById('message_id_input');
let dashboardCurrentMessageId = null;

dashboardMessageItems.forEach(item => {
    item.addEventListener('click', function () {
        const isActive = this.classList.contains('active');
        if (isActive) {
            this.classList.remove('active');
            dashboardMessageDetail.style.display = 'none';
            return;
        }
        dashboardMessageItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');

        dashboardCurrentMessageId = this.dataset.id;
        dashboardMessageIdInput.value = this.dataset.id;
        dashboardDetailSender.textContent = this.dataset.sender;
        dashboardDetailSubject.textContent = this.dataset.subject;
        dashboardDetailMessage.textContent = this.dataset.message;
        dashboardDetailDate.textContent = this.dataset.date;
        dashboardDetailAvatar.textContent = this.dataset.sender.substring(0,2).toUpperCase();

        dashboardMessageDetail.style.display = 'block';
    });
});

const dashboardReplyForm = document.getElementById('reply-form');
dashboardReplyForm.addEventListener('submit', e => {
    e.preventDefault();
    if(!dashboardCurrentMessageId) return alert('Select a message first');
    const formData = new FormData(dashboardReplyForm);
    fetch('admin_reply_message.php', { method:'POST', body: formData })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){ alert('Reply sent!'); location.reload(); }
            else alert('Error: '+(data.error||'Failed'));
        });
});

function dashboardMarkAsRead() {
    if(!dashboardCurrentMessageId) return;
    fetch('admin_mark_read.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'message_id='+dashboardCurrentMessageId })
        .then(res=>res.json()).then(data=>{ if(data.success) location.reload(); });
}

function dashboardDeleteMessage() {
    if(!dashboardCurrentMessageId) return;
    if(confirm('Are you sure you want to delete this message?')) alert('Delete coming soon');
}

// Compose modal
function dashboardOpenComposeModal(){ document.getElementById('compose-modal').style.display='flex'; }
function dashboardCloseComposeModal(){ document.getElementById('compose-modal').style.display='none'; }
document.addEventListener('keydown', e => { if(e.key==='Escape') dashboardCloseComposeModal(); });
document.querySelectorAll('.tab-btn').forEach(btn=>{
btn.addEventListener('click',()=>{
const tab = btn.dataset.tab;

document.querySelectorAll('.tab-content').forEach(t=>t.style.display='none');
document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));

document.getElementById(tab).style.display='block';
btn.classList.add('active');
});
});


</script>