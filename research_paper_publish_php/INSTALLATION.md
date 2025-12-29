# Installation & Setup Guide

## Prerequisites
- **XAMPP** installed with Apache and MySQL running
- **PHP 7.4+** with GD extension enabled
- **MySQL/MariaDB** database
- Libraries: FPDF and PHP QR Code

## Step-by-Step Installation

### 1. Enable GD Extension (if not already enabled)

Open `C:\xampp\php\php.ini` and find this line:
```ini
;extension=gd
```

Remove the semicolon to enable it:
```ini
extension=gd
```

Restart Apache in XAMPP Control Panel.

### 2. Create Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create a new database named `research_db`
3. Import the schema:
   - Go to the SQL tab
   - Copy and paste contents from `sql/schema.sql`
   - Click "Go" to execute

### 3. Create Upload Directories

Run these commands in PowerShell from the project root:

```powershell
mkdir uploads\papers
mkdir uploads\copyrights
mkdir uploads\published
mkdir uploads\certificates
mkdir uploads\qrcodes
```

Or create them manually in File Explorer.

### 4. Install Libraries

The following libraries should already be in the `vendor/` folder:
- `vendor/fpdf186/fpdf.php` - FPDF library
- `vendor/phpqrcode/qrlib.php` - PHP QR Code library

If missing, download them:
- FPDF: http://www.fpdf.org/
- PHP QR Code: https://sourceforge.net/projects/phpqrcode/

### 5. Create Admin User

Run this SQL query in phpMyAdmin to create an admin account:

```sql
INSERT INTO users (name, email, password, role) 
VALUES ('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

**Login Credentials:**
- Email: `admin@example.com`
- Password: `password`

**⚠️ Important:** Change this password after first login!

To generate a new password hash, run this PHP code:
```php
echo password_hash('YourNewPassword', PASSWORD_DEFAULT);
```

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost/research_paper_submission/
```

**Landing Page:**
- Browse published papers (no login required)
- Login/Register links in navbar

**Author Features:**
- Register new account
- Login and submit papers
- View paper status
- Download certificates (when approved)

**Admin Features:**
- Login with admin credentials
- View all submitted papers
- Review and approve/reject papers
- Generate certificates
- Upload final published PDFs

## File Structure

```
research_paper_submission/
├── admin/                      # Admin panel
│   ├── includes/
│   │   ├── admin_sidebar.php
│   │   └── admin_navbar.php
│   ├── dashboard.php
│   ├── papers.php
│   ├── view_paper.php
│   ├── review.php
│   ├── generate_certificate.php
│   └── upload_final_pdf.php
├── assets/
│   ├── css/
│   │   └── style.css          # Custom CSS framework
│   └── js/
│       └── main.js            # JavaScript utilities
├── includes/
│   ├── config.php             # Database configuration
│   ├── db.php                 # PDO connection
│   ├── functions.php          # Helper functions
│   ├── author_sidebar.php     # Author sidebar component
│   └── author_navbar.php      # Author navbar component
├── sql/
│   └── schema.sql             # Database schema
├── uploads/                   # Upload directories (create these!)
│   ├── papers/
│   ├── copyrights/
│   ├── published/
│   ├── certificates/
│   └── qrcodes/
├── vendor/                    # Third-party libraries
│   ├── fpdf186/
│   └── phpqrcode/
├── index.php                  # Landing page (IEEE style)
├── login.php
├── register.php
├── dashboard.php              # Author dashboard
├── submit_paper.php           # Submit new paper
├── my_papers.php              # Author's papers list
├── profile.php                # Author profile
├── paper_view.php             # View paper details
├── papers.php                 # Browse all published papers
├── published.php              # Public paper view
├── certificate.php            # Certificate viewer
├── 404.php                    # Error page
└── logout.php
```

## Configuration

Edit `includes/config.php` if needed:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'research_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // XAMPP default is empty

// Base paths
define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', 'http://localhost/research_paper_submission');
```

## Common Issues

### Issue: "GD library not found"
**Solution:** Enable GD extension in php.ini and restart Apache

### Issue: "FPDF library not found"
**Solution:** Ensure `vendor/fpdf186/fpdf.php` exists

### Issue: "Failed to save uploads"
**Solution:** Create upload directories with write permissions

### Issue: "Parse error" with match()
**Solution:** Code uses if-else for PHP 7.4 compatibility (already fixed)

### Issue: Database connection failed
**Solution:** Check credentials in `includes/config.php` and ensure MySQL is running

## Security Recommendations

After installation:

1. **Change admin password** immediately
2. **Update database credentials** if not using XAMPP defaults
3. **Enable HTTPS** in production
4. **Restrict file upload permissions**
5. **Set proper directory permissions** (uploads/ should be writable)
6. **Backup database** regularly

## Testing the System

### Test Workflow:

1. **Register as Author:**
   - Go to Register page
   - Create new author account
   - Login

2. **Submit Paper:**
   - Click "Submit Paper"
   - Fill in all fields
   - Add multiple author email addresses
   - Upload PDFs (sample PDFs work)
   - Submit

3. **Admin Review:**
   - Logout and login as admin
   - Go to "Papers" in admin panel
   - View the submitted paper
   - Click "Review / Change Status"
   - Change status to "Approved"

4. **Generate Certificates:**
   - After approving, click "Generate Certificates"
   - System creates one certificate per author email
   - View certificates in the sidebar

5. **Author Views Certificates:**
   - Logout and login as original author
   - Go to "My Papers"
   - Click "View" on the approved paper
   - See all certificates listed
   - Click to download individual certificates

## Support

For issues or questions:
- Check `MULTI_AUTHOR_CERTIFICATE_GUIDE.md` for feature documentation
- Review code comments in PHP files
- Check browser console for JavaScript errors
- Check Apache error logs in XAMPP

## Credits

- **Bootstrap 5.3.0** - UI Framework
- **Font Awesome 6.4.0** - Icons
- **FPDF** - PDF Generation
- **PHP QR Code** - QR Code Generation
