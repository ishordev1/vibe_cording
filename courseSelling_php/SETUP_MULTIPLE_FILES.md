# Setup Instructions for Multiple Module Files Feature

## Quick Start

### Step 1: Create the Database Table
Run the migration script OR manually execute the SQL:

**Option A: Run migration script**
```
Visit: http://localhost/ai/DigitalTarai/migrate_module_files.php
```

**Option B: Manual SQL (phpMyAdmin)**
```sql
CREATE TABLE module_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    upload_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);
```

### Step 2: Verify Upload Directory Exists
The system will auto-create `/public/uploads/modules/` directory if it doesn't exist.

**Manual check** (optional):
```bash
mkdir -p /path/to/ai/DigitalTarai/public/uploads/modules/
chmod 755 /path/to/ai/DigitalTarai/public/uploads/modules/
```

### Step 3: Use the Feature
1. Go to: `Admin Panel > Internships > Select Internship > Content`
2. Click "Add Module" or "Edit Details" on existing module
3. Scroll to "Upload Files" section
4. Select one or more files:
   - Click upload area OR
   - Drag & drop files
5. Selected files will appear with icons and sizes
6. Click "Add Module" or "Update Module" to save

## Testing Workflow

### Test 1: Add Module with Multiple Files
1. Create new internship (if needed)
2. Go to Content Management
3. Click "Add Module"
4. Fill in: Title, Description, Order
5. **SELECT 3-5 FILES** (mix of PDF, Word, Excel, etc.)
6. Click "Add Module"
7. ✓ Verify: Files uploaded, displayed in list

### Test 2: Add More Files to Existing Module
1. Go to existing module "Edit Details"
2. Scroll to "Uploaded Files" - should see list
3. Scroll to "Select Files" section
4. Add 2-3 more files
5. Click "Update Module"
6. ✓ Verify: New files added to list, old ones still there

### Test 3: Download File
1. In "Uploaded Files" section
2. Click "Download" button
3. ✓ Verify: File opens/downloads correctly

### Test 4: Delete File
1. In "Uploaded Files" section
2. Click "Delete" button on a file
3. Confirm deletion
4. ✓ Verify: File removed from list, file deleted from server

### Test 5: Drag & Drop
1. Open module edit form
2. Drag 2-3 files from explorer to upload area
3. ✓ Verify: Files appear in preview before upload

## Supported File Types
✅ Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT
✅ Archives: ZIP, RAR
✅ Media: MP4, MP3, JPG, JPEG, PNG, GIF

To allow more types, edit line in admin/sections/internships.php:
```php
accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt,.xls,.xlsx,.mp4,.mp3,.jpg,.jpeg,.png,.gif"
```

## File Organization

### Server Storage
```
/public/uploads/modules/
├── 1735000123_0_course_intro.pdf
├── 1735000123_1_syllabus.docx
├── 1735000123_2_exercises.xlsx
└── ...
```

### Database Structure
```
module_files table:
- id: Unique file ID
- module_id: Links to modules table
- file_name: Original filename displayed
- file_path: Relative path (modules/filename.ext)
- file_type: Extension (.pdf, .docx, etc.)
- file_size: Size in bytes
- upload_order: Order for display
- created_at: Upload timestamp
```

## Features Summary

### Admin Features
✅ Select multiple files in single upload
✅ Drag & drop file upload
✅ Visual file type indicators (icons)
✅ File size display
✅ Upload date/time tracking
✅ Individual file download
✅ Individual file delete
✅ Add files without replacing existing ones
✅ File upload order tracking

### User Experience
✅ Real-time file preview before upload
✅ File count indicator
✅ Color-coded file types
✅ Organized layout with existing files
✅ One-click download per file
✅ Confirmation before delete
✅ Responsive design (mobile-friendly)

## Troubleshooting

### Problem: Upload Directory Not Created
**Solution**: Ensure PHP has write permissions
```
chmod 777 /path/to/ai/DigitalTarai/public/uploads/
```

### Problem: Files Don't Appear After Upload
**Check**:
1. Refresh page (Ctrl+F5)
2. Check database for module_files records
3. Verify /public/uploads/modules/ directory exists
4. Check PHP error logs

### Problem: Can't Delete File
**Check**:
1. Verify file exists on server
2. Check write permissions on /public/uploads/modules/
3. Ensure database record exists in module_files

### Problem: Filenames Look Corrupted
- This is normal! Filenames are sanitized and timestamped
- Original filename shown in "file_name" field
- Display name shows: "Sample Document.pdf" (not timestamp version)

## Database Verification

Check if table created successfully:
```sql
SHOW TABLES LIKE 'module_files';
DESCRIBE module_files;
```

Should return table with columns:
- id, module_id, file_name, file_path, file_type, file_size, upload_order, created_at

## Performance Notes

- **Max file size**: Depends on PHP config (php.ini)
  - Default: 2MB (post_max_size)
  - Can be increased in php.ini
- **File limit**: No per-module limit (can add unlimited files)
- **Storage**: Ensure sufficient disk space
- **Database**: minimal impact (small records)

## Backward Compatibility

✓ Old single-file modules still work
✓ Old module_file field preserved
✓ Both systems can coexist
✓ No data loss on upgrade
✓ Existing modules unaffected

## Future Enhancements (Optional)

Possible additions:
- [ ] Reorder files via drag-in-list
- [ ] Bulk download (ZIP)
- [ ] File descriptions
- [ ] Version history
- [ ] Storage quotas
- [ ] Preview thumbnails
- [ ] File access logs

