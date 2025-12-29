# Multi-Author Certificate System - Implementation Guide

## Overview
The research paper submission system now supports **individual certificates for each author** with personalized information.

## Key Features

### 1. Author Email Collection
- When submitting a paper, authors can now add multiple email addresses
- Each author will receive their own individual certificate
- Dynamic "Add Author" button to add as many authors as needed
- First author field is required (cannot be removed)

### 2. Database Changes
**Papers Table:**
- Added `author_emails` field (TEXT, JSON format) to store array of author emails

**Certificates Table:**
- Added `author_name` field (VARCHAR 200) - Specific author's name for this certificate
- Added `author_email` field (VARCHAR 200) - Specific author's email for this certificate
- Now supports multiple certificates per paper (one per author)

### 3. Certificate Generation Process

**Admin Flow:**
1. Admin approves a paper
2. Admin clicks "Generate Certificates" button
3. System creates **one certificate per author**:
   - Parses author names from `author_list` field (comma-separated)
   - Matches with emails from `author_emails` JSON array
   - Generates unique Certificate ID for each
   - Creates individual QR code for each certificate
   - Generates personalized PDF certificate

**Certificate Information:**
- Paper title
- **Individual author name** (not all authors)
- Author email (if provided)
- Unique Certificate ID (format: CERT-XXXXXXXXXXXX)
- Publication date
- QR code linking to certificate verification page

### 4. Certificate Viewing

**Author Dashboard:**
- `paper_view.php` now displays all certificates as a list
- Each certificate shows:
  - Author name
  - Author email
  - Certificate ID
  - Download link

**Public Certificate Page:**
- URL format: `certificate.php?cert_id=CERT-XXXXXXXXXXXX`
- Displays individual author information
- Shows certificate PDF in iframe
- Download button for PDF
- Backwards compatible with old `?id=` parameter

### 5. Updated Files

**Modified Files:**
- `sql/schema.sql` - Database schema with new fields
- `submit_paper.php` - Dynamic author email input fields with JavaScript
- `admin/generate_certificate.php` - Multi-author certificate generation
- `paper_view.php` - Display all certificates in list format
- `certificate.php` - Support cert_id lookup with professional design
- `admin/papers.php` - Added navbar, professional table design
- `admin/view_paper.php` - Complete redesign with sidebar, navbar, certificates list

## How to Use

### For Authors:

1. **Submit a Paper:**
   - Fill in paper title, abstract, author list (comma-separated)
   - Add author email addresses using the "Add Author" button
   - Upload paper PDF and copyright form
   - Submit

2. **View Certificates:**
   - Once paper is approved, go to "View Paper"
   - Scroll to "Certificates" section
   - Click on any certificate to view/download

### For Admins:

1. **Generate Certificates:**
   - Review and approve paper
   - Click "Generate Certificates" button
   - System automatically creates one certificate per author
   - Each author gets personalized certificate

2. **View All Certificates:**
   - Open paper in admin view
   - Right sidebar shows all generated certificates
   - Click to view individual certificates

## Technical Implementation

### JavaScript (submit_paper.php)
```javascript
// Dynamic author email management
- addAuthorEmail button adds new email input field
- remove-author-email removes field (disabled for first field)
- updateRemoveButtons() ensures at least one field remains
```

### PHP Certificate Generation Logic
```php
// Parse author emails from JSON
$author_emails = json_decode($p['author_emails'], true);

// Parse author names from comma-separated list
$author_names = explode(',', $p['author_list']);

// Generate certificate for each author
foreach ($author_emails as $index => $author_email) {
    $author_name = $author_names[$index];
    // Generate unique cert_id, QR code, PDF
}
```

### Database Queries
```sql
-- Insert individual certificate
INSERT INTO certificates 
(certificate_id, paper_id, author_id, author_name, author_email, qr_code_path, pdf_path) 
VALUES (?, ?, ?, ?, ?, ?, ?)

-- Fetch all certificates for a paper
SELECT * FROM certificates WHERE paper_id=? ORDER BY id
```

## Backward Compatibility

- Old certificates without author_name/author_email still work
- Old URL format `certificate.php?id=123` redirects to first certificate
- New URL format `certificate.php?cert_id=CERT-XXXX` is preferred

## Benefits

✅ Each author gets personalized certificate
✅ Individual QR codes for verification
✅ Unique certificate IDs prevent fraud
✅ Easy distribution via email
✅ Professional presentation
✅ Scalable to any number of authors

## Next Steps (Optional Enhancements)

1. **Email Integration:** Automatically email certificates to authors
2. **Bulk Download:** Download all certificates as ZIP
3. **Certificate Templates:** Add logo and signature images
4. **Email Validation:** Verify author emails during submission
5. **Duplicate Detection:** Prevent duplicate email addresses
