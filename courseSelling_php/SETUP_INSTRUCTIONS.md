# Certificates & Offer Letters Setup Instructions

## Step 1: Create Database Tables

You have two options:

### Option A: Using the Setup Script (Easiest)
1. Open your browser and go to:
   ```
   http://localhost/ai/DigitalTarai/setup_tables.php
   ```
2. The script will automatically create the `certificates` and `offer_letters` tables
3. You should see confirmation messages

### Option B: Using phpMyAdmin
1. Open phpMyAdmin
2. Select the `digital_tarai` database
3. Go to the SQL tab
4. Copy and paste this code:

```sql
CREATE TABLE IF NOT EXISTS certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    generated_by INT,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS offer_letters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    generated_by INT,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

5. Click "Execute"

## Step 2: Features Now Working

### In Certificates Section (`admin/index.php?section=certificates`)

✅ **Column 1: Student Name** - Name of the student
✅ **Column 2: Internship Program** - Name of the internship
✅ **Column 3: Approved Date** - When the application was approved
✅ **Column 4: Status** - Shows either:
  - `Pending` (orange badge) - Certificate not yet generated
  - `Generated` (green badge) - Certificate has been generated

✅ **Column 5: Generated Date** - When the certificate was generated (shows `-` if pending)

✅ **Column 6: Action** - Shows either:
  - `Generate` button - Click to generate the certificate (for pending)
  - `View` button - Click to view/download the certificate (for generated)

### In Offer Letters Section (`admin/index.php?section=offer-letters`)

✅ Same structure as Certificates section
✅ Tracks which offer letters have been generated
✅ Shows generation status and date
✅ Direct links to view/download generated documents

## Step 3: How to Use

### Generating a Certificate:

1. Go to Admin Dashboard → Certificates
2. Find the student with "Pending" status
3. Click the "Generate" button
4. Certificate will be:
   - Generated as an HTML file
   - Saved to `/public/certificates/`
   - Record saved to `certificates` table with:
     - Filename
     - File path
     - Generated date/time
     - Which admin generated it
5. Page automatically shows status as "Generated"
6. Click "View" button to open/download the certificate

### Generating an Offer Letter:

Same process as certificates but for offer letters section

## Step 4: Verify in Database

### Check Certificates Table:
```sql
SELECT * FROM certificates;
```

### Check Offer Letters Table:
```sql
SELECT * FROM offer_letters;
```

You should see records with:
- application_id
- user_id
- internship_id
- filename (e.g., certificate_5_1_1766816356.html)
- file_path (e.g., public/certificates/certificate_5_1_1766816356.html)
- generated_by (admin user ID)
- generated_at (timestamp)

## Step 5: Frontend Display (Optional Next Feature)

Once tables are set up, you can add frontend features to show:
- Student dashboard displaying their generated certificates
- Downloadable certificates from student portal
- History of all issued certificates

## Troubleshooting

**Problem: Button shows but nothing happens**
- Solution: Make sure tables are created using setup_tables.php

**Problem: Certificate generated but not showing in list**
- Solution: Refresh the page (F5) or check the database to confirm record was saved

**Problem: File exists but can't open**
- Solution: Check `/public/certificates/` folder has correct permissions (755)

**Problem: "Cannot modify header" error still appears**
- Solution: Make sure output buffering is enabled (ob_start/ob_end_clean are in place)

## Files Updated

✅ `/admin/sections/certificates.php` - Added status tracking and improved UI
✅ `/admin/sections/offer-letters.php` - Added status tracking and improved UI
✅ `/database.sql` - Updated schema with new tables
✅ `/setup_tables.php` - Auto-setup script
✅ `/CERTIFICATES_OFFER_LETTERS_SETUP.md` - Detailed documentation

