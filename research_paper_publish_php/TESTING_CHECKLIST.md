# Testing Checklist - Research Paper Submission System

## Pre-Testing Setup
- [ ] XAMPP Apache and MySQL running
- [ ] Database `research_db` created
- [ ] Schema imported from `sql/schema.sql`
- [ ] Admin user created in database
- [ ] GD extension enabled in php.ini
- [ ] All upload directories exist (papers, copyrights, published, certificates, qrcodes)
- [ ] FPDF library at `vendor/fpdf186/fpdf.php`
- [ ] PHP QR Code library at `vendor/phpqrcode/qrlib.php`

## Public Pages (No Login Required)

### Landing Page (index.php)
- [ ] Page loads without errors
- [ ] Hero section displays with gradient
- [ ] Features cards visible
- [ ] Statistics section shows
- [ ] Recent papers section displays
- [ ] Navigation bar works
- [ ] Login/Register buttons in navbar
- [ ] Responsive on mobile/tablet

### Browse Papers (papers.php)
- [ ] Page loads
- [ ] Shows "No papers" message if empty
- [ ] Search bar functional
- [ ] Paper cards display correctly
- [ ] "View Paper" links work
- [ ] Pagination (if implemented)

### Published Paper View (published.php)
- [ ] Opens from papers.php
- [ ] Shows paper title in hero section
- [ ] Breadcrumb navigation works
- [ ] Abstract card displays
- [ ] PDF viewer shows paper
- [ ] Download button works
- [ ] Share URL button copies link
- [ ] Sidebar shows paper info

### Certificate Verification (certificate.php)
- [ ] Opens with cert_id parameter
- [ ] Displays certificate details
- [ ] Shows author name and email
- [ ] PDF iframe loads certificate
- [ ] Download button works
- [ ] "Back to Home" button works

### 404 Page
- [ ] Displays on invalid URLs
- [ ] Professional gradient design
- [ ] Home and Browse buttons work

## Registration & Login

### Register (register.php)
- [ ] Form displays correctly
- [ ] Name field validation works
- [ ] Email validation (format check)
- [ ] Password strength indicator appears
- [ ] Confirm password validation
- [ ] Duplicate email error shows
- [ ] Success: redirects to login
- [ ] Error messages display properly

### Login (login.php)
- [ ] Form displays
- [ ] Email/password validation
- [ ] Invalid credentials error
- [ ] Success: redirects to dashboard
- [ ] "Don't have account" link works

### Logout
- [ ] Session cleared
- [ ] Redirects to login page

## Author Dashboard

### Dashboard (dashboard.php)
- [ ] Sidebar displays correctly
- [ ] Top navbar appears
- [ ] User name in dropdown
- [ ] Statistics cards show counts
- [ ] Total Papers count accurate
- [ ] Pending/Approved/Rejected counts correct
- [ ] Recent submissions table displays
- [ ] Table shows latest 5 papers
- [ ] Status badges color-coded
- [ ] "View" links work
- [ ] Mobile responsive

### Submit Paper (submit_paper.php)
- [ ] Navbar and sidebar present
- [ ] Title field validation
- [ ] Abstract textarea works
- [ ] Author list field present
- [ ] "Add Author" button works
- [ ] Can add multiple author emails
- [ ] Remove author button works (except first)
- [ ] Paper PDF upload validation
- [ ] Copyright PDF upload validation
- [ ] File size validation (20MB)
- [ ] MIME type validation (PDF only)
- [ ] Non-PDF files rejected
- [ ] Success: redirects to dashboard
- [ ] Success message displays
- [ ] Author emails saved as JSON
- [ ] Guidelines sidebar shows

### My Papers (my_papers.php)
- [ ] Shows only current user's papers
- [ ] Table displays all columns
- [ ] Status badges work
- [ ] "View" button works
- [ ] Search functionality
- [ ] Empty state message

### Profile (profile.php)
- [ ] User info displays
- [ ] Edit profile form works
- [ ] Password change works
- [ ] Validation errors show

### Paper View (paper_view.php)
- [ ] Loads with paper ID
- [ ] Shows paper title and status
- [ ] Abstract card displays
- [ ] Paper PDF iframe loads
- [ ] Copyright PDF iframe loads
- [ ] Download buttons work
- [ ] Status badge correct color
- [ ] Published PDF section (if approved)
- [ ] Certificates section (if approved)
- [ ] Multiple certificates display
- [ ] Each certificate shows author name/email
- [ ] Certificate IDs visible
- [ ] Click certificate opens in new tab
- [ ] Only paper owner can access

## Admin Panel

### Admin Login
- [ ] Separate login for admin
- [ ] Admin credentials work
- [ ] Non-admin cannot access

### Admin Dashboard (admin/dashboard.php)
- [ ] Admin sidebar (red theme)
- [ ] Admin navbar
- [ ] Six stat cards display:
  - [ ] Total Papers
  - [ ] Pending
  - [ ] Under Review
  - [ ] Approved
  - [ ] Rejected
  - [ ] Total Authors
- [ ] Counts are accurate
- [ ] Recent submissions table
- [ ] Status distribution section
- [ ] Progress bars show percentages

### Manage Papers (admin/papers.php)
- [ ] All submitted papers visible
- [ ] Search by title works
- [ ] Table shows all columns
- [ ] ID, Title, Submitter, Status, Date
- [ ] Status badges color-coded
- [ ] "View" button works
- [ ] Empty state if no papers
- [ ] Paper count displayed

### View Paper (admin/view_paper.php)
- [ ] Sidebar and navbar present
- [ ] Paper title in header
- [ ] Status badge displays
- [ ] Abstract card shows
- [ ] Paper PDF iframe loads
- [ ] Copyright PDF iframe loads
- [ ] Published PDF section (if exists)
- [ ] All download buttons work
- [ ] Paper info sidebar:
  - [ ] Submitter name/email
  - [ ] Authors list
  - [ ] Submission date
  - [ ] Current status
- [ ] Admin Actions card:
  - [ ] Review/Change Status button
  - [ ] Upload Final PDF button (if approved)
  - [ ] Generate Certificates button (if approved)
  - [ ] Back to Papers button
- [ ] Certificates card (if generated):
  - [ ] Lists all certificates
  - [ ] Shows author names
  - [ ] Shows author emails
  - [ ] Shows certificate IDs
  - [ ] View buttons work

### Review Paper (admin/review.php)
- [ ] Current status displays
- [ ] Dropdown shows all statuses
- [ ] Can change to Pending
- [ ] Can change to Under Review
- [ ] Can change to Approved
- [ ] Can change to Rejected
- [ ] Optional notes field
- [ ] Success: redirects to view_paper
- [ ] Status updated in database

### Upload Final PDF (admin/upload_final_pdf.php)
- [ ] Only accessible for approved papers
- [ ] File upload field
- [ ] PDF validation
- [ ] Success: saves to uploads/published/
- [ ] Updates published_pdf field
- [ ] Redirects to view_paper

### Generate Certificates (admin/generate_certificate.php)
- [ ] Only for approved papers
- [ ] Generates one certificate per author
- [ ] Parses author_list (comma-separated)
- [ ] Matches with author_emails (JSON)
- [ ] Creates unique Certificate ID for each
- [ ] Generates QR code for each
- [ ] QR code saves to uploads/qrcodes/
- [ ] Creates PDF certificate
- [ ] Certificate includes:
  - [ ] Paper title
  - [ ] Individual author name
  - [ ] Author email (if provided)
  - [ ] Publication date
  - [ ] Certificate ID
  - [ ] QR code image
- [ ] Saves to uploads/certificates/
- [ ] Inserts to database (one row per author)
- [ ] Redirects to view_paper
- [ ] Multiple certificates visible

## Multi-Author Certificate System

### Submission with Multiple Authors
- [ ] Submit paper with 3 authors
- [ ] Author list: "John Doe, Jane Smith, Bob Johnson"
- [ ] Add 3 email addresses:
  - [ ] john@example.com
  - [ ] jane@example.com
  - [ ] bob@example.com
- [ ] Form submits successfully
- [ ] author_emails saved as JSON in database

### Certificate Generation
- [ ] Admin approves paper
- [ ] Clicks "Generate Certificates"
- [ ] System creates 3 certificates
- [ ] Each has unique Certificate ID
- [ ] Each has unique QR code
- [ ] Each certificate shows individual author
- [ ] Database has 3 certificate rows
- [ ] All 3 visible in admin view_paper.php
- [ ] All 3 visible in author paper_view.php

### Certificate Access
- [ ] Author can see all 3 certificates
- [ ] Each certificate link works
- [ ] Each shows correct author name
- [ ] Each shows correct email
- [ ] PDFs download correctly
- [ ] QR codes scan and link to certificate page

## Edge Cases

### Error Handling
- [ ] Invalid paper ID shows 404
- [ ] Invalid certificate ID shows 404
- [ ] Non-existent user redirects to login
- [ ] SQL errors handled gracefully
- [ ] File upload errors show messages

### Security
- [ ] Author cannot view other authors' papers
- [ ] Non-admin cannot access admin panel
- [ ] SQL injection prevented (prepared statements)
- [ ] XSS prevented (htmlspecialchars)
- [ ] File upload limited to PDF
- [ ] MIME type validation works
- [ ] Session hijacking prevented

### Database Integrity
- [ ] Foreign keys work correctly
- [ ] Cascade delete works (delete paper deletes certificates)
- [ ] Unique constraints enforced (email, certificate_id)
- [ ] Default values set correctly

### Backward Compatibility
- [ ] Old certificates (no author_name) still work
- [ ] Old certificate.php?id=X URLs work
- [ ] Papers without author_emails generate 1 certificate

## Performance

### Page Load Times
- [ ] Landing page loads < 2 seconds
- [ ] Dashboard loads < 2 seconds
- [ ] Admin panel loads < 2 seconds
- [ ] PDF iframes load reasonably fast

### File Operations
- [ ] PDF uploads complete successfully
- [ ] Large PDFs (up to 20MB) work
- [ ] Certificate generation completes
- [ ] QR code generation fast

## Browser Compatibility

### Desktop
- [ ] Chrome works
- [ ] Firefox works
- [ ] Edge works
- [ ] Safari works (if available)

### Mobile
- [ ] Responsive on iPhone
- [ ] Responsive on Android
- [ ] Touch interactions work
- [ ] Navbar collapses properly

## Final Checks

### Code Quality
- [ ] No PHP errors in logs
- [ ] No JavaScript console errors
- [ ] No CSS layout issues
- [ ] All links work
- [ ] All buttons functional

### Documentation
- [ ] README.md complete
- [ ] INSTALLATION.md accurate
- [ ] MULTI_AUTHOR_CERTIFICATE_GUIDE.md helpful
- [ ] Code comments present

### Deployment Readiness
- [ ] All files committed
- [ ] Database schema updated
- [ ] Configuration documented
- [ ] Security recommendations noted

---

## Test Results

**Date:** _______________

**Tester:** _______________

**Overall Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial

**Notes:**
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________

**Critical Issues Found:**
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________

**Recommendations:**
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________
