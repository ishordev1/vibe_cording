# 🚀 ADMIN DASHBOARD - COMPLETE FEATURE GUIDE

## 📋 Table of Contents
1. [Dashboard Overview](#dashboard-overview)
2. [Internships Management](#internships-management)
3. [Users Management](#users-management)
4. [Applications Management](#applications-management)
5. [Payments Verification](#payments-verification)
6. [Blogs Management](#blogs-management)
7. [Certificates Generation](#certificates-generation)
8. [Offer Letters Generation](#offer-letters-generation)
9. [Technical Details](#technical-details)

---

## Dashboard Overview

### What You'll See on Login
```
┌─────────────────────────────────────────────────────┐
│           DIGITAL TARAI - ADMIN DASHBOARD            │
├─────────────────────────────────────────────────────┤
│                                                      │
│  [Stat Cards]     [Stat Cards]    [Stat Cards]     │
│  Total Users      Internships     Applications      │
│     🟦 45            🟩 4              🟧 12        │
│                                                      │
│  [Stat Card]                                        │
│  Pending Payments  [Chart: App Status]              │
│    🟥 2            [Progress Bars]                  │
│                                                      │
│  [Quick Actions]          [Recent Applications]     │
│  - Manage Users           - Ram Kumar | Pending     │
│  - Manage Internships     - Priya Sharma | Approved │
│  - Review Applications    - Anil Singh | Pending    │
│  - Verify Payments        - More...                 │
│                                                      │
└─────────────────────────────────────────────────────┘
```

### Key Features
- ✅ **Real-time Statistics**: Updated based on database
- ✅ **Visual Charts**: Progress bars showing application status
- ✅ **Quick Links**: One-click access to all sections
- ✅ **Recent Activity**: Last 5 applications shown
- ✅ **Welcome Message**: Shows admin's name and current date

---

## Internships Management

### Section 1: View All Internships
**Path**: Click "Internships" in sidebar

Shows all available internship programs in a card grid:
```
┌─────────────────────────┐  ┌─────────────────────────┐
│  Frontend Development   │  │  Backend Development    │
│  ─────────────────────  │  │  ─────────────────────  │
│  Duration: 3 months     │  │  Duration: 3 months     │
│  Fees: Rs. 500          │  │  Fees: Rs. 500          │
│  Applicants: 5 🔵       │  │  Applicants: 3 🔵       │
│  [View Applicants] ────────→ Shows all who applied   │
│                         │  │  for this course        │
└─────────────────────────┘  └─────────────────────────┘

┌─────────────────────────┐  ┌─────────────────────────┐
│  Android Development    │  │  Full Stack Development │
│  ─────────────────────  │  │  ─────────────────────  │
│  Duration: 4 months     │  │  Duration: 4 months     │
│  Fees: Rs. 600          │  │  Fees: Rs. 700          │
│  Applicants: 2 🔵       │  │  Applicants: 1 🔵       │
│  [View Applicants]      │  │  [View Applicants]      │
└─────────────────────────┘  └─────────────────────────┘
```

### Section 2: View Course Applicants
**Action**: Click [View Applicants] on any course

Shows detailed information:
```
┌──────────────────────────────────────────────────────┐
│  FRONTEND DEVELOPMENT INTERNSHIP                     │
│  ──────────────────────────────────────────────────  │
│                                                      │
│  Duration: 3 months  | Fees: Rs. 500               │
│  Total Applicants: 5 | Pending: 2 | Approved: 3   │
│                                                      │
│  APPLICANTS:                                         │
│  ┌────────────────────────────────────────────────┐ │
│  │ Name      │ Email    │ Phone │ Status  │      │ │
│  ├────────────────────────────────────────────────┤ │
│  │ Ram Kumar │ ram@...  │ 9800  │ Pending │ View │ │
│  │ Priya S.  │ priya@.. │ 9801  │ Approv. │ View │ │
│  │ Anil Singh│ anil@... │ 9802  │ Pending │ View │ │
│  │ Neha K.   │ neha@... │ 9803  │ Approv. │ View │ │
│  │ Arjun M.  │ arjun@.. │ 9804  │ Approv. │ View │ │
│  └────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────┘
```

### Section 3: View Individual Applicant
**Action**: Click [View Details] on any applicant

Shows complete information:
```
┌────────────────────────────────────────────────┐
│ USER INFORMATION            │  QUICK ACTIONS   │
│ ──────────────────────────────────────────────│
│ Name: Ram Kumar             │ [Gen. Certificate]
│ Email: ram@example.com      │                  │
│ Phone: +977-9800000000      │ [Gen. Offer Ltr] │
│ User Type: Student          │                  │
│                             │ [Approve App]    │
│ Applied For: Frontend Dev.  │                  │
│ App Status: 🟧 Pending      │                  │
│ Applied Date: Dec 27, 2025  │                  │
│                             │                  │
└────────────────────────────────────────────────┘
```

---

## Users Management

### View All Students
**Path**: Click "Users" in sidebar

Shows complete student list:
```
┌────────────────────────────────────────────────────────┐
│  ALL STUDENTS                                          │
│  ─────────────────────────────────────────────────────│
│  Name    │ Email       │ Phone    │ Status │ Joined  │
│  ─────────────────────────────────────────────────────│
│  Ram K.  │ ram@...     │ 9800...  │ Active │ Dec 27  │
│  Priya S.│ priya@...   │ 9801...  │ Active │ Dec 26  │
│  Anil S. │ anil@...    │ 9802...  │ Active │ Dec 26  │
│  Neha K. │ neha@...    │ 9803...  │ Active │ Dec 25  │
│  Arjun M.│ arjun@...   │ 9804...  │ Inactive│ Dec 20 │
└────────────────────────────────────────────────────────┘
```

---

## Applications Management

### Filter Applications by Status
**Path**: Click "Applications" in sidebar

Features:
- **[All]** - Show all applications
- **[Pending]** - Shows only pending applications
- **[Approved]** - Shows only approved applications
- **[Rejected]** - Shows only rejected applications

### Take Action on Applications
```
For PENDING applications:
├─ [Approve] → Mark as approved immediately
└─ [Reject]  → Mark as rejected immediately

For REVIEWED applications:
└─ [Status: Reviewed] → No further action needed
```

Example Table:
```
┌──────────────────────────────────────────────────────────┐
│  PENDING APPLICATIONS                                    │
│  ─────────────────────────────────────────────────────  │
│  Student    │ Internship    │ Status  │ Applied  │ Act  │
│  ─────────────────────────────────────────────────────  │
│  Ram Kumar  │ Frontend Dev  │ Pending │ Dec 27   │[✓][✗]│
│  Priya S.   │ Backend Dev   │ Pending │ Dec 27   │[✓][✗]│
│  Neha K.    │ Android Dev   │ Pending │ Dec 26   │[✓][✗]│
└──────────────────────────────────────────────────────────┘
✓ = Approve  | ✗ = Reject
```

---

## Payments Verification

### View and Verify Payments
**Path**: Click "Payments" in sidebar

### Filter by Status:
- **Pending** - Awaiting verification
- **Verified** - Already verified
- **Rejected** - Marked as not valid

### Verification Process:
```
┌───────────────────────────────────────────────────────┐
│  PENDING PAYMENT                                      │
│  Student: Ram Kumar                                   │
│  Email: ram@example.com                               │
│  Amount: Rs. 500                                      │
│  Method: Online Transfer                              │
│  Date: Dec 27, 2025                                   │
│                                                       │
│  [View Screenshot] ──→ Shows uploaded payment proof   │
│  [Verify] ──────────→ Confirms payment, updates DB   │
│  [Reject] ──────────→ Marks payment as invalid       │
└───────────────────────────────────────────────────────┘
```

---

## Blogs Management

### View All Blog Posts
**Path**: Click "Blogs" in sidebar

Features available:
- ✅ **Create New Post** - Click [+ Create New Blog Post]
- ✅ **Edit Post** - Click [Edit] on any post
- ✅ **Delete Post** - Click [Delete] with confirmation

Display includes:
- Title and excerpt
- Author name
- Publication status (Published/Draft)
- View count
- Creation date

---

## Certificates Generation

### What is Included?
When you generate a certificate, it includes:

```
┌─────────────────────────────────────────────────┐
│                                                 │
│      🎓 DIGITAL TARAI 🎓                        │
│                                                 │
│   CERTIFICATE OF COMPLETION                    │
│   ─────────────────────────────                │
│                                                 │
│        This is to certify that                 │
│                                                 │
│    ━━━━ RAM KUMAR ━━━━                         │
│                                                 │
│   Has successfully completed the               │
│   internship program in                        │
│                                                 │
│  ⭐ FRONTEND DEVELOPMENT ⭐                     │
│                                                 │
│   With dedication and excellence               │
│                                                 │
│   [Official Seal]          [Signatures]        │
│                                                 │
│   Director          Date     Coordinator       │
│   ─────────────  27 Dec 2025  ─────────────   │
│                                                 │
└─────────────────────────────────────────────────┘
```

### How to Generate:
1. Go to **Certificates** section
2. See list of approved students
3. Click **[Generate]** button
4. Certificate opens in new tab
5. Print to PDF or save as HTML

---

## Offer Letters Generation

### What is Included?
Professional offer letter with:

```
┌─────────────────────────────────────────────────┐
│  Digital Tarai Letterhead                       │
│  ─────────────────────────────────────────────  │
│                                                 │
│  Date: 27 December, 2025                        │
│                                                 │
│  To: Ram Kumar                                  │
│      ram@example.com                            │
│      +977-9800000000                            │
│                                                 │
│  Dear Ram Kumar,                                │
│                                                 │
│  We are pleased to offer you a position as     │
│  an FRONTEND DEVELOPMENT INTERN at Digital     │
│  Tarai.                                         │
│                                                 │
│  Position: Frontend Development Intern          │
│  Duration: 3 months                             │
│  Fee: Rs. 500                                   │
│                                                 │
│  Benefits Include:                              │
│  ✓ Professional Training & Mentorship           │
│  ✓ Work on Live Projects                        │
│  ✓ Certificate of Completion                    │
│  ✓ Letter of Recommendation                     │
│  ✓ Skill Development                            │
│                                                 │
│  Best Regards,                                  │
│                                                 │
│  ─────────────────    ─────────────────        │
│  For Digital Tarai     [Candidate Sig.]         │
│  Director             Ram Kumar                 │
│                                                 │
└─────────────────────────────────────────────────┘
```

### How to Generate:
1. Go to **Offer Letters** section
2. See list of approved students
3. Click **[Generate & Download]**
4. Letter opens in new tab
5. Print to PDF or save as HTML

---

## Technical Details

### Database Tables Used:
- **users** - Student and admin information
- **internships** - Available internship programs
- **applications** - Student applications
- **payments** - Payment records with screenshots
- **blogs** - Blog posts
- **modules** - Course modules
- **student_module_progress** - Progress tracking

### File Locations:
```
/admin/
├── index.php                    # Main dashboard
└── sections/
    ├── dashboard.php            # Overview
    ├── users.php               # Student management
    ├── internships.php         # Course management
    ├── applications.php        # Application handling
    ├── payments.php            # Payment verification
    ├── blogs.php               # Blog management
    ├── certificates.php        # Certificate generation
    └── offer-letters.php       # Offer letter generation

Generated Documents:
├── /public/certificates/       # Generated certificates
└── /public/offer-letters/      # Generated offer letters
```

### Authentication:
- **Method**: Session-based
- **Required**: User must be logged in as admin
- **Check**: `isLoggedIn()` && `user_type === 'admin'`
- **Redirect**: Unauthorized users sent to login page

### Security Features:
- ✅ Session verification on every page
- ✅ Database query escaping
- ✅ Input validation
- ✅ Admin-only access checks
- ✅ Data encryption for passwords

---

## Quick Reference - Button Locations

| Feature | Section | Action |
|---------|---------|--------|
| View All Internships | Internships | Click internship card |
| See Applicants | Internships Detail | Auto-displays on click |
| View Student Details | Applicants List | Click "View Details" |
| Generate Certificate | Student Details | Click "Generate Certificate" |
| Generate Offer Letter | Student Details | Click "Generate Offer Letter" |
| Approve/Reject App | Student Details | Click buttons |
| Verify Payment | Payments | Click "Verify" |
| View Payment Proof | Payments | Click "View" |
| Create Blog | Blogs | Click "+ Create" |
| Edit Blog | Blogs | Click "Edit" |
| Delete Blog | Blogs | Click "Delete" |

---

## Common Tasks

### Task 1: Approve a Student Application
```
1. Internships → Select Course
2. Click [View Details] on student
3. Click [Approve Application]
4. Done! Student is now approved
```

### Task 2: Generate Certificate
```
1. Internships → Select Course → Student Details
2. Click [Generate Certificate]
3. New tab opens with certificate
4. Right-click → Save/Print as PDF
```

### Task 3: Verify Payment
```
1. Payments → Filter "Pending"
2. Click [View] to see screenshot
3. Verify amount and details
4. Click [Verify] to approve
```

### Task 4: Create Blog Post
```
1. Blogs → Click [+ Create New Blog Post]
2. Fill title and content
3. Set status: Published/Draft
4. Click Save
```

---

## System Requirements

- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+
- **Server**: Apache with mod_rewrite
- **Browser**: Modern browser (Chrome, Firefox, Edge, Safari)
- **JavaScript**: Enabled

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Can't access admin | Check if logged in as admin account |
| Certificate not generating | Ensure student is approved first |
| Payment verification fails | Check payment screenshot uploaded |
| Sidebar not showing | Clear browser cache, refresh page |
| Documents not downloading | Use modern browser, check pop-up blocker |

---

**Version**: 1.0  
**Status**: Production Ready  
**Last Updated**: December 27, 2025  
**Support**: For issues, contact admin@digitaltarai.com
