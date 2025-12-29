<style>
.professional-navbar {
  background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
  padding: 1rem 0;
  box-shadow: 0 2px 15px rgba(0,0,0,0.2);
}
.professional-navbar .navbar-brand {
  color: #fff !important;
  font-size: 1.6rem;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
}
.professional-navbar .navbar-brand i {
  font-size: 1.8rem;
  vertical-align: middle;
}
.professional-navbar .nav-link {
  color: rgba(255,255,255,0.95) !important;
  font-weight: 500;
  font-size: 0.95rem;
  padding: 0.6rem 1.1rem !important;
  transition: all 0.3s ease;
  position: relative;
  margin: 0 2px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
}
.professional-navbar .nav-link i {
  font-size: 1rem;
  margin-right: 6px;
}
.professional-navbar .nav-link:hover {
  color: #fff !important;
  background: rgba(255,255,255,0.15);
  transform: translateY(-1px);
}
.professional-navbar .dropdown-menu {
  background: #fff;
  border: none;
  box-shadow: 0 8px 25px rgba(0,0,0,0.18);
  border-radius: 8px;
  margin-top: 0.5rem;
  animation: dropdownFade 0.3s ease;
  min-width: 250px;
  padding: 0.5rem 0;
}
@keyframes dropdownFade {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.professional-navbar .dropdown-item {
  padding: 0.8rem 1.5rem;
  color: #333;
  font-weight: 500;
  font-size: 0.93rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}
.professional-navbar .dropdown-item:hover {
  background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
  color: #fff;
  padding-left: 2rem;
}
.professional-navbar .dropdown-item i {
  width: 22px;
  margin-right: 10px;
  color: #3b5998;
  font-size: 1rem;
}
.professional-navbar .dropdown-item:hover i {
  color: #fff;
}
.professional-navbar .btn-login {
  background: rgba(255,255,255,0.2);
  color: #fff !important;
  border: 2px solid rgba(255,255,255,0.6);
  padding: 0.55rem 1.8rem;
  border-radius: 30px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}
.professional-navbar .btn-login:hover {
  background: #fff;
  color: #3b5998 !important;
  border-color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 5px 20px rgba(255,255,255,0.4);
}
.professional-navbar .btn-register {
  background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
  color: #2d4373 !important;
  border: none;
  padding: 0.55rem 1.8rem;
  border-radius: 30px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}
.professional-navbar .btn-register:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 20px rgba(255,215,0,0.5);
  background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
}
.professional-navbar .dropdown-toggle::after {
  margin-left: 0.5em;
  vertical-align: 0.2em;
  border-top: 0.35em solid;
  border-right: 0.35em solid transparent;
  border-left: 0.35em solid transparent;
}
.professional-navbar .navbar-toggler {
  border-color: rgba(255,255,255,0.5);
  padding: 0.5rem 0.75rem;
}
.professional-navbar .navbar-toggler:focus {
  box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.3);
}
.professional-navbar .navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
@media (max-width: 991px) {
  .professional-navbar .nav-link {
    padding: 0.7rem 1rem !important;
    margin: 3px 0;
  }
  .professional-navbar .btn-login,
  .professional-navbar .btn-register {
    width: 100%;
    margin: 5px 0;
    justify-content: center;
  }
  .professional-navbar .dropdown-menu {
    background: rgba(255,255,255,0.98);
  }
}
</style>

<nav class="navbar navbar-expand-lg professional-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fas fa-graduation-cap me-2"></i>IJSRET
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>Home
          </a>
        </li>
        
        <!-- About IJSRET Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-info-circle"></i>About IJSRET
          </a>
          <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
            <li><a class="dropdown-item" href="about.php"><i class="fas fa-university"></i>About Journal</a></li>
            <li><a class="dropdown-item" href="aim-scope.php"><i class="fas fa-bullseye"></i>Aim & Scope</a></li>
            <li><a class="dropdown-item" href="indexing.php"><i class="fas fa-database"></i>Journal Indexing</a></li>
          </ul>
        </li>
        
        <!-- For Authors Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="authorsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-pen-fancy"></i>For Authors
          </a>
          <ul class="dropdown-menu" aria-labelledby="authorsDropdown">
            <li><a class="dropdown-item" href="submit_paper.php"><i class="fas fa-upload"></i>Paper Submission</a></li>
            <li><a class="dropdown-item" href="author-guidelines.php"><i class="fas fa-book"></i>Author's Guidelines</a></li>
            <li><a class="dropdown-item" href="ethics-rights.php"><i class="fas fa-gavel"></i>Ethics & Rights</a></li>
            <li><a class="dropdown-item" href="processing-fee.php"><i class="fas fa-money-bill-wave"></i>Article Processing Fee</a></li>
            <li><a class="dropdown-item" href="paper-review.php"><i class="fas fa-search"></i>About Paper Review</a></li>
          </ul>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="papers.php">
            <i class="fas fa-archive"></i>Archive
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="contact.php">
            <i class="fas fa-envelope"></i>Contact Us
          </a>
        </li>
        
        <?php
        $user = current_user();
        if ($user):
          if ($user['role'] === 'admin'):
        ?>
        <li class="nav-item ms-lg-2">
          <a class="btn btn-login" href="admin/dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
          </a>
        </li>
        <li class="nav-item ms-2">
          <a class="btn btn-register" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a>
        </li>
        <?php else: ?>
        <li class="nav-item ms-lg-2">
          <a class="btn btn-login" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
          </a>
        </li>
        <li class="nav-item ms-2">
          <a class="btn btn-register" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a>
        </li>
        <?php endif; else: ?>
        <li class="nav-item ms-lg-2">
          <a class="btn btn-login" href="login.php">
            <i class="fas fa-sign-in-alt me-2"></i>Login
          </a>
        </li>
        <li class="nav-item ms-2">
          <a class="btn btn-register" href="register.php">
            <i class="fas fa-user-plus me-2"></i>Register
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
