<?php
/**
 * Admin Sidebar Navigation
 * Include this file in all admin pages for consistent navigation
 */
?>

<nav class="col-md-2 bg-dark sidebar p-0" style="min-height: 100vh; position: sticky; top: 0;">
    <div class="sidebar-sticky">
        <h5 class="text-white p-3 border-bottom">🎓 InternHub</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/dashboard.php', '/')); ?>admin/dashboard.php">📊 Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'internships') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/internships/manage.php', '/')); ?>admin/internships/manage.php">💼 Manage Internships</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'applications') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/applications/view.php', '/')); ?>admin/applications/view.php">📝 Applications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'tasks') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/tasks/manage.php', '/')); ?>admin/tasks/manage.php">📋 Tasks & Levels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'review.php') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/applications/review.php', '/')); ?>admin/applications/review.php">👀 Review Submissions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'attendance') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/attendance/manage.php', '/')); ?>admin/attendance/manage.php">✅ Mark Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo (strpos($_SERVER['PHP_SELF'], 'certificates') !== false) ? 'active' : ''; ?>" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/admin/certificates/generate.php', '/')); ?>admin/certificates/generate.php">🏆 Generate Certificates</a>
            </li>
            <li class="nav-item border-top mt-3 pt-3">
                <a class="nav-link text-white" href="<?php echo str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/auth/logout.php', '/')); ?>auth/logout.php">🚪 Logout</a>
            </li>
        </ul>
    </div>
</nav>

<style>
    .sidebar {
        background-color: #667eea !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .sidebar-sticky {
        position: sticky;
        top: 0;
    }
    
    .sidebar .nav-link {
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.9) !important;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-left-color: #fff;
        color: #fff !important;
    }
    
    .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        border-left-color: #fff;
        color: #fff !important;
    }
    
    .sidebar h5 {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
</style>
