# Certificate Workflow - Quick Testing Guide

## Pre-Test Requirements

✅ Database: certificates table has `requested_at` column
✅ Student side: modules.php updated with certificate request logic
✅ Admin side: certificates.php updated with 3-state detection
✅ Student must have an "approved" application for the internship

## Step-by-Step Testing

### Phase 1: Student Requests Certificate

1. **Log in as Student**
   - URL: `http://localhost/ai/DigitalTarai/student/`
   - Navigate to Dashboard

2. **Select Active Course**
   - Click "Your Courses" section
   - Click "View Modules" for an approved internship
   - URL should be: `/student/modules.php?internship_id=<ID>`

3. **Complete All Modules**
   - Check all module checkboxes
   - Verify progress bar reaches 100%
   - Verify "Request Certificate" button becomes enabled (turns from gray to yellow)

4. **Request Certificate**
   - Click "Request Certificate" button
   - Should see green success message
   - Yellow form disappears
   - Wait for page to reload

5. **Verify Student Side**
   - Check page shows "Ready for Your Certificate?" section still visible
   - But should show "Certificate request sent successfully!" message
   - If you refresh, message may be gone (SESSION unset is correct behavior)

---

### Phase 2: Admin Generates Certificate

1. **Log in as Admin**
   - URL: `http://localhost/ai/DigitalTarai/admin/`
   - User must have admin role

2. **Navigate to Certificates**
   - Click "Certificates" in admin menu
   - Look for the student's name in the table

3. **Verify Request Appears**
   - **Status**: Should show "Requested" in orange badge
   - **Requested Date**: Should show the date student clicked request
   - **Generated Date**: Should show "-" (not generated yet)
   - **Action Button**: Should show purple "Generate" button (not blue "View")

4. **Generate Certificate**
   - Click "Generate" button for the student
   - Page should redirect back to certificates section
   - Should see green success message: "Certificate generated successfully"

5. **Verify Generation Success**
   - Table should update automatically
   - **Status**: Now shows "Generated" in green badge
   - **Generated Date**: Shows today's date
   - **Action Buttons**: Now shows blue "View" and orange "Regenerate"
   - Check that file was created: `/public/certificates/certificate_<id>_<timestamp>.html`

---

### Phase 3: Student Sees Generated Certificate

1. **Log in as Student (same account from Phase 1)**
   - Return to dashboard

2. **Open Course Modules Again**
   - Click same internship's "View Modules"
   - URL: `/student/modules.php?internship_id=<ID>`

3. **Verify Certificate Display**
   - **Old Section**: Yellow "Ready for Your Certificate?" should be GONE
   - **New Section**: Green "🎉 Certificate Generated!" should appear
   - **Generated Date**: Shows the date admin generated
   - **Buttons**: "Download Certificate" and "View Certificate" visible

4. **Download/View Certificate**
   - Click either button
   - Should open new tab with HTML certificate
   - Should see:
     - Student name
     - Internship title
     - Skills section
     - QR code
     - Director/Coordinator signatures
     - Reference number (8 digits from cert_id)
     - Issue date

---

### Phase 4: Admin Regenerates Certificate (Optional)

1. **Return to Admin Certificates**
   - As admin, go to Certificates section

2. **Regenerate Certificate**
   - Click "Regenerate" button (orange)
   - New file created with new timestamp
   - Old file deleted from server
   - Database updated with new filename and generated_at

3. **Verify Update**
   - Generated date changes to new timestamp
   - File path updates to new filename
   - Student will see same certificate but from new file on refresh

---

## Expected Database Changes

### After Student Requests:
```sql
SELECT * FROM certificates WHERE user_id = <student_id>;

-- Expected:
id          | 5
application_id | 10
user_id     | 2
internship_id | 3
requested_at   | 2024-01-15 14:30:45
filename       | pending
file_path      | pending
generated_by   | NULL
generated_at   | NULL
created_at     | 2024-01-15 14:30:45
```

### After Admin Generates:
```sql
SELECT * FROM certificates WHERE user_id = <student_id>;

-- Expected:
id          | 5
application_id | 10
user_id     | 2
internship_id | 3
requested_at   | 2024-01-15 14:30:45
filename       | certificate_5_1705340445.html
file_path      | public/certificates/certificate_5_1705340445.html
generated_by   | 1 (admin user id)
generated_at   | 2024-01-15 14:45:20
created_at     | 2024-01-15 14:30:45
```

---

## Common Issues & Solutions

### Issue 1: Student's "Request Certificate" button stays disabled
**Solution:** 
- Verify all modules are checked ✓
- Refresh page to recalculate progress
- Check that progress bar shows 100%

### Issue 2: Student doesn't see "Request" form at all
**Possible Causes:**
- Application status is not "approved" (check admin panel)
- Student already has a generated certificate for this internship
- Page not loading certificate check properly

**Solution:**
- Use browser DevTools > Console to check for JS errors
- Verify application status in admin panel
- Refresh page (Ctrl+F5)

### Issue 3: Admin doesn't see "Requested" status, only "Not Requested"
**Solution:**
- Student must click "Request Certificate" first
- Admin must refresh page to see update
- Check database to verify `requested_at` was set

### Issue 4: Admin "Generate" button doesn't work
**Solution:**
- Check browser console for JavaScript errors
- Verify `/public/certificates/` directory exists and is writable
- Verify admin user is logged in with proper permissions

### Issue 5: Certificate file doesn't open after generation
**Solution:**
- Check that file was created: `/public/certificates/certificate_<id>_<timestamp>.html`
- Verify file_path in database is correct (relative path, not absolute)
- Try direct URL: `http://localhost/ai/DigitalTarai/public/certificates/certificate_<id>_<timestamp>.html`

### Issue 6: Student still sees yellow "Request" form after admin generates
**Solution:**
- Student must refresh page (Ctrl+F5)
- Check that certificate filename is NOT 'pending' in database
- Verify query filters by `filename != 'pending'`

---

## Quick Database Queries for Testing

### Check certificate status:
```sql
SELECT c.*, u.name, i.title 
FROM certificates c 
JOIN users u ON c.user_id = u.id 
JOIN internships i ON c.internship_id = i.id 
ORDER BY c.id DESC;
```

### Check all applications and certificate status:
```sql
SELECT a.id, u.name, i.title, a.status, 
       CASE 
           WHEN c.filename IS NOT NULL AND c.filename != 'pending' THEN 'Generated'
           WHEN c.id IS NOT NULL THEN 'Requested'
           ELSE 'Not Requested'
       END as cert_status,
       c.requested_at, c.generated_at
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id
LEFT JOIN certificates c ON a.id = c.application_id
WHERE a.status = 'approved'
ORDER BY a.id DESC;
```

### Check specific student's certificates:
```sql
SELECT c.*, i.title 
FROM certificates c 
JOIN internships i ON c.internship_id = i.id 
WHERE c.user_id = <student_id>;
```

---

## Code Locations for Reference

### Student Certificate Request
**File:** `/student/modules.php` Lines 31-55
**What it does:** Handles POST request when student clicks "Request Certificate"

### Student Certificate Display
**File:** `/student/modules.php` Lines 260-310  
**What it does:** Shows either green success OR yellow request form based on certificate status

### Admin Certificate Generation
**File:** `/admin/sections/certificates.php` Lines 6-45
**What it does:** Creates HTML certificate, saves file, updates database

### Admin Certificate Query
**File:** `/admin/sections/certificates.php` Lines 315-330
**What it does:** Fetches all approved applications with 3-state certificate status

### Admin Certificate Table Display
**File:** `/admin/sections/certificates.php` Lines 330-385
**What it does:** Renders table with proper buttons/statuses for each state

