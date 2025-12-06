<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Contact Messages';

// Read contact messages from log file
$logFile = __DIR__ . '/../storage/logs/contact_messages.log';
$messages = [];

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $messageBlocks = explode('=== CONTACT MESSAGE ===', $logContent);
    
    foreach ($messageBlocks as $block) {
        if (trim($block)) {
            $lines = explode("\n", trim($block));
            $message = [];
            $messageContent = '';
            $inMessage = false;
            
            foreach ($lines as $line) {
                if (strpos($line, 'Date: ') === 0) {
                    $message['date'] = substr($line, 6);
                } elseif (strpos($line, 'Name: ') === 0) {
                    $message['name'] = substr($line, 6);
                } elseif (strpos($line, 'Email: ') === 0) {
                    $message['email'] = substr($line, 7);
                } elseif (strpos($line, 'Subject: ') === 0) {
                    $message['subject'] = substr($line, 9);
                } elseif (strpos($line, 'Email Sent: ') === 0) {
                    $message['email_sent'] = substr($line, 12);
                } elseif (strpos($line, 'IP: ') === 0) {
                    $message['ip'] = substr($line, 4);
                } elseif (strpos($line, 'Message:') === 0) {
                    $inMessage = true;
                } elseif ($inMessage && strpos($line, '========================') !== 0) {
                    $messageContent .= $line . "\n";
                }
            }
            
            if (!empty($message['name'])) {
                $message['message'] = trim($messageContent);
                $messages[] = $message;
            }
        }
    }
    
    // Sort messages by date (newest first)
    usort($messages, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Contact Messages</h2>
            <p class="text-muted mb-0">View and manage customer contact form submissions</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="../contact" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt me-2"></i>View Contact Form
                </a>
                <button onclick="location.reload()" class="btn btn-outline-secondary">
                    <i class="fas fa-sync me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= e(count($messages)) ?></h4>
                        <p class="mb-0">Total Messages</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= e(count(array_filter($messages, function($m) { return $m['email_sent'] === 'Yes'; }))) ?></h4>
                        <p class="mb-0">Emails Sent</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= e(count(array_filter($messages, function($m) { return $m['email_sent'] === 'No'; }))) ?></h4>
                        <p class="mb-0">Email Failed</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= e(count(array_filter($messages, function($m) { return strtotime($m['date']) > strtotime('-24 hours'); }))) ?></h4>
                        <p class="mb-0">Last 24 Hours</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Messages List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Contact Messages
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4>No Messages Yet</h4>
                <p class="text-muted">Contact form submissions will appear here.</p>
                <a href="../contact" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>Test Contact Form
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Email Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $index => $message): ?>
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <?= e(date('M j, Y', strtotime($message['date']))) ?><br>
                                        <?= e(date('g:i A', strtotime($message['date']))) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($message['name']) ?></strong>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($message['email']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($message['subject']) ?></td>
                                <td>
                                    <?php if ($message['email_sent'] === 'Yes'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Sent
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation me-1"></i>Failed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewMessage(<?= e($index) ?>)">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-reply me-1"></i>Reply
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Message content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="replyButton">
                    <i class="fas fa-reply me-2"></i>Reply
                </button>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<script>
const messages = <?= json_encode($messages) ?>;

function viewMessage(index) {
    const message = messages[index];
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Contact Information:</h6>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>${message.name}</td></tr>
                    <tr><td><strong>Email:</strong></td><td><a href="mailto:${message.email}">${message.email}</a></td></tr>
                    <tr><td><strong>Subject:</strong></td><td>${message.subject}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>${message.date}</td></tr>
                    <tr><td><strong>Email Sent:</strong></td><td>
                        ${message.email_sent === 'Yes' ? 
                            '<span class="badge bg-success">Yes</span>' : 
                            '<span class="badge bg-warning">No</span>'}
                    </td></tr>
                    <tr><td><strong>IP Address:</strong></td><td>${message.ip || 'Unknown'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Message:</h6>
                <div class="border p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    ${message.message.replace(/\n/g, '<br>')}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('messageContent').innerHTML = content;
    document.getElementById('replyButton').onclick = function() {
        window.open(`mailto:${message.email}?subject=Re: ${message.subject}`, '_blank');
    };
    
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}
</script>

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
