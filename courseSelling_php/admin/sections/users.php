<?php
// Get all users (students)
$users = $conn->query("SELECT * FROM users WHERE user_type='student' ORDER BY created_at DESC");
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-users mr-2 text-blue-600"></i>All Students
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Joined</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($users->num_rows > 0) {
                    while($user = $users->fetch_assoc()) {
                        $statusColor = $user['status'] === 'active' ? 'green' : 'red';
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3 font-semibold text-gray-900'>" . htmlspecialchars($user['name']) . "</td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($user['email']) . "</td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($user['phone'] ?? 'N/A') . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                    " . ucfirst($user['status']) . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($user['created_at'])) . "</td>
                            <td class='px-4 py-3'>
                                <button onclick=\"openUserModal(" . $user['id'] . ")\" class='px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm'>
                                    <i class='fas fa-eye mr-1'></i>View
                                </button>
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='6' class='px-4 py-3 text-center text-gray-600'>No students found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function openUserModal(userId) {
    alert('User ID: ' + userId + '\n\nFeature to view full user details with applications coming soon...');
}
</script>
