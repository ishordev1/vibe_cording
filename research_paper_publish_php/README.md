# Research Paper Submission System

> **Professional web application for managing research paper submissions with multi-author certificate generation**

A complete, production-ready research paper submission and management system built with PHP, MySQL, and Bootstrap 5. Features include role-based authentication, paper submission workflow, admin review system, and automated certificate generation with QR code verification.

---

## ✨ Key Features

### For Authors
- ✅ **User Registration & Authentication** - Secure account creation with email validation
- ✅ **Paper Submission** - Upload research papers with abstracts and copyright forms
- ✅ **Real-time Status Tracking** - Monitor paper review progress (Pending, Under Review, Approved, Rejected)
- ✅ **Multi-Author Support** - Add individual email addresses for each author
- ✅ **Certificate Downloads** - Personalized publication certificates for each author
- ✅ **Professional Dashboard** - Beautiful, responsive interface with statistics

### For Admins
- ✅ **Paper Management** - View, search, and filter all submitted papers
- ✅ **Review System** - Approve or reject submissions with status updates
- ✅ **Certificate Generation** - Automated individual certificates per author with QR codes
- ✅ **Final PDF Upload** - Publish formatted versions of accepted papers
- ✅ **Admin Dashboard** - Comprehensive statistics and recent submissions overview

### General Features
- ✅ **IEEE-Style Landing Page** - Professional public-facing homepage
- ✅ **Browse Published Papers** - Public access to approved research papers
- ✅ **Certificate Verification** - QR code-based certificate authentication
- ✅ **Responsive Design** - Mobile-friendly Bootstrap 5 interface
- ✅ **Secure File Uploads** - PDF validation with MIME type checking
- ✅ **Session-Based Auth** - Role-based access control (Author/Admin)

---

## 🛠 Tech Stack

- **Backend:** PHP 7.4+ (PDO, Sessions)
- **Frontend:** Bootstrap 5.3.0, Font Awesome 6.4.0, Custom CSS
- **Database:** MySQL/MariaDB with InnoDB engine
- **Authentication:** PHP Sessions with bcrypt password hashing
- **PDF Generation:** FPDF library
- **QR Codes:** PHP QR Code library
- **File Handling:** Secure uploads with validation

---

## 📁 Project Structure

```
research_paper_submission/
├── admin/                      # Admin panel
│   ├── includes/
│   │   ├── admin_sidebar.php  # Reusable admin sidebar
│   │   └── admin_navbar.php   # Admin top navigation
│   ├── dashboard.php          # Admin statistics dashboard
│   ├── papers.php             # All papers management
│   ├── view_paper.php         # Detailed paper view with PDFs
│   ├── review.php             # Change paper status
│   ├── upload_final_pdf.php   # Publish final version
│   └── generate_certificate.php # Multi-author certificates
├── assets/
│   ├── css/
│   │   └── style.css          # Custom CSS variables & styles
│   └── js/
│       └── main.js            # Form validation, UI helpers
├── includes/
│   ├── config.php             # Database & path configuration
│   ├── db.php                 # PDO database connection
│   ├── functions.php          # Auth & validation helpers
│   ├── author_sidebar.php     # Author dashboard sidebar
│   └── author_navbar.php      # Author top navigation
├── sql/
│   └── schema.sql             # Complete database schema
├── uploads/                   # File storage (writable)
│   ├── papers/                # Submitted research papers
│   ├── copyrights/            # Copyright forms
│   ├── published/             # Final published PDFs
│   ├── certificates/          # Generated certificates
│   └── qrcodes/               # QR code images
├── vendor/                    # Third-party libraries
│   ├── fpdf186/               # FPDF library
│   └── phpqrcode/             # PHP QR Code
├── index.php                  # Landing page (IEEE style)
├── login.php                  # User login
├── register.php               # User registration
├── dashboard.php              # Author dashboard
├── submit_paper.php           # Paper submission form
├── my_papers.php              # Author's papers list
├── profile.php                # User profile
├── paper_view.php             # View paper details
├── papers.php                 # Browse published papers
├── published.php              # Public paper display
├── certificate.php            # Certificate viewer & verification
├── 404.php                    # Error page
├── logout.php                 # Session termination
├── INSTALLATION.md            # Setup guide
└── MULTI_AUTHOR_CERTIFICATE_GUIDE.md  # Feature documentation
```
├── submit_paper.php
├── paper_view.php
├── published.php
├── certificate.php
├── logout.php
├── .htaccess
├── .gitignore
├── composer.json
└── README.md
```

---

## ⚡ Features

### 🧑‍💻 Author Features
- ✅ **Registration** (creates author account)
- ✅ **Login** (session-based authentication)
- ✅ **Dashboard** (view all submitted papers)
- ✅ **Submit Paper** (upload PDF, copyright form, add metadata)
- ✅ **View Paper Status** (Pending, Under Review, Approved, Rejected)
- ✅ **View Published Paper** (if approved)
- ✅ **Download Certificate** (auto-generated after approval)

### 🛠 Admin Features
- ✅ **Admin Login** (separate panel)
- ✅ **Dashboard** (stats overview)
- ✅ **View All Submissions**
- ✅ **Search by Title**
- ✅ **Preview Uploaded PDFs** (iframe)
- ✅ **Review & Change Status** (Approve/Reject)
- ✅ **Upload Final Formatted PDF** (after manual formatting)
- ✅ **Generate Certificate** (auto-generated PDF with QR code)

### 🧾 Certificate System
- Auto-generates a certificate PDF with:
  - Paper title
  - Authors
  - Publication date
  - Unique Certificate ID
  - QR Code (links to public certificate page)
  - Custom fonts/layout (via FPDF)
- QR Code redirects to `/certificate.php?id=<paper_id>` showing:
  - Paper details
  - Authors
  - Download published PDF

---

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.4+ with GD extension
- Modern web browser

### Installation

1. **Clone or download** this repository to `C:\xampp\htdocs\research_paper_submission\`

2. **Enable GD extension** (for QR codes):
   - Open `C:\xampp\php\php.ini`
   - Find `;extension=gd` and remove the semicolon
   - Restart Apache

3. **Create database:**
   ```sql
   CREATE DATABASE research_db;
   ```

4. **Import schema:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Select `research_db` database
   - Go to SQL tab
   - Paste contents from `sql/schema.sql`
   - Click "Go"

5. **Create admin user:**
   ```sql
   INSERT INTO users (name, email, password, role) 
   VALUES ('Admin', 'admin@example.com', 
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
   ```
   **Default Login:** `admin@example.com` / `password`

6. **Access the app:**
   ```
   http://localhost/research_paper_submission/
   ```

📖 **For detailed setup instructions, see [INSTALLATION.md](INSTALLATION.md)**

---

## 📚 Documentation

- **[INSTALLATION.md](INSTALLATION.md)** - Complete setup guide with troubleshooting
- **[MULTI_AUTHOR_CERTIFICATE_GUIDE.md](MULTI_AUTHOR_CERTIFICATE_GUIDE.md)** - Multi-author certificate feature documentation

---

## 🎯 Usage Workflow

### Author Workflow:
1. **Register** → Create account with name, email, password
2. **Login** → Access author dashboard
3. **Submit Paper** → Fill form with title, abstract, authors, upload PDFs
4. **Add Author Emails** → Use "Add Author" button for multiple authors
5. **Wait for Review** → Admin reviews and changes status
6. **View Certificates** → Download individual certificates (one per author)

### Admin Workflow:
1. **Login** → Access admin panel
2. **Review Papers** → View all submitted papers
3. **View Details** → Preview PDFs, read abstract
4. **Approve/Reject** → Change status to "Approved" or "Rejected"
5. **Generate Certificates** → Click button to create certificates for all authors
6. **Upload Final PDF** → (Optional) Upload formatted published version

---

## 🔐 Security Features

- ✅ **Password Hashing** - bcrypt with `password_hash()`
- ✅ **Prepared Statements** - SQL injection prevention
- ✅ **MIME Type Validation** - Only PDF uploads allowed
- ✅ **File Size Limits** - 20MB maximum
- ✅ **Session-Based Auth** - Secure login with role checking
- ✅ **XSS Protection** - Output escaping with `htmlspecialchars()`
- ✅ **Role-Based Access** - Admin/Author separation

---

## 🎨 Design Features

- **IEEE-Inspired Landing Page** - Professional hero section with gradient
- **Responsive Dashboards** - Bootstrap 5 grid system
- **Sidebar Navigation** - Consistent layout across all pages
- **Status Badges** - Color-coded paper status indicators
- **Card-Based Layout** - Modern, clean interface
- **Font Awesome Icons** - Professional iconography
- **Custom CSS Variables** - Easy theme customization
- **Dark Sidebar** - Professional admin panel aesthetic

---

## 📦 Database Schema

### Tables:
- **users** - Author and admin accounts
- **papers** - Submitted research papers with metadata
- **certificates** - Generated certificates (one per author)

### Key Relationships:
- `papers.created_by` → `users.id` (Author who submitted)
- `certificates.paper_id` → `papers.id` (Multiple certificates per paper)
- `certificates.author_id` → `users.id` (Submitting author)

---

## 🔧 Configuration

Edit `includes/config.php` to customize:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'research_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Upload limits
// Modify in includes/functions.php:
// $maxSize = 20 * 1024 * 1024; // 20MB
```

---

## 📸 Screenshots

### Landing Page
IEEE-style homepage with hero section, features, and published papers

### Author Dashboard
Professional dashboard with statistics, recent submissions, and quick actions

### Admin Panel
Comprehensive paper management with search, filtering, and bulk actions

### Certificate System
Automated individual certificates with QR codes for verification

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| "GD library not found" | Enable `extension=gd` in php.ini |
| "FPDF not found" | Check `vendor/fpdf186/fpdf.php` exists |
| Database connection error | Verify credentials in `includes/config.php` |
| Upload failed | Ensure `uploads/` subdirectories exist and are writable |
| Parse error (match) | Already fixed - code uses PHP 7.4 compatible if-else |

---

## 🤝 Contributing

Contributions welcome! To contribute:
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is open source and available for educational purposes.

---

## 🙏 Credits

- **Bootstrap** - Responsive UI framework
- **Font Awesome** - Icon library
- **FPDF** - PDF generation
- **PHP QR Code** - QR code generation

---

## 📞 Support

For issues or questions:
- Check documentation in `INSTALLATION.md` and `MULTI_AUTHOR_CERTIFICATE_GUIDE.md`
- Review code comments
- Check Apache error logs in XAMPP

---

**Built with ❤️ using PHP, MySQL, and Bootstrap**
