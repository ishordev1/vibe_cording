<?php
// Handle payment verification
if(isset($_GET['verify'])) {
    $payment_id = intval($_GET['verify']);
    $admin_id = $_SESSION['user_id'];
    $verified_at = date('Y-m-d H:i:s');
    
    // Get application ID from payment
    $payment_result = $conn->query("SELECT application_id FROM payments WHERE id=$payment_id");
    $payment = $payment_result->fetch_assoc();
    $app_id = $payment['application_id'];
    
    // Update payment status
    $conn->query("UPDATE payments SET status='verified', verified_by=$admin_id, verified_at='$verified_at' WHERE id=$payment_id");
    
    // Auto-approve the application
    $conn->query("UPDATE applications SET status='approved' WHERE id=$app_id");
    
    $message = "Payment verified successfully! Application auto-approved!";
    $messageType = "success";
}

if(isset($_GET['reject_payment'])) {
    $payment_id = intval($_GET['reject_payment']);
    
    // Get application ID from payment
    $payment_result = $conn->query("SELECT application_id FROM payments WHERE id=$payment_id");
    $payment = $payment_result->fetch_assoc();
    $app_id = $payment['application_id'];
    
    // Update payment status
    $conn->query("UPDATE payments SET status='rejected' WHERE id=$payment_id");
    
    // Auto-reject the application
    $conn->query("UPDATE applications SET status='rejected' WHERE id=$app_id");
    
    $message = "Payment rejected! Application auto-rejected!";
    $messageType = "error";
}

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

$where = "WHERE p.status='" . $conn->real_escape_string($status_filter) . "'";

// Get payments
$payments = $conn->query("
    SELECT p.*, u.name, u.email, i.title, a.id as app_id
    FROM payments p
    JOIN applications a ON p.application_id = a.id
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    $where
    ORDER BY p.created_at DESC
");
?>

<!-- Filter Tabs -->
<div class="mb-6 flex gap-2">
    <a href="?section=payments&status=pending" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'pending' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Pending
    </a>
    <a href="?section=payments&status=verified" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'verified' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Verified
    </a>
    <a href="?section=payments&status=rejected" class="px-4 py-2 rounded-lg transition <?php echo $status_filter === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
        Rejected
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-credit-card mr-2 text-blue-600"></i>Payment Records
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">Student</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Internship</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Amount</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Method</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($payments->num_rows > 0) {
                    while($payment = $payments->fetch_assoc()) {
                        $statusColor = $payment['status'] === 'verified' ? 'green' : ($payment['status'] === 'pending' ? 'orange' : 'red');
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3'>
                                <div class='font-semibold text-gray-900'>" . htmlspecialchars($payment['name']) . "</div>
                                <div class='text-sm text-gray-600'>" . htmlspecialchars($payment['email']) . "</div>
                            </td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($payment['title']) . "</td>
                            <td class='px-4 py-3 font-semibold text-gray-900'>Rs. " . number_format($payment['amount']) . "</td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($payment['payment_method'] ?? 'N/A') . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                    " . ucfirst($payment['status']) . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($payment['created_at'])) . "</td>
                            <td class='px-4 py-3 space-x-2'>
                                " . ($payment['status'] === 'pending' ? "
                                <a href='?section=payments&verify=" . $payment['id'] . "' class='inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm'>
                                    <i class='fas fa-check mr-1'></i>Verify
                                </a>
                                <a href='?section=payments&reject_payment=" . $payment['id'] . "' class='inline-flex items-center px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm'>
                                    <i class='fas fa-times mr-1'></i>Reject
                                </a>
                                " : "") . "
                                " . (!empty($payment['screenshot']) ? "
                                <a href='" . SITE_URL . "/public/uploads/" . htmlspecialchars($payment['screenshot']) . "' target='_blank' class='inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm'>
                                    <i class='fas fa-image mr-1'></i>View
                                </a>
                                " : "") . "
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='7' class='px-4 py-3 text-center text-gray-600'>No payments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
