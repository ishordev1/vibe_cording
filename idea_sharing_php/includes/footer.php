    <!-- Footer -->
    <footer class="bg-dark text-white ">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4 text-white">
                    <h5><i class="bi bi-lightbulb-fill"></i> <?php echo APP_NAME; ?></h5>
                    <p class="text-white-50">Connecting innovative ideas with visionary investors.</p>
                </div>
                <div class="col-md-4 text-white">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo APP_URL; ?>/index.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="<?php echo APP_URL; ?>/pages/ideas.php" class="text-white-50 text-decoration-none">Browse Ideas</a></li>
                        
                    </ul>
                </div>
                <div class="col-md-4 text-white">
                    <h5>Contact</h5>
                    <p class="text-white-50">
                        <i class="bi bi-envelope"></i> info@ideaconnect.com<br>
                        <i class="bi bi-phone"></i> +1 (555) 123-4567
                    </p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-white">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for Ajax) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php echo $extraJS; ?>
    <?php endif; ?>
</body>
</html>
