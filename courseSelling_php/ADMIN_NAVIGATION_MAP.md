# Admin Dashboard - Visual Navigation Map

## MAIN DASHBOARD STRUCTURE

```
┌─────────────────────────────────────────────────────────────────────┐
│                        ADMIN DASHBOARD                              │
├──────────────────────────────────────────────────────────────────── │
│                                                                      │
│  ┌────────────────────┐  ┌──────────────────────────────────────┐ │
│  │   SIDEBAR          │  │  MAIN CONTENT AREA                   │ │
│  │ ┌────────────────┐ │  │                                      │ │
│  │ │ 🎓 Admin Panel │ │  │  [Statistics Cards in Grid]         │ │
│  │ └────────────────┘ │  │  - Total Users                      │ │
│  │                    │  │  - Total Internships                │ │
│  │ 📊 Dashboard    ◄──┼──┼─ - Total Applications               │ │
│  │ 👥 Users       ◄──┼──┼─ - Pending Payments                │ │
│  │ 📚 Internships ◄──┼──┼─ [Status Chart]                     │ │
│  │ 📄 Applications◄──┼──┼─ [Quick Actions]                    │ │
│  │ 💳 Payments    ◄──┼──┼─ [Recent Applications Table]        │ │
│  │ 📝 Blogs       ◄──┼──┼─                                    │ │
│  │ 🎓 Certificates◄──┼──┼─                                    │ │
│  │ 📋 Offer Letters   │  │                                      │ │
│  │                    │  │                                      │ │
│  │ 🚪 Logout         │  │                                      │ │
│  │                    │  │                                      │ │
│  └────────────────────┘  └──────────────────────────────────────┘ │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

## INTERNSHIPS SECTION - DRILL DOWN FLOW

```
Internships List View
├─ [Frontend Development]          [Total Applicants: 5]
│  ├─ [View Applicants] ──────────────┐
│  │                                   │
│  └─ [Backend Development]           │
│     [Total Applicants: 3]           ▼
│     [View Applicants]          Internship Detail
│                                ├─ Course Info
│                                ├─ Statistics
│                                └─ Applicants Table
│                                   ├─ Ram Kumar [Pending]
│                                   │  └─ [View Details] ────┐
│                                   ├─ Priya Sharma [Pending] │
│                                   │  └─ [View Details]     │
│                                   └─ Anil Singh [Approved] │
│                                      └─ [View Details]     │
│                                                             ▼
│                                      User Application Details
│                                      ├─ User Info
│                                      │  - Name, Email, Phone
│                                      │  - Applied for: Frontend Dev
│                                      │  - Applied Date: Dec 27, 2025
│                                      │
│                                      └─ Quick Actions Panel
│                                         ├─ [Generate Certificate]
│                                         ├─ [Generate Offer Letter]
│                                         └─ [Approve Application]
```

## APPLICATIONS SECTION - STATUS FILTER

```
Applications Management

[Filter Tabs]
├─ All Applications
├─ Pending Applications ◄────── Shows only pending
│  ├─ Ram Kumar | Frontend Dev | [Approve] [Reject]
│  ├─ Priya Sharma | Backend | [Approve] [Reject]
│  └─ Neha Kumari | Android | [Approve] [Reject]
│
├─ Approved Applications ◄────── Shows only approved
│  └─ Anil Singh | Full Stack | [Status: Reviewed]
│
└─ Rejected Applications
   └─ (none)
```

## PAYMENTS SECTION - VERIFICATION FLOW

```
Payments Management

[Filter by Status]
├─ Pending (5)
│  ├─ Ram Kumar | Frontend Dev | Rs. 500 | [Verify] [Reject] [View Screenshot]
│  ├─ Priya Sharma | Backend | Rs. 500 | [Verify] [Reject] [View Screenshot]
│  └─ ...more pending payments
│
├─ Verified (8)
│  ├─ Anil Singh | Android | Rs. 600 | [Already Verified]
│  └─ ...more verified payments
│
└─ Rejected (2)
   ├─ User 1 | Program | Rs. ... | [Status: Rejected]
   └─ ...more
```

## CERTIFICATES SECTION

```
Certificates Generation

[Approved Students List]
├─ Ram Kumar | Frontend Development Internship | [Generate]
│  └─ Generates professional certificate with:
│     - Official Digital Tarai header
│     - Student name (large, bold)
│     - Internship program name
│     - Seal and signatures
│     - Date issued
│
├─ Priya Sharma | Backend Development | [Generate]
└─ ...more approved students
```

## OFFER LETTERS SECTION

```
Offer Letters Generation

[Approved Students List]
├─ Ram Kumar | Frontend Development Internship | [Generate & Download]
│  └─ Generates professional offer letter with:
│     - Company letterhead
│     - Offer date
│     - Student address
│     - Position details
│     - Internship duration
│     - Program fee
│     - Key benefits
│     - Signature sections
│
├─ Priya Sharma | Backend Development | [Generate & Download]
└─ ...more approved students
```

## COLOR CODING SYSTEM

```
Sidebar Active:     Purple (#8e44ad)
Sidebar Inactive:   Gray (#374151)
Status Badge:
  ├─ Active/Approved/Verified:   Green
  ├─ Pending:                     Orange
  ├─ Inactive/Rejected:           Red
  └─ Draft:                       Gray

Cards:
  ├─ Users:                       Blue
  ├─ Internships:                 Green
  ├─ Applications:                Orange
  └─ Payments:                    Red
```

## DATA FLOW

```
Database
├─ users (4 students + 1 admin)
├─ internships (4 programs)
├─ applications (multiple)
├─ payments (with screenshots)
└─ blogs (published posts)
   │
   ├─ Displayed in Admin Dashboard
   │
   ├─ Updated via Admin Actions
   │  ├─ Approve/Reject Applications
   │  ├─ Verify/Reject Payments
   │  ├─ Generate Certificates
   │  ├─ Generate Offer Letters
   │  └─ Manage Blogs
   │
   └─ Final Documents Generated
      ├─ Certificate HTML files → /public/certificates/
      └─ Offer Letter HTML files → /public/offer-letters/
```

## KEY STATISTICS DISPLAYED

Dashboard Overview:
- Total Users: Students registered
- Total Internships: Available programs
- Total Applications: Submitted applications
- Applications Approved: Count of approved
- Applications Pending: Count of pending
- Applications Rejected: Count of rejected
- Total Blogs: Published articles
- Pending Payments: Awaiting verification

## RESPONSIVE DESIGN

```
Desktop (>1024px)
├─ Sidebar: Fixed left (256px)
├─ Content: Full width with left margin
└─ Grid: Multi-column (2-4 columns)

Tablet (768px-1024px)
├─ Sidebar: Fixed left (narrower)
├─ Content: Responsive width
└─ Grid: 2-column layout

Mobile (<768px)
├─ Sidebar: Collapsible/Hidden
├─ Content: Full width
└─ Grid: Single column
    Tables: Horizontal scroll
```

## ACTION WORKFLOWS

### Workflow 1: Approve Student & Generate Certificate

```
1. Admin logs in
   ↓
2. Navigates to Internships
   ↓
3. Clicks on specific internship
   ↓
4. Sees all applicants for that course
   ↓
5. Clicks "View Details" on student
   ↓
6. See student's full information
   ↓
7. Clicks "Approve Application"
   ↓
8. Clicks "Generate Certificate"
   ↓
9. Professional certificate opens in new tab
   ↓
10. Admin can print/download as PDF
```

### Workflow 2: Verify Payment

```
1. Admin logs in
   ↓
2. Navigates to Payments
   ↓
3. Filters by "Pending" status
   ↓
4. Sees pending payment with screenshot
   ↓
5. Clicks "View" to see payment proof
   ↓
6. Verifies amount matches
   ↓
7. Clicks "Verify" to approve payment
   ↓
8. Application status updates
   ↓
9. Student now eligible for certificate
```

### Workflow 3: Generate Offer Letter

```
1. Admin navigates to Offer Letters
   ↓
2. Sees list of approved students
   ↓
3. Clicks "Generate & Download" button
   ↓
4. Professional offer letter generates
   ↓
5. Opens in new tab (HTML format)
   ↓
6. Admin can print to PDF or save as HTML
   ↓
7. Student receives offer document
```

## SESSION MANAGEMENT

```
User visits /admin/
   ↓
Check session: isLoggedIn() && admin check
   ↓
No Session → Redirect to login
   ↓
Session Valid → Load dashboard
   ↓
Display: User name, Current date/time
   ↓
Click Logout → Destroy session, redirect to login
```

---

This comprehensive admin dashboard provides all the functionality needed to manage internship programs, student applications, and generate professional documents.
