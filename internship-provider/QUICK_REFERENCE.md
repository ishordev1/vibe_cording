# ⚡ QUICK REFERENCE GUIDE

## 🚀 30-SECOND SETUP

1. **Extract files** to `C:\xampp\htdocs\internship-provider\`
2. **Create database** `internship_platform` in phpMyAdmin
3. **Run** `http://localhost/internship-provider/includes/db_schema.php`
4. **Login** with `admin@internship.com` / `admin123`
5. **Create internship** → Browse as user → Test

---

## 📍 IMPORTANT URLS

| Task | URL |
|------|-----|
| Home | `http://localhost/internship-provider` |
| Login | `http://localhost/internship-provider/auth/login.php` |
| Register | `http://localhost/internship-provider/auth/register.php` |
| Admin Dashboard | `http://localhost/internship-provider/admin/dashboard.php` |
| User Dashboard | `http://localhost/internship-provider/user/dashboard.php` |
| Database Setup | `http://localhost/internship-provider/includes/db_schema.php` |
| phpMyAdmin | `http://localhost/phpmyadmin` |

---

## 👤 LOGIN CREDENTIALS

**Admin Account (Pre-created):**
- Email: `admin@internship.com`
- Password: `admin123`
- Role: Admin

**User Account (Create New):**
- Register via `/auth/register.php`
- Use same email/password for login

---

## 📂 KEY FILES LOCATION

| File | Purpose | Path |
|------|---------|------|
| Database Config | Connection settings | `includes/config.php` |
| Schema Creation | Create tables | `includes/db_schema.php` |
| Session Manager | Auth functions | `includes/session.php` |
| Validation | Form validation | `includes/validation.php` |
| Admin Home | Dashboard | `admin/dashboard.php` |
| User Home | Dashboard | `user/dashboard.php` |
| CSS Stylesheet | All styles | `assets/css/style.css` |

---

## 🔄 USER FLOWS

### Admin Flow:
```
Login → Create Internship → Create Tasks → 
Review Applications → Mark Attendance → Generate Certificate
```

### User Flow:
```
Register → Login → Browse → Apply → 
Wait for Approval → Submit Tasks → 
Get Attendance → Download Certificate
```

---

## 📋 ADMIN QUICK ACTIONS

| Action | Button Location |
|--------|-----------------|
| Create Internship | Dashboard → "Create New" |
| View Applications | Navigation → "Applications" |
| Create Task | Navigation → "Tasks & Levels" |
| Mark Attendance | Navigation → "Mark Attendance" |
| Review Submissions | Navigation → "Review Submissions" |
| Generate Certificate | Navigation → "Generate Certificates" |

---

## 👥 USER QUICK ACTIONS

| Action | Button Location |
|--------|-----------------|
| Browse Internships | Dashboard → "Browse Internships" |
| Submit Task | Dashboard → "My Tasks" |
| View Attendance | Dashboard → "Attendance" |
| Download Certificate | Dashboard → "Certificate" |
| Logout | Top Right → "Logout" |

---

## 🗄️ DATABASE QUICK REFERENCE

### Main Tables:
```
users                  - All accounts (admin + interns)
internships           - Job postings
applications          - Applications to internships
offer_letters         - Generated offers
levels                - 4 progression levels
tasks                 - Task definitions
task_submissions      - Intern work submissions
attendance            - Daily attendance records
certificates          - Completion certificates
level_completion      - Level completion tracker
```

### Default Data:
```
Admin user created ✓
4 Levels created (1-4) ✓
Database ready ✓
```

---

## 🔐 SECURITY QUICK REFERENCE

| Aspect | Implementation |
|--------|-----------------|
| Passwords | bcrypt hashing |
| SQL | Prepared statements |
| Input | htmlspecialchars() |
| Access | Role-based control |
| Files | Type & size validation |
| Session | Secure handlers |

---

## 📊 STATISTICS AVAILABLE

### Admin Can See:
- Total interns registered
- Total internships created
- Pending applications count
- Approved interns count
- Recent applications list

### User Can See:
- Application status
- Active internship details
- Tasks completed count
- Current level
- Attendance days
- Certificate status

---

## ⚙️ CONFIGURATION CHANGES

### To Change Database:
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Your password here
define('DB_NAME', 'internship_platform');
```

### To Change Session Timeout:
Edit `includes/config.php`:
```php
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
```

### To Change Upload Limit:
Edit `includes/config.php`:
```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
```

---

## 🐛 COMMON ISSUES & FIXES

| Issue | Solution |
|-------|----------|
| "Connection failed" | Run db_schema.php again |
| Files not uploading | Check folder permissions |
| Login not working | Clear cookies & try incognito |
| Password too weak | Use: Abc123 format |
| Certificate not generating | Complete all levels first |
| Can't find uploaded file | Check assets/uploads/ folder |

---

## 📱 BROWSER COMPATIBILITY

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full |
| Firefox | ✅ Full |
| Safari | ✅ Full |
| Edge | ✅ Full |
| Mobile Safari | ✅ Responsive |
| Mobile Chrome | ✅ Responsive |

---

## 📦 FILE SIZE LIMITS

| File Type | Max Size |
|-----------|----------|
| Payment Proof | 5 MB |
| Task Submission | 5 MB |
| Document Upload | 5 MB |

---

## 🔗 FORM VALIDATION RULES

| Field | Rules |
|-------|-------|
| Email | Valid email format |
| Password | 8+ chars, uppercase, lowercase, number |
| Phone | 10 digits, Indian format |
| Date | YYYY-MM-DD format |
| URL | Valid http/https URL |
| File | Allowed types only |

---

## 🎯 RECOMMENDED TEST CASES

### Basic Test:
1. Login as admin
2. Create internship
3. Register as user
4. Apply for internship
5. Admin approves
6. User submits task
7. Admin approves task

### Full Cycle:
1. Repeat basic test for all 4 levels
2. Complete all tasks
3. Generate certificate
4. Download certificate
5. Verify certificate details

---

## 📞 SUPPORT RESOURCES

| Resource | Location |
|----------|----------|
| Full Docs | README.md |
| Setup Help | SETUP_GUIDE.md |
| Sample Data | SAMPLE_DATA.md |
| Features | PROJECT_SUMMARY.md |
| Code Comments | In PHP files |

---

## 🎓 KEY LEARNINGS

This project teaches:
- ✅ Full-stack PHP development
- ✅ MySQL database design
- ✅ Bootstrap responsive design
- ✅ Security best practices
- ✅ Form validation
- ✅ File handling
- ✅ Session management
- ✅ Role-based access

---

## 💡 QUICK TIPS

1. **Always run db_schema.php first** - Sets up database
2. **Use admin account for testing** - All features access
3. **Clear browser cache** - If styles not loading
4. **Check console (F12)** - For any JavaScript errors
5. **Use phpMyAdmin** - To verify database changes
6. **Read comments in code** - Explains complex logic
7. **Check README** - For complete documentation

---

## ✅ BEFORE GOING LIVE

- [ ] Change default admin password
- [ ] Set proper permissions on uploads folder
- [ ] Enable HTTPS (set cookie_secure to 1)
- [ ] Configure email notifications
- [ ] Setup database backups
- [ ] Test all user flows
- [ ] Check security settings
- [ ] Verify file uploads work

---

## 🚀 NEXT STEPS

1. **Explore**: Test all admin features
2. **Create Data**: Add sample internships
3. **Test User Flow**: Register and apply
4. **Review Code**: Understand structure
5. **Customize**: Add your branding
6. **Deploy**: When ready for production

---

## 📞 WHEN YOU NEED HELP

1. **Check README.md** - Comprehensive guide
2. **Check SETUP_GUIDE.md** - Installation help
3. **Check SAMPLE_DATA.md** - Examples
4. **Check Project Code** - Comments explain logic
5. **Check Database** - phpMyAdmin to verify

---

**Everything is ready! Start using the platform now! 🎓**

Last Updated: 2025
