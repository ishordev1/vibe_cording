<?php
// Handle approval/rejection
if(isset($_GET['approve'])) {
    $app_id = intval($_GET['approve']);
    $conn->query("UPDATE applications SET status='approved' WHERE id=$app_id");
    $message = "Application approved successfully!";
    $messageType = "success";
}

if(isset($_GET['reject'])) {
    $app_id = intval($_GET['reject']);
    $conn->query("UPDATE applications SET status='rejected' WHERE id=$app_id");
    $message = "Application rejected!";
    $messageType = "error";
}

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = "";
if($status_filter !== 'all') {
    $where = "WHERE a.status='" . $conn->real_escape_string($status_filter) . "'";
}

// Get applications
$applications = $conn->query("
    SELECT a.id, u.name, u.email, i.title, a.status, a.application_date
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    $where
    ORDER BY a.application_date DESC
");
?>

<!-- Filter Tabs -->
<div class="mb-6 flex gap-2">
    <a href="?section=applications&status=all" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        All
    </a>
    <a href="?section=applications&status=pending" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'pending' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Pending
    </a>
    <a href="?section=applications&status=approved" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Approved
    </a>
    <a href="?section=applications&status=rejected" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Rejected
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-file-alt mr-2 text-orange-600"></i>Applications
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">Student</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Internship</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Applied Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($applications->num_rows > 0) {
                    while($app = $applications->fetch_assoc()) {
                        $statusColor = $app['status'] === 'approved' ? 'green' : ($app['status'] === 'pending' ? 'orange' : 'red');
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3'>
                                <div class='font-semibold text-gray-900'>" . htmlspecialchars($app['name']) . "</div>
                                <div class='text-sm text-gray-600'>" . htmlspecialchars($app['email']) . "</div>
                            </td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($app['title']) . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                    " . ucfirst($app['status']) . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($app['application_date'])) . "</td>
                            <td class='px-4 py-3 space-x-2'>
                                " . ($app['status'] === 'pending' ? "
                                <a href='?section=applications&approve=" . $app['id'] . "' class='inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm'>
                                    <i class='fas fa-check mr-1'></i>Approve
                                </a>
                                <a href='?section=applications&reject=" . $app['id'] . "' class='inline-flex items-center px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm'>
                                    <i class='fas fa-times mr-1'></i>Reject
                                </a>
                                " : "<span class='text-gray-500 text-sm'>Reviewed</span>") . "
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='5' class='px-4 py-3 text-center text-gray-600'>No applications found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
