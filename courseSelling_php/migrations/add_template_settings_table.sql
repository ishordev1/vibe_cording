-- Create template_settings table for storing customization settings
CREATE TABLE IF NOT EXISTS `template_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `template_type` VARCHAR(50) NOT NULL UNIQUE COMMENT 'certificates or offer-letters',
    `settings` LONGTEXT NOT NULL COMMENT 'JSON formatted settings',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_template_type` (`template_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
