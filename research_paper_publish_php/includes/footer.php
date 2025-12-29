<footer class="site-footer bg-dark text-white mt-5">
  <div class="container py-5">
    <div class="row">
      <!-- About Section -->
      <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
        <h5 class="fw-bold mb-3">
          <i class="fas fa-graduation-cap me-2"></i>Research Journal
        </h5>
        <p class="text-light">
          A premier platform for publishing cutting-edge research papers across various disciplines. 
          Join our community of scholars and contribute to the advancement of knowledge.
        </p>
        <div class="social-links mt-3">
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2" title="Facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2" title="Twitter">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2" title="LinkedIn">
            <i class="fab fa-linkedin-in"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle" title="Email">
            <i class="fas fa-envelope"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
        <h6 class="fw-bold mb-3">Quick Links</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="index.php" class="text-light"><i class="fas fa-angle-right me-1"></i>Home</a></li>
          <li><a href="papers.php" class="text-light"><i class="fas fa-angle-right me-1"></i>Browse Papers</a></li>
          
      </div>

      <!-- For Authors -->
      <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
        <h6 class="fw-bold mb-3">For Authors</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="register.php" class="text-light"><i class="fas fa-angle-right me-1"></i>Submit Paper</a></li>
          <li><a href="#" class="text-light"><i class="fas fa-angle-right me-1"></i>Author Guidelines</a></li>
          <li><a href="#" class="text-light"><i class="fas fa-angle-right me-1"></i>Publication Ethics</a></li>
          <li><a href="#" class="text-light"><i class="fas fa-angle-right me-1"></i>Peer Review Process</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-bold mb-3">Contact Us</h6>
        <ul class="list-unstyled footer-contact">
          <li class="mb-2">
            <i class="fas fa-map-marker-alt me-2"></i>
            123 Research Avenue<br>
            <span class="ms-4">Science City, ST 12345</span>
          </li>
          <li class="mb-2">
            <i class="fas fa-phone me-2"></i>
            <a href="tel:+1234567890" class="text-light">+1 (234) 567-890</a>
          </li>
          <li class="mb-2">
            <i class="fas fa-envelope me-2"></i>
            <a href="mailto:info@researchjournal.com" class="text-light">info@researchjournal.com</a>
          </li>
          <li>
            <i class="fas fa-clock me-2"></i>
            Mon - Fri: 9:00 AM - 6:00 PM
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Copyright Bar -->
  <div class="footer-bottom bg-black py-3">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0 text-light small">
            &copy; <?= date('Y') ?> Research Journal. All rights reserved.
          </p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="#" class="text-light small">Privacy Policy</a>
            </li>
            <li class="list-inline-item">|</li>
            <li class="list-inline-item">
              <a href="#" class="text-light small">Terms of Service</a>
            </li>
            <li class="list-inline-item">|</li>
            <li class="list-inline-item">
              <a href="#" class="text-light small">Cookie Policy</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
.site-footer {
  border-top: 3px solid var(--primary);
}

.footer-links a {
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
  padding: 5px 0;
}

.footer-links a:hover {
  color: var(--primary) !important;
  transform: translateX(5px);
}

.footer-contact a {
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-contact a:hover {
  color: var(--primary) !important;
}

.social-links .btn {
  width: 36px;
  height: 36px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.social-links .btn:hover {
  background-color: var(--primary);
  border-color: var(--primary);
  transform: translateY(-3px);
}

.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.1);
}
</style>
