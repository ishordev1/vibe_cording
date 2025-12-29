<?php
/**
 * ADMIN DASHBOARD SETUP GUIDE
 * 
 * This file explains how the admin dashboard works and what you need to know
 */

echo "
======================================================================
ADMIN DASHBOARD - QUICK START GUIDE
======================================================================

1. ACCESS THE ADMIN DASHBOARD
   URL: http://localhost/ai/DigitalTarai/admin/
   
2. LOGIN CREDENTIALS
   Email: admin@digitaltarai.com
   Password: admin123
   
3. MAIN SECTIONS

   A. DASHBOARD
      - Overview of all statistics
      - Quick access to all modules
      - Recent applications
      
   B. USERS
      - View all registered students
      - See status (active/inactive)
      - Join dates
      
   C. INTERNSHIPS
      - Click on any internship to see applicants
      - View detailed applicant information
      - Total applicants count
      
   D. APPLICATIONS
      - Filter by status (All, Pending, Approved, Rejected)
      - Approve or reject applications
      
   E. PAYMENTS
      - Filter pending, verified, rejected payments
      - View payment screenshots
      - Mark payments as verified
      
   F. BLOGS
      - Create, edit, delete blog posts
      - Manage publications
      
   G. CERTIFICATES
      - Generate professional certificates
      - For approved applicants only
      - Download as HTML/PDF
      
   H. OFFER LETTERS
      - Generate formal offer letters
      - For approved applicants only
      - Download as HTML/PDF

4. WORKFLOW EXAMPLE: APPROVE A STUDENT AND GENERATE DOCUMENTS

   Step 1: Go to 'Internships' section
   Step 2: Click on the internship name (e.g., 'Frontend Development')
   Step 3: See all applicants for that internship
   Step 4: Click 'View Details' on the student you want to approve
   Step 5: Click 'Approve Application' button
   Step 6: Now you can:
           - Click 'Generate Certificate' to create their certificate
           - Click 'Generate Offer Letter' to create their offer letter
   Step 7: Documents will open in new tab, ready to print/download

5. IMPORTANT NOTES

   ✓ Only approved applicants can receive certificates
   ✓ Generate documents only for approved applications
   ✓ Payment verification updates application status
   ✓ All actions are logged in the system
   ✓ Admin name appears in top right of dashboard
   
6. STATISTICS ON DASHBOARD

   Cards show:
   - Total Users: All registered students
   - Total Internships: All available programs
   - Total Applications: All submitted applications
   - Pending Payments: Waiting for verification

7. FEATURES

   ✓ Responsive design (works on phones, tablets, desktops)
   ✓ Color-coded status indicators
   ✓ Professional documents (certificates & letters)
   ✓ Secure authentication
   ✓ Database integration
   
======================================================================
";
?>
