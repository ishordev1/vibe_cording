# Certificate Management System - Complete Implementation

## Overview
Fixed the certificate request/generation workflow to properly distinguish between three states:
1. **Not Requested** - No certificate record exists
2. **Requested** - Student has requested, admin hasn't generated yet (filename = 'pending')
3. **Generated** - Admin has generated the certificate (filename = actual filename)

---

## Changes Made

### 1. Database Schema (✅ COMPLETED)
**File:** `ALTER TABLE certificates...`
**Change:** Added `requested_at` column to track when students request certificates

```sql
ALTER TABLE certificates ADD COLUMN requested_at DATETIME NULL AFTER internship_id;
```

**Current Table Structure:**
- id (PRIMARY KEY)
- application_id (FK to applications)
- user_id (FK to users)
- internship_id (FK to internships)
- **requested_at** (NEW) - Timestamp when student requests
- filename - 'pending' while waiting for generation, actual filename when generated
- file_path - Path to certificate file
- generated_by (FK to users) - Who generated the certificate
- generated_at - When certificate was actually generated
- created_at - Record creation timestamp

---

### 2. Student Side (modules.php) - ✅ COMPLETED

**Location:** `c:\xampp\htdocs\ai\DigitalTarai\student\modules.php`

#### Certificate Check Query (Lines 25-30)
```php
// Only shows generated certificates (filename != 'pending')
$cert_query = $conn->query("SELECT c.id, c.filename, c.file_path, c.generated_at FROM certificates c 
                           JOIN applications a ON c.application_id = a.id 
                           WHERE a.user_id={$_SESSION['user_id']} AND c.internship_id=$internship_id 
                           AND c.filename != 'pending' LIMIT 1");
$certificate = $cert_query && $cert_query->num_rows > 0 ? $cert_query->fetch_assoc() : null;
```

#### Certificate Request Handler (Lines 31-55)
When student completes all modules and clicks "Request Certificate":
```php
$requested_at = date('Y-m-d H:i:s');
$conn->query("INSERT INTO certificates (application_id, user_id, internship_id, requested_at, filename, file_path) 
             VALUES ($app_id, {$_SESSION['user_id']}, $internship_id, '$requested_at', 'pending', 'pending')");
```

#### Certificate Display Section (Lines 260-310)
**If Generated (filename != 'pending'):**
- Shows green success box
- Displays generated date
- Provides Download and View buttons
- Links: `<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($certificate['file_path']); ?>`

**If Not Generated:**
- Shows yellow request form
- Shows completion status (X/Y modules completed)
- Request button enabled only when all modules completed

---

### 3. Admin Side (admin/sections/certificates.php) - ✅ COMPLETED

**Location:** `c:\xampp\htdocs\ai\DigitalTarai\admin\sections\certificates.php`

#### Certificate Generation Handler (Lines 6-45)
When admin clicks "Generate":
1. Creates HTML certificate file
2. Saves to `/public/certificates/certificate_<id>_<timestamp>.html`
3. Updates database with actual filename and timestamps

```php
// After creating certificate file:
$file_path = 'public/certificates/' . $filename;
$generated_at = date('Y-m-d H:i:s');
$conn->query("UPDATE certificates 
             SET filename='$filename', 
                 file_path='$file_path', 
                 generated_by={$_SESSION['user_id']}, 
                 generated_at='$generated_at' 
             WHERE id=$cert_id");

// Redirect back to page with success message
header("Location: ?section=certificates&success=Certificate generated successfully");
```

#### Certificate Query (Lines 315-330)
**Three-way CASE statement to determine status:**

```php
$approved_apps = $conn->query("
    SELECT a.id, a.user_id, a.internship_id, u.name, i.title, a.application_date,
           CASE 
               WHEN c.filename IS NOT NULL AND c.filename != 'pending' THEN 'generated'
               WHEN c.id IS NOT NULL THEN 'requested'
               ELSE 'not_requested'
           END as cert_status,
           c.id as cert_id, c.generated_at, c.filename, c.requested_at, c.file_path
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    LEFT JOIN certificates c ON a.id = c.application_id
    WHERE a.status = 'approved'
    ORDER BY a.application_date DESC
");
```

#### Table Display Logic (Lines 330-385)
Three-state display based on cert_status:

**State 1: not_requested**
- Status Badge: Gray "Not Requested"
- Requested Date: "-"
- Generated Date: "-"
- Action: "Generate" button

**State 2: requested**
- Status Badge: Orange "Requested"
- Requested Date: Shows actual request date
- Generated Date: "-"
- Action: "Generate" button (ready to generate)

**State 3: generated**
- Status Badge: Green "Generated"
- Requested Date: Shows request date
- Generated Date: Shows actual generation date
- Action: "View" + "Regenerate" buttons
- View links to: `<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($app['file_path']); ?>`

---

## Complete Workflow

### Student Journey:
1. **Completes All Modules**
   - Checks all module checkboxes
   - Progress bar reaches 100%
   - "Request Certificate" button becomes enabled

2. **Requests Certificate**
   - Clicks "Request Certificate"
   - Record inserted: `certificates(filename='pending', requested_at=NOW())`
   - Shows success message: "Certificate request sent successfully!"
   - Form disappears, shows "Ready for Your Certificate?" section

3. **After Admin Generates**
   - Student refreshes modules page
   - Query finds certificate with `filename != 'pending'`
   - Shows green success box with generated date
   - Download and View buttons link to actual certificate file

### Admin Journey:
1. **Views Certificate Requests**
   - Navigates to Admin > Certificates section
   - Sees table with all approved applications
   - Filters by cert_status:
     - "Not Requested" (gray) - Student hasn't requested yet
     - "Requested" (orange) - Student has requested, waiting for generation
     - "Generated" (green) - Certificate already created

2. **Generates Certificate**
   - Clicks "Generate" button for a requested certificate
   - System creates HTML certificate with:
     - Student name and internship title
     - QR code for verification
     - Director and coordinator signatures
     - Reference number (cert_id padded to 8 digits)
   - File saved to: `/public/certificates/certificate_<cert_id>_<timestamp>.html`
   - Database updated with:
     - `filename` = actual filename (no longer 'pending')
     - `file_path` = path relative to SITE_URL
     - `generated_by` = current admin user_id
     - `generated_at` = current timestamp

3. **Views Generated Certificates**
   - Status changes to green "Generated"
   - Shows generation date
   - "View" button links to certificate file
   - "Regenerate" button available for recreation

---

## Key Features

### ✅ Three-State Tracking
- **Not Requested**: No database record
- **Requested**: Record exists with filename='pending'
- **Generated**: Record exists with actual filename

### ✅ Timestamp Tracking
- **requested_at**: Set when student clicks request (in modules.php)
- **generated_at**: Set when admin generates (in admin certificates.php)
- **created_at**: Set when record first created (auto)

### ✅ File Management
- Files stored in `/public/certificates/` directory
- Unique naming: `certificate_<cert_id>_<timestamp>.html`
- Paths stored relative to SITE_URL for easy linking
- Previous certificates deleted on regenerate

### ✅ User Experience
**Student Side:**
- Clear progress tracking
- Automatic button enable/disable based on completion
- Success confirmation when requesting
- Green success display when certificate ready
- One-click download/view

**Admin Side:**
- Visual status indicators (gray/orange/green)
- Request dates and generation dates displayed
- Single "Generate" button for requests
- Regenerate option for existing certificates
- Redirect back to page (stays on admin section)

---

## Testing Checklist

- [ ] Student completes all modules
- [ ] Student clicks "Request Certificate" 
  - [ ] Success message appears
  - [ ] Form disappears
- [ ] Check admin certificates section
  - [ ] Row shows "Requested" status (orange)
  - [ ] "Requested" column shows date
  - [ ] "Generate" button visible
- [ ] Admin clicks "Generate"
  - [ ] File created at `/public/certificates/certificate_<id>_<timestamp>.html`
  - [ ] Database updated (filename != 'pending')
  - [ ] generated_by set to admin user_id
  - [ ] generated_at set to current timestamp
  - [ ] Stays on certificates page (redirect works)
  - [ ] Success message displays
- [ ] Admin section shows update
  - [ ] Status changes to "Generated" (green)
  - [ ] "Generated Date" column shows date
  - [ ] "View" button appears
- [ ] Student page updates
  - [ ] Refresh modules page
  - [ ] Green success section displays
  - [ ] Shows generated date
  - [ ] Download/View buttons work
  - [ ] Links open certificate file correctly

---

## Files Modified

1. **Database**: Added `requested_at` column to certificates table
2. **student/modules.php**: Certificate request handler, query, and display logic
3. **admin/sections/certificates.php**: Generation logic, query, and table display

## File Locations
- Student modules page: `/student/modules.php`
- Admin certificates: `/admin/sections/certificates.php`
- Generated certificates: `/public/certificates/`
- Configuration: `/config/config.php` (defines SITE_URL)

