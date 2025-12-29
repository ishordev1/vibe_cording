# 🎓 Internship Platform - Complete Management System

A comprehensive web-based internship management platform built with HTML, CSS, JavaScript, Bootstrap, PHP, and MySQL. This platform enables organizations to manage internships, applications, tasks, attendance, and certificates in a professional and scalable manner.

## ✨ Features

### 1. **Authentication & Authorization**
- User registration for interns
- Admin and intern login with role-based access control
- Secure password hashing with bcrypt
- Session management with timeout
- Protected pages requiring authentication

### 2. **Internship Posting (Admin)**
- Create, edit, publish, and delete internship postings
- Define internship details:
  - Title and role description
  - Duration (1-4 months flexible)
  - Type (Task-based or Learning)
  - Remote/On-site/Hybrid options
  - Required skills
  - Number of available positions

### 3. **Internship Application (User)**
- Browse published internships
- Submit applications with:
  - Personal details
  - Cover letter
  - LinkedIn/GitHub profiles
  - ₹500 security deposit proof upload
- Track application status (Pending/Approved/Rejected)

### 4. **Offer Letter System**
- Auto-generate offer letters upon application approval
- Unique Offer Letter IDs
- Includes:
  - Intern name
  - Internship role
  - Start and end dates
  - Duration
  - Verification capability

### 5. **Task & Level System**
- 4 Level progression (Level 1-4):
  - Foundation Level
  - Intermediate Level
  - Advanced Level
  - Expert Level
- Create text-based tasks per level
- Same tasks for all interns
- Task components:
  - Title and description
  - Deliverables specification
  - Submission format (Link/Text/File)
  - Order sequence

### 6. **Task Submission & Review**
- Interns submit work via:
  - GitHub/Drive links
  - Text descriptions
  - File uploads
- Admin review with approval/rejection/rework options
- Feedback system
- Resubmission capability

### 7. **Attendance Management**
- Manual attendance marking by admin
- Status options: Present/Absent/Late
- Date-wise records
- Attendance statistics
- Notes capability
- Linked to task approvals

### 8. **Certificate System**
- Auto-generated certificates upon level completion
- Unique Certificate IDs
- Includes:
  - Intern name
  - Internship duration
  - Issue date
  - Certificate verification
- Print-friendly certificate design

### 9. **Comprehensive Dashboards**
- **Intern Dashboard:**
  - Application status overview
  - Active internship details
  - Level-wise task progress
  - Attendance tracking
  - Certificate download
  - Statistics and quick actions

- **Admin Dashboard:**
  - Internship management overview
  - Application metrics
  - Recent activities
  - Quick access to all functions
  - Statistics and analytics

### 10. **Technical Excellence**
- Clean MVC-inspired folder structure
- Input validation (Client & Server-side)
- SQL injection prevention with prepared statements
- Responsive Bootstrap UI
- Professional styling
- File upload handling
- Error handling and logging

## 📁 Project Structure

```
internship-provider/
├── assets/
│   ├── css/
│   │   └── style.css              # Main stylesheet
│   ├── js/                        # JavaScript files
│   └── uploads/                   # User uploads (payments, files)
│       ├── payments/              # Payment proofs
│       └── submissions/           # Task submissions
│
├── admin/
│   ├── dashboard.php              # Admin dashboard
│   ├── internships/
│   │   └── manage.php             # Create/edit/delete internships
│   ├── applications/
│   │   ├── view.php               # View & approve/reject applications
│   │   └── view_detail.php        # Application details
│   ├── tasks/
│   │   └── manage.php             # Create tasks for levels
│   ├── attendance/
│   │   └── manage.php             # Mark attendance
│   └── certificates/
│       └── generate.php           # Generate certificates
│
├── user/
│   ├── dashboard.php              # Intern dashboard
│   ├── applications/
│   │   └── browse.php             # Browse & apply for internships
│   ├── tasks/
│   │   └── view.php               # View & submit tasks
│   ├── attendance/
│   │   └── view.php               # View attendance records
│   └── certificates/
│       └── view.php               # View & print certificate
│
├── auth/
│   ├── login.php                  # Login page (admin/intern)
│   ├── register.php               # Intern registration
│   └── logout.php                 # Logout handler
│
├── includes/
│   ├── config.php                 # Database configuration
│   ├── db_schema.php              # Database schema creation
│   ├── session.php                # Session management
│   └── validation.php             # Form validation functions
│
├── pdf/                           # PDF generation (future)
├── index.php                      # Home page/redirect
└── README.md                      # Documentation
```

## 🚀 Quick Start Guide

### Prerequisites
- XAMPP/LAMP/WAMP stack installed
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Edge, Safari)

### Installation Steps

#### 1. **Setup Database**
- Open `http://localhost/phpmyadmin`
- Create a new database named `internship_platform`
- Import the schema by visiting:
  ```
  http://localhost/internship-provider/includes/db_schema.php
  ```
  This will create all necessary tables and insert default data.

#### 2. **Configure Database Connection**
- Edit `includes/config.php` if your database credentials differ:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'internship_platform');
  ```

#### 3. **Set File Permissions**
- Ensure upload directories are writable:
  ```bash
  chmod 755 assets/uploads/
  chmod 755 assets/uploads/payments/
  chmod 755 assets/uploads/submissions/
  ```

#### 4. **Access the Platform**
- Visit: `http://localhost/internship-provider`
- Login with demo admin account:
  - **Email:** admin@internship.com
  - **Password:** admin123
- Or register as a new intern

### 📝 Admin Login Credentials (Default)
- **Email:** admin@internship.com
- **Password:** admin123

## 🔄 Workflow

### For Admins:
1. **Create Internship** → Dashboard → "Create Internship"
2. **Publish Internship** → Once created, publish to make visible
3. **Review Applications** → Applications → Approve/Reject
4. **Create Tasks** → Tasks & Levels → Add tasks per level
5. **Mark Attendance** → Mark Attendance → Daily record
6. **Review Submissions** → Review Submissions → Approve/Reject work
7. **Generate Certificates** → Generate Certificates (after all levels complete)

### For Interns:
1. **Register Account** → Provide all details
2. **Browse Internships** → Find opportunities
3. **Apply** → Submit application + ₹500 proof
4. **Wait for Approval** → Admin reviews your application
5. **View Tasks** → Dashboard → My Tasks
6. **Submit Tasks** → Complete and submit per level
7. **Track Progress** → Monitor attendance and approvals
8. **Get Certificate** → Download after completing all levels

## 🔐 Security Features

- **Password Hashing:** bcrypt with cost factor 10
- **SQL Injection Prevention:** Prepared statements throughout
- **Session Management:** Secure session handling with timeout
- **File Upload Validation:** Type and size checks
- **Input Sanitization:** htmlspecialchars() on all user input
- **CSRF Protection:** Session validation on form submissions
- **Role-Based Access Control:** Admin vs Intern separation

## 📊 Database Schema

### Key Tables:
- **users** - Admin and intern accounts
- **internships** - Internship postings
- **applications** - Internship applications
- **offer_letters** - Generated offer letters
- **levels** - 4 level progression system
- **tasks** - Task definitions per level
- **task_submissions** - Intern submissions
- **attendance** - Daily attendance records
- **certificates** - Generated certificates
- **level_completion** - Track level completions

## 🎨 UI/UX Features

- **Responsive Design:** Works on desktop, tablet, mobile
- **Bootstrap 5:** Professional UI framework
- **Modern Colors:** Purple gradient theme
- **Icons:** Bootstrap Icons for visual clarity
- **Forms:** Comprehensive validation feedback
- **Navigation:** Intuitive sidebars and navbars
- **Modals:** Clean modal dialogs for actions
- **Tables:** Sortable, responsive data tables

## ⚡ Key PHP Functions

### Session Management (`includes/session.php`):
```php
isLoggedIn()           // Check if user is logged in
hasRole($role)         // Check user role
requireLogin()         // Redirect if not logged in
requireAdmin()         // Redirect if not admin
requireIntern()        // Redirect if not intern
logout()               // Destroy session
```

### Validation (`includes/validation.php`):
```php
validateEmail($email)       // Validate email
validatePassword($password) // Validate password strength
validatePhone($phone)       // Validate phone
validateFile($file)         // Validate file upload
sanitizeInput($input)       // Sanitize input
validateRegistration($data) // Validate registration form
```

## 📧 Email Integration (Future)

To add email notifications, implement in:
- Application approval/rejection
- Task review notifications
- Certificate generation alerts

## 🔄 Workflow Examples

### Example 1: Admin Creates Internship
1. Login as admin
2. Go to "Manage Internships"
3. Click "Create New"
4. Fill internship details
5. Publish to make it visible

### Example 2: Intern Applies
1. Login/Register as intern
2. Browse Internships
3. Click Apply Now
4. Fill cover letter, links
5. Upload ₹500 payment proof
6. Submit application

### Example 3: Complete Internship
1. Wait for admin approval
2. Start completing tasks level-by-level
3. Submit tasks with deliverables
4. Admin reviews and approves
5. Attendance marked daily
6. After all levels: Certificate generated

## 🐛 Troubleshooting

### Problem: Database connection error
**Solution:** Check database credentials in `includes/config.php`

### Problem: Upload files not working
**Solution:** Check folder permissions on `assets/uploads/`

### Problem: Sessions not persisting
**Solution:** Ensure PHP sessions are configured in php.ini

### Problem: Password reset needed
**Solution:** Update hash in users table or recreate admin

## 📱 API Endpoints (For Future Mobile App)

Future API endpoints can be created by adding:
- `/api/auth/login`
- `/api/applications/submit`
- `/api/tasks/get`
- `/api/submissions/upload`
- etc.

## 🚀 Deployment Considerations

### For Production:
1. Set `SESSION_TIMEOUT` appropriately
2. Use HTTPS (set `session.cookie_secure` to 1)
3. Add rate limiting
4. Implement logging
5. Use environment variables for credentials
6. Regular database backups
7. CDN for static assets

## 📚 Additional Features to Consider

- Email notifications
- SMS alerts
- Advanced analytics/reporting
- Bulk operations
- API for third-party integrations
- Mobile app
- Video task submissions
- Real-time notifications
- Payment gateway integration

## 🤝 Contributing

This is a learning-purpose platform. Feel free to extend with additional features:
- Payment integration (Razorpay, PayPal)
- Email notifications
- Video submissions
- Bulk imports/exports
- Dashboard charts
- Advanced search/filters

## 📄 License

This project is created for educational purposes.

## 👨‍💼 Support

For issues or questions:
1. Check the documentation
2. Review database schema
3. Check file permissions
4. Verify PHP/MySQL versions
5. Check browser console for JS errors

---

**Happy Internship Management! 🎓**

### Key Files to Remember:
- `includes/config.php` - Database configuration
- `includes/db_schema.php` - Run once to create tables
- `includes/session.php` - Session functions
- `includes/validation.php` - Validation functions
- `auth/login.php` - Login page
- `auth/register.php` - Registration page
- `admin/dashboard.php` - Admin home
- `user/dashboard.php` - Intern home

Last Updated: 2025
