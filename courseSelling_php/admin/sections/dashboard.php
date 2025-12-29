<!-- Dashboard Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalUsers; ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Internships Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Internships</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalInternships; ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-briefcase text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Applications Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Applications</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalApplications; ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fas fa-file-alt text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Pending Payments Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Pending Payments</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $pendingPayments; ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-credit-card text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Applications Status -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-pie mr-2 text-purple-600"></i>Applications Status
        </h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Approved</span>
                    <span class="font-bold text-green-600"><?php echo $approvedApplications; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo ($totalApplications > 0) ? ($approvedApplications/$totalApplications)*100 : 0; ?>%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Pending</span>
                    <span class="font-bold text-orange-600"><?php echo $pendingApplications; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: <?php echo ($totalApplications > 0) ? ($pendingApplications/$totalApplications)*100 : 0; ?>%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Rejected</span>
                    <span class="font-bold text-red-600"><?php echo $totalApplications - $approvedApplications - $pendingApplications; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo ($totalApplications > 0) ? (($totalApplications - $approvedApplications - $pendingApplications)/$totalApplications)*100 : 0; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-lightning-bolt mr-2 text-yellow-600"></i>Quick Actions
        </h3>
        <div class="space-y-3">
            <a href="?section=users" class="block px-4 py-3 bg-blue-50 border-l-4 border-blue-600 hover:bg-blue-100 rounded transition">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>Manage Users
            </a>
            <a href="?section=internships" class="block px-4 py-3 bg-green-50 border-l-4 border-green-600 hover:bg-green-100 rounded transition">
                <i class="fas fa-plus text-green-600 mr-2"></i>Manage Internships
            </a>
            <a href="?section=applications" class="block px-4 py-3 bg-purple-50 border-l-4 border-purple-600 hover:bg-purple-100 rounded transition">
                <i class="fas fa-check text-purple-600 mr-2"></i>Review Applications
            </a>
            <a href="?section=payments" class="block px-4 py-3 bg-red-50 border-l-4 border-red-600 hover:bg-red-100 rounded transition">
                <i class="fas fa-money-bill text-red-600 mr-2"></i>Verify Payments
            </a>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-history mr-2 text-gray-600"></i>Recent Applications
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">User</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Internship</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT a.id, u.name as user_name, i.title as internship_title, a.status, a.application_date 
                         FROM applications a 
                         JOIN users u ON a.user_id = u.id 
                         JOIN internships i ON a.internship_id = i.id 
                         ORDER BY a.application_date DESC 
                         LIMIT 5";
                $result = $conn->query($query);
                
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusColor = $row['status'] === 'approved' ? 'green' : ($row['status'] === 'pending' ? 'orange' : 'red');
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3'>
                                <a href='?section=users&view=" . $row['id'] . "' class='text-blue-600 hover:underline'>" . htmlspecialchars($row['user_name']) . "</a>
                            </td>
                            <td class='px-4 py-3'>" . htmlspecialchars($row['internship_title']) . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                    " . ucfirst($row['status']) . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($row['application_date'])) . "</td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='4' class='px-4 py-3 text-center text-gray-600'>No applications yet</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
