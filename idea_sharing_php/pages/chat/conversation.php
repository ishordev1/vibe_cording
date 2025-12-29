<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Chat - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require login
requireLogin();

$conn = getDBConnection();
$user_id = getCurrentUserId();
$user_type = getCurrentUserType();

// Get conversation ID
$conversation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$idea_id = isset($_GET['idea_id']) ? (int)$_GET['idea_id'] : 0;

// If idea_id is provided, get or create conversation
if ($idea_id > 0 && $conversation_id === 0) {
    if ($user_type === USER_TYPE_INVESTOR) {
        $stmt = $conn->prepare("SELECT c.id FROM conversations c WHERE c.idea_id = ? AND c.investor_id = ?");
        $stmt->bind_param("ii", $idea_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $conversation_id = $result->fetch_assoc()['id'];
        }
    }
}

// Get conversation details
$stmt = $conn->prepare("
    SELECT c.*, i.title as idea_title, i.user_id as creator_id,
           creator.full_name as creator_name, creator.username as creator_username,
           investor.full_name as investor_name, investor.username as investor_username
    FROM conversations c
    JOIN ideas i ON c.idea_id = i.id
    JOIN users creator ON c.creator_id = creator.id
    JOIN users investor ON c.investor_id = investor.id
    WHERE c.id = ? AND (c.creator_id = ? OR c.investor_id = ?)
");
$stmt->bind_param("iii", $conversation_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows === 0) {

    // Check if conversation exists but user doesn't have access
    $check_stmt = $conn->prepare("SELECT creator_id, investor_id FROM conversations WHERE id = ?");
    $check_stmt->bind_param("i", $conversation_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $conv_data = $check_result->fetch_assoc();
        setFlashMessage('error', 'You do not have access to this conversation. (Your ID: ' . $user_id . ', Conversation belongs to: ' . $conv_data['creator_id'] . ' and ' . $conv_data['investor_id'] . ')');
    } else {
        setFlashMessage('error', 'Conversation not found.');
    }
    redirect('/pages/chat/conversations.php');
}

$conversation = $result->fetch_assoc();

// Determine other user
$other_user_name = $user_type === USER_TYPE_CREATOR 
    ? $conversation['investor_name'] 
    : $conversation['creator_name'];
$other_user_username = $user_type === USER_TYPE_CREATOR 
    ? $conversation['investor_username'] 
    : $conversation['creator_username'];

// Mark messages as read
$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ?");
$stmt->bind_param("ii", $conversation_id, $user_id);
$stmt->execute();

// Get messages
$stmt = $conn->prepare("
    SELECT m.*, u.full_name as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.created_at ASC
");
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<div class="container-fluid mt-4 mb-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Chat Header -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($other_user_name); ?>
                            </h5>
                            <small>
                                <i class="bi bi-lightbulb"></i> 
                                <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $conversation['idea_id']; ?>" 
                                   class="text-white text-decoration-none">
                                    <?php echo htmlspecialchars($conversation['idea_title']); ?>
                                </a>
                            </small>
                        </div>
                        <div>
                            <a href="<?php echo APP_URL; ?>/pages/chat/conversations.php" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#scheduleMeetingModal">
                                <i class="bi bi-calendar-event"></i> Schedule Meeting
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card mb-3">
                <div class="card-body" id="chatMessages" style="height: 500px; overflow-y: auto;">
                    <?php if ($messages->num_rows > 0): ?>
                        <?php while ($msg = $messages->fetch_assoc()): ?>
                            <?php $is_mine = $msg['sender_id'] == $user_id; ?>
                            <div class="mb-3 <?php echo $is_mine ? 'text-end' : ''; ?>">
                                <div class="d-inline-block <?php echo $is_mine ? 'bg-primary text-white' : 'bg-light'; ?> rounded p-3" 
                                     style="max-width: 70%;">
                                    <?php if (!$is_mine): ?>
                                        <strong class="d-block mb-1"><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                                    <?php endif; ?>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                    <small class="<?php echo $is_mine ? 'text-white-50' : 'text-muted'; ?>">
                                        <?php echo timeAgo($msg['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                            <p class="mt-2">No messages yet. Start the conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Message Input -->
            <div class="card">
                <div class="card-body">
                    <form id="sendMessageForm">
                        <div class="input-group">
                            <textarea class="form-control" id="messageInput" placeholder="Type your message..." 
                                      rows="2" required></textarea>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-send"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Meetings Section -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Scheduled Meetings</h5>
                </div>
                <div class="card-body" id="meetingsList">
                    <p class="text-muted">Loading meetings...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Meeting Modal -->
<div class="modal fade" id="scheduleMeetingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleMeetingModalLabel">Schedule a Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleMeetingForm">
                <input type="hidden" id="meeting_id" name="meeting_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="meeting_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="meeting_date" name="meeting_date" required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="meeting_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location/Link</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               placeholder="e.g., Zoom link, office address">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Agenda or additional information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
const conversationId = <?php echo $conversation_id; ?>;
const userId = <?php echo $user_id; ?>;
let lastMessageId = 0;

// Handle Enter key in textarea
$('#messageInput').on('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        $('#sendMessageForm').submit();
    }
});

// Send message
$('#sendMessageForm').on('submit', function(e) {
    e.preventDefault();
    const message = $('#messageInput').val().trim();
    
    if (!message) return;

    $.post('<?php echo APP_URL; ?>/actions/send-message.php', {
        conversation_id: conversationId,
        message: message
    }, function(response) {
        if (response.success) {
            $('#messageInput').val('');
            loadNewMessages();
        } else {
            alert(response.message || 'Failed to send message');
        }
    }, 'json');
});

// Load new messages (polling)
function loadNewMessages() {
    $.get('<?php echo APP_URL; ?>/actions/get-messages.php', {
        conversation_id: conversationId,
        after_id: lastMessageId
    }, function(response) {
        if (response.success && response.messages.length > 0) {
            response.messages.forEach(function(msg) {
                appendMessage(msg);
                lastMessageId = Math.max(lastMessageId, msg.id);
            });
            scrollToBottom();
        }
    }, 'json');
}

// Append message to chat
function appendMessage(msg) {
    const isMine = msg.sender_id == userId;
    const alignment = isMine ? 'text-end' : '';
    const bgClass = isMine ? 'bg-primary text-white' : 'bg-light';
    const timeClass = isMine ? 'text-white-50' : 'text-muted';
    
    let html = `
        <div class="mb-3 ${alignment}">
            <div class="d-inline-block ${bgClass} rounded p-3" style="max-width: 70%;">
                ${!isMine ? '<strong class="d-block mb-1">' + msg.sender_name + '</strong>' : ''}
                <p class="mb-1">${msg.message.replace(/\n/g, '<br>')}</p>
                <small class="${timeClass}">${msg.time_ago}</small>
            </div>
        </div>
    `;
    
    $('#chatMessages').append(html);
}

// Scroll to bottom
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Load meetings
function loadMeetings() {
    $.get('<?php echo APP_URL; ?>/actions/get-meetings.php', {
        conversation_id: conversationId
    }, function(response) {
        if (response.success) {
            displayMeetings(response.meetings);
        }
    }, 'json');
}

// Display meetings
function displayMeetings(meetings) {
    if (meetings.length === 0) {
        $('#meetingsList').html('<p class="text-muted">No meetings scheduled</p>');
        return;
    }
    
    let html = '<div class="list-group">';
    meetings.forEach(function(meeting) {
        const statusBadge = {
            'pending': 'warning',
            'confirmed': 'success',
            'cancelled': 'danger',
            'completed': 'secondary'
        }[meeting.status] || 'secondary';
        
        const canEdit = meeting.scheduled_by == userId || meeting.status === 'pending';
        
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <i class="bi bi-calendar-event"></i> ${meeting.formatted_date}
                        </h6>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-clock"></i> ${meeting.formatted_time}
                        </p>
                        ${meeting.location ? '<p class="mb-1 small"><i class="bi bi-geo-alt"></i> ' + meeting.location + '</p>' : ''}
                        ${meeting.notes ? '<p class="mb-1 small">' + meeting.notes + '</p>' : ''}
                        <small class="text-muted">Scheduled by ${meeting.scheduled_by_name}</small>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <span class="badge bg-${statusBadge}">${meeting.status}</span>
                        ${canEdit ? `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm" onclick="editMeeting(${meeting.id}, '${meeting.meeting_date}', '${meeting.meeting_time}', '${meeting.location || ''}', '${meeting.notes || ''}', '${meeting.status}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="updateMeetingStatus(${meeting.id}, 'cancelled')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        ` : ''}
                        ${meeting.status === 'pending' && meeting.scheduled_by != userId ? `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success btn-sm" onclick="updateMeetingStatus(${meeting.id}, 'confirmed')">
                                    <i class="bi bi-check-circle"></i> Confirm
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="updateMeetingStatus(${meeting.id}, 'cancelled')">
                                    <i class="bi bi-x-circle"></i> Decline
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    $('#meetingsList').html(html);
}

// Schedule meeting
$('#scheduleMeetingForm').on('submit', function(e) {
    e.preventDefault();
    
    const meetingId = $('#meeting_id').val();
    const url = meetingId ? '<?php echo APP_URL; ?>/actions/update-meeting.php' : '<?php echo APP_URL; ?>/actions/schedule-meeting.php';
    
    $.post(url, {
        meeting_id: meetingId,
        conversation_id: conversationId,
        meeting_date: $('#meeting_date').val(),
        meeting_time: $('#meeting_time').val(),
        location: $('#location').val(),
        notes: $('#notes').val()
    }, function(response) {
        if (response.success) {
            $('#scheduleMeetingModal').modal('hide');
            $('#scheduleMeetingForm')[0].reset();
            $('#meeting_id').val('');
            $('#scheduleMeetingModalLabel').text('Schedule a Meeting');
            loadMeetings();
            alert(meetingId ? 'Meeting updated successfully!' : 'Meeting scheduled successfully!');
        } else {
            alert(response.message || 'Failed to save meeting');
        }
    }, 'json');
});

// Edit meeting function
function editMeeting(id, date, time, location, notes, status) {
    $('#meeting_id').val(id);
    $('#meeting_date').val(date);
    $('#meeting_time').val(time);
    $('#location').val(location);
    $('#notes').val(notes);
    $('#scheduleMeetingModalLabel').text('Update Meeting');
    $('#scheduleMeetingModal').modal('show');
}

// Update meeting status
function updateMeetingStatus(meetingId, status) {
    if (!confirm('Are you sure you want to ' + status + ' this meeting?')) {
        return;
    }
    
    $.post('<?php echo APP_URL; ?>/actions/update-meeting-status.php', {
        meeting_id: meetingId,
        status: status
    }, function(response) {
        if (response.success) {
            loadMeetings();
            alert('Meeting ' + status + ' successfully!');
        } else {
            alert(response.message || 'Failed to update meeting');
        }
    }, 'json');
}

// Initialize
$(document).ready(function() {
    
    scrollToBottom();
    loadMeetings();
    
    // Poll for new messages every 3 seconds
    setInterval(loadNewMessages, 3000);
});

// Handle Enter key in message input
$('#messageInput').on('keypress', function(e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        $('#sendMessageForm').submit();
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
