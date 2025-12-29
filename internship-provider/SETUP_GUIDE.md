# 📋 SETUP INSTRUCTIONS FOR INTERNSHIP PLATFORM

## Step 1: Prerequisites Check
✓ XAMPP/WAMP/LAMP installed
✓ PHP 7.4+
✓ MySQL 5.7+
✓ phpMyAdmin access

## Step 2: Extract Files
- Extract the project to: `C:\xampp\htdocs\internship-provider\`
- Or your webroot equivalent: `/var/www/html/internship-provider/`

## Step 3: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create new database
3. Name: `internship_platform`
4. Charset: `utf8mb4_unicode_ci`
5. Click "Create"

## Step 4: Run Schema Setup
1. Open: `http://localhost/internship-provider/includes/db_schema.php`
2. This will automatically:
   - Create all required tables
   - Create default admin account
   - Insert default levels
   - Show confirmation messages

## Step 5: Verify Installation
1. Try login: `http://localhost/internship-provider/auth/login.php`
2. Use credentials:
   - Email: admin@internship.com
   - Password: admin123
   - Role: Admin

## Step 6: Create Test Data
### As Admin:
1. Login to admin dashboard
2. Create a test internship in "Manage Internships"
3. Publish the internship
4. Create tasks in "Tasks & Levels"

### As Intern:
1. Register new account
2. Browse internships
3. Apply with ₹500 proof
4. Wait for admin approval

## 🔧 Configuration (if needed)

### Database Connection
Edit: `includes/config.php`
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'internship_platform');
```

### File Upload Permissions
Windows: Usually automatic
Linux: `chmod 755 assets/uploads/`

## 📁 File Structure Created
```
✓ assets/css/
✓ assets/js/
✓ assets/uploads/
✓ admin/
✓ user/
✓ auth/
✓ includes/
✓ pdf/ (for future)
✓ index.php
✓ README.md
```

## ✅ Features Ready to Use

### Admin Features:
- [x] Dashboard with stats
- [x] Manage internships (create/edit/delete/publish)
- [x] View applications (approve/reject)
- [x] Create tasks per level
- [x] Review task submissions
- [x] Mark attendance
- [x] Generate certificates

### User Features:
- [x] Register account
- [x] Browse internships
- [x] Apply with payment proof
- [x] View active internship
- [x] Submit tasks
- [x] Track attendance
- [x] Download certificate

## 🧪 Test Workflow

### Complete Internship Cycle:
1. **Admin**: Create Internship → "Web Developer - 3 months"
2. **Admin**: Create 2 tasks for Level 1
3. **User**: Register → Browse → Apply
4. **Admin**: Approve application → Offer letter generated
5. **User**: Submit Level 1 tasks
6. **Admin**: Approve submissions → Level 1 complete
7. **Admin**: Generate Certificate
8. **User**: Download Certificate

## 🐛 Common Issues & Fixes

### Issue: "Connection failed: Unknown database"
**Fix**: Run `includes/db_schema.php` again to create database

### Issue: "UPLOAD_ERR_OK - file not found"
**Fix**: Check `assets/uploads/` permissions (chmod 755)

### Issue: Login not working
**Fix**: Clear browser cookies, try incognito mode

### Issue: File uploads failing
**Fix**: Check PHP `upload_max_filesize` in php.ini

## 📞 Support Resources

### Documentation:
- Read `README.md` for detailed features
- Check database schema in `includes/db_schema.php`
- Review validation in `includes/validation.php`

### Debugging:
- Check browser console (F12) for JS errors
- Check PHP error logs in XAMPP
- Check database in phpMyAdmin

## 🚀 Next Steps

### To Extend the Platform:
1. Add email notifications
2. Integrate payment gateway (Razorpay)
3. Create REST API
4. Build mobile app
5. Add advanced reporting
6. Implement real-time notifications

## 📊 Database Overview

### Main Tables:
- `users` - Admin & intern accounts
- `internships` - Job postings
- `applications` - Submitted applications
- `offer_letters` - Generated offers
- `levels` - 4 progression levels
- `tasks` - Task definitions
- `task_submissions` - Intern work submissions
- `attendance` - Daily records
- `certificates` - Completion certificates

## 🎯 Key Features Summary

✓ Dual authentication (Admin & Intern)
✓ Secure password hashing (bcrypt)
✓ Responsive Bootstrap UI
✓ File upload handling
✓ Form validation
✓ Role-based access control
✓ Task progression system
✓ Attendance tracking
✓ Certificate generation
✓ Comprehensive dashboards

## 💡 Pro Tips

1. **Performance**: Database queries use prepared statements
2. **Security**: All inputs sanitized and validated
3. **UX**: Mobile-responsive design for all pages
4. **Scalability**: Clean folder structure for easy expansion
5. **Maintenance**: Well-commented code for understanding

## 📚 Learning Resources

- Bootstrap 5: https://getbootstrap.com
- PHP Best Practices: https://www.php.net
- MySQL Documentation: https://dev.mysql.com
- Security: https://owasp.org

---

**Platform is ready for use! 🎉**

Last Updated: 2025
Created for Learning Purposes
