<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm" style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; z-index: 1020;">
  <div class="container-fluid px-4">
    <button class="btn btn-outline-primary d-lg-none me-2" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <div class="d-flex align-items-center">
      <i class="fas fa-tachometer-alt text-primary me-2"></i>
      <span class="fw-semibold">Author Dashboard</span>
    </div>
    <div class="ms-auto d-flex align-items-center gap-3">
      <a href="index.php" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-home me-1"></i>Home
      </a>
      <a href="papers.php" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-search me-1"></i>Browse Papers
      </a>
      <div class="dropdown">
        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <i class="fas fa-user-circle me-1"></i><?= esc($u['name']) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<style>
@media (max-width: 992px) {
  nav.navbar { left: 0 !important; }
}
</style>