# Admin Dashboard - Complete Documentation

## Overview
A comprehensive admin dashboard for Digital Tarai with a professional sidebar navigation and multiple management sections.

## Features Implemented

### 1. **Dashboard Home** (Main Overview)
- **Statistics Cards**: 
  - Total Users, Total Internships, Total Applications, Pending Payments
- **Applications Status Chart**: Shows approved, pending, and rejected applications with progress bars
- **Quick Actions**: Direct links to manage users, internships, applications, and payments
- **Recent Applications Table**: Last 5 applications with status

### 2. **Internships Management** 
#### Internships List View
- Grid layout showing all internship programs
- Each card displays:
  - Title and description
  - Duration and fees
  - Total applicants count
  - "View Applicants" button

#### Course Detail View (Click on any internship)
- Full course information
- Statistics: Total applicants, Approved, Pending
- Applicants table with:
  - Student name, email, phone
  - Application status (Pending/Approved/Rejected)
  - Applied date
  - "View Details" button for each applicant

#### User Application Detail View
- Complete user information
- Application status
- Application date
- **Quick Actions Panel** with:
  - **Generate Certificate** button
  - **Generate Offer Letter** button
  - **Approve Application** button

### 3. **Users Management**
- List all students registered on the platform
- Display: Name, Email, Phone, Status, Join Date
- View user details (expandable feature)

### 4. **Applications Management**
- Filter by status: All, Pending, Approved, Rejected
- Table showing: Student, Internship, Status, Applied Date
- Actions:
  - **Approve** pending applications
  - **Reject** applications
  - View status of reviewed applications

### 5. **Payments Verification**
- Filter by status: Pending, Verified, Rejected
- Display:
  - Student name and email
  - Internship program
  - Payment amount
  - Payment method
  - Payment status
  - Date submitted
- Actions:
  - **Verify** pending payments
  - **Reject** payments
  - **View** payment screenshot/proof

### 6. **Blogs Management**
- List all blog posts with:
  - Title and excerpt
  - Author name
  - Publication status (Published/Draft)
  - View count
  - Creation date
- Actions:
  - Create new blog post
  - Edit existing posts
  - Delete posts

### 7. **Certificates Generation**
- Shows all approved applications
- One-click certificate generation
- Beautiful, professional PDF-style certificate with:
  - Official Digital Tarai branding
  - Student name
  - Internship program name
  - Duration of internship
  - Official seal and signatures
  - Professional border design
- Downloads as HTML file (can be printed to PDF)

### 8. **Offer Letters Generation**
- Shows all approved applications
- One-click offer letter generation
- Professional offer letter template including:
  - Company header with contact information
  - Date of letter
  - Student information
  - Internship details
  - Position, duration, and fees
  - Key benefits
  - Signature sections (company and student)
- Downloads as HTML file (can be printed or saved as PDF)

## Sidebar Navigation
Professional left sidebar with:
- **Admin Panel** branding with shield icon
- Navigation links with hover effects
- Badge counters showing:
  - Total Users
  - Total Internships
  - Total Applications
  - Pending Payments
  - Published Blogs
- **Logout** button (red)
- Dark theme (#2c3e50 background)
- Purple highlight for active section (#8e44ad)

## Top Navigation Bar
Sticky top bar with:
- Current section title
- Welcome message with admin name
- Current date and time
- Message alerts (success/error)

## Key Features
✅ **Responsive Design**: Works on desktop, tablet, and mobile  
✅ **Color-coded Status**: Different colors for different statuses  
✅ **Statistics Cards**: Quick overview of key metrics  
✅ **Drill-down Navigation**: From list → detail → user application → actions  
✅ **Batch Operations**: Approve/reject multiple applications  
✅ **Document Generation**: PDF-ready certificates and offer letters  
✅ **User Authentication**: Admin-only access with session verification  
✅ **Data Validation**: Input validation and security checks  

## Database Integration
- Uses existing database tables: users, internships, applications, payments, blogs
- Queries are parameterized and secure
- Proper foreign key relationships maintained

## Styling
- **Tailwind CSS**: Utility-first CSS framework
- **Font Awesome Icons**: 50+ icons for visual appeal
- **Professional Color Scheme**:
  - Primary: Purple (#8e44ad)
  - Secondary: Gray/Blue shades
  - Status colors: Green (approved), Orange (pending), Red (rejected)

## How to Access
1. Navigate to: `http://localhost/ai/DigitalTarai/admin/`
2. Login as admin: `admin@digitaltarai.com / admin123`
3. You'll be redirected to login if not authenticated

## File Structure
```
admin/
├── index.php                 # Main dashboard controller
└── sections/
    ├── dashboard.php         # Dashboard overview
    ├── users.php            # User management
    ├── internships.php       # Internship management
    ├── applications.php      # Application management
    ├── payments.php          # Payment verification
    ├── blogs.php            # Blog management
    ├── certificates.php      # Certificate generation
    └── offer-letters.php     # Offer letter generation
```

## Working Flow

### For Internship Management:
1. Admin goes to "Internships" section
2. Clicks on an internship to see all applicants
3. Clicks "View Details" on an applicant to see their full information
4. Can click "Generate Certificate" or "Generate Offer Letter"
5. Documents are generated and ready to download/print

### For Payment Verification:
1. Admin goes to "Payments" section
2. Sees pending payment screenshots
3. Verifies payment after checking screenshot
4. Can view the uploaded proof image
5. Marks as verified or rejected

### For Application Management:
1. Admin can filter applications by status
2. Review each application
3. Approve or reject with one click
4. Once approved, student gets access to generate certificate/offer letter

## Future Enhancements (Optional)
- Email notifications to students
- PDF download for certificates and letters
- Bulk export of data
- Advanced analytics and reporting
- Student progress tracking
- Module completion tracking
- Payment gateway integration
- Email template customization

---
**Created**: December 27, 2025  
**Version**: 1.0  
**Status**: Production Ready
