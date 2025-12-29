# Digital Tarai - Software Development Company Website

This is a professional startup website for Digital Tarai, a software development company based in Siraha, Nepal.

## Features

### Frontend
- **Modern, Clean Design**: White background with black navbar and footer
- **Responsive**: Fully responsive on mobile, tablet, and desktop
- **Professional Theme**: Tailwind CSS with purple accent colors
- **Smooth Animations**: Hover effects and transitions

### Pages
1. **Home Page**
   - Hero section with CTA buttons
   - Services preview
   - Why choose us section
   - Recent blog posts
   - Contact form

2. **Services Page**
   - Web Development
   - Mobile App Development
   - Custom Software
   - UI/UX Design

3. **How It Works Page**
   - Step-by-step process visualization
   - Detailed explanations for each step

4. **Blog Section**
   - Blog listing with pagination
   - Blog detail pages
   - Category filtering
   - View tracking

5. **Careers & Internships**
   - Internship listings
   - Benefits overview
   - FAQ section
   - ONLY internships (no full-time jobs)

### Backend Features

#### Authentication System
- Student registration and login
- Admin login (separate)
- PHP sessions
- Password hashing with PHP's password_hash()

#### Internship Management
1. **Application Process**
   - Students apply for internships
   - Application form with pre-filled details
   - Auto-redirect to payment page

2. **Payment System**
   - Manual payment verification
   - Screenshot upload
   - Payment status tracking
   - Admin verification dashboard

3. **Course Modules**
   - Modules per internship
   - Student progress tracking
   - Certificate generation on completion

4. **Admin Dashboard**
   - View all applications
   - Manage payment verification
   - Approve/reject applications
   - Generate offer letters (template ready)

#### Database (MySQL)
- users
- internships
- applications
- payments
- modules
- student_module_progress
- blogs
- activity_logs

## Project Structure

```
DigitalTarai/
├── app/
│   ├── controllers/
│   │   └── AuthController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Internship.php
│   │   ├── Application.php
│   │   ├── Payment.php
│   │   └── Blog.php
│   └── views/
│       ├── header.php
│       ├── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       └── pages/
├── config/
│   ├── config.php
│   └── database.php
├── lib/
│   └── functions.php
├── public/
│   ├── css/
│   ├── js/
│   ├── uploads/
│   └── downloads/
├── student/
│   ├── dashboard.php
│   └── application-detail.php
├── admin/
│   ├── dashboard.php
│   ├── application-detail.php
│   └── verify-payment.php
├── pages/
│   ├── services.php
│   ├── how-it-works.php
│   ├── blog.php
│   ├── blog-detail.php
│   ├── careers.php
│   ├── internship-detail.php
│   ├── internship-apply.php
│   ├── internship-payment.php
│   └── contact.php
├── index.php
├── auth.php
├── setup.php
└── .htaccess
```

## Installation & Setup

### 1. Create Database
```bash
# Run setup.php in your browser
http://localhost/ai/DigitalTarai/setup.php
```

### 2. Default Login Credentials
- **Admin**: admin@digitaltarai.com / admin123
- **Student**: ram@example.com / student123

### 3. Configure Database
Edit `config/database.php` with your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'digital_tarai');
```

## Key Features Details

### Internship Application Flow
1. Student views internship details
2. Student clicks "Apply Now"
3. If not logged in → redirect to login
4. If logged in → show application confirmation
5. After confirmation → Payment screenshot upload page
6. Admin verifies payment → Application approved
7. Student gets offer letter and course access

### Admin Workflow
1. View all applications in dashboard
2. Check pending payments
3. View payment screenshots
4. Approve/reject payments
5. Applications automatically updated
6. Students notified of status

### Payment Verification
- Students upload payment screenshot
- Payment marked as "Pending" initially
- Admin reviews screenshot
- Admin approves (verified) or rejects
- On approval: Application approved, student gets offer letter
- On rejection: Student can retry

## Security Features
- CSRF token generation (ready to use)
- Password hashing with bcrypt
- SQL injection prevention via prepared statements
- File upload validation
- Session-based authentication
- Activity logging

## Dummy Data Included
- 1 Admin account
- 4 Student accounts
- 4 Internship programs
- 16 Course modules
- 4 Blog posts
- 1 Sample application

## Customization

### Colors
- Primary: Purple (#8b5cf6)
- Background: White (#ffffff)
- Navbar/Footer: Black (#1a1a1a)
- Accent: Gray/Purple

Edit in `app/views/header.php` CSS section

### Company Info
Edit `config/config.php`:
```php
define('COMPANY_EMAIL', 'your@email.com');
define('COMPANY_PHONE', '+977-...');
define('COMPANY_LOCATION', 'Your Location');
```

### Add Internships
Login as admin and add internships with modules through database queries or create admin interface

## Browser Compatibility
- Chrome (Latest)
- Firefox (Latest)
- Safari (Latest)
- Edge (Latest)

## Performance
- Optimized queries
- Responsive images
- Minimal CSS/JS
- Fast page loads
- Mobile-first approach

## Future Enhancements
- Payment gateway integration (Khalti, Esewa)
- Email notifications
- Certificate PDF generation
- Admin email templates
- Analytics dashboard
- Student testimonials
- Social login integration
- Video course support

## Support
For issues or improvements, contact: info@digitaltarai.com

---

Built with ❤️ using PHP, MySQL, and Tailwind CSS
