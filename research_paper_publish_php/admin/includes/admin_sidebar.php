<aside class="sidebar">
  <div class="sidebar-header">
    <h4><i class="fas fa-shield-alt me-2"></i>Research Journal</h4>
    <small class="text-white-50">Admin Panel</small>
  </div>
  <nav class="sidebar-menu">
    <a href="dashboard.php" class="sidebar-menu-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-tachometer-alt"></i>Dashboard
    </a>
    <a href="papers.php" class="sidebar-menu-item <?= basename($_SERVER['PHP_SELF']) == 'papers.php' ? 'active' : '' ?>">
      <i class="fas fa-file-alt"></i>All Papers
    </a>
    <a href="certificate_settings.php" class="sidebar-menu-item <?= basename($_SERVER['PHP_SELF']) == 'certificate_settings.php' ? 'active' : '' ?>">
      <i class="fas fa-certificate"></i>Certificate Settings
    </a>
    <a href="../index.php" class="sidebar-menu-item">
      <i class="fas fa-home"></i>Home
    </a>
    <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
    <a href="../logout.php" class="sidebar-menu-item">
      <i class="fas fa-sign-out-alt"></i>Logout
    </a>
  </nav>
  <div class="p-3 mt-auto" style="border-top: 1px solid rgba(255,255,255,0.1);">
    <div class="d-flex align-items-center">
      <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
        <i class="fas fa-user-shield text-white"></i>
      </div>
      <div class="flex-grow-1">
        <div class="text-white small fw-semibold"><?= esc($u['name']) ?></div>
        <div class="text-white-50" style="font-size: 0.75rem;">Administrator</div>
      </div>
    </div>
  </div>
</aside>