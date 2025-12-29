# Multiple Module Files Upload - Implementation Guide

## Overview
Updated the internship module management system to allow uploading multiple files per module instead of just one file.

## Changes Made

### 1. Database Schema Addition
**File**: `setup.php` + `migrate_module_files.php`

**New Table**: `module_files`
```sql
CREATE TABLE module_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,          -- Original filename displayed to users
    file_path VARCHAR(255) NOT NULL,          -- Relative path in server
    file_type VARCHAR(50),                    -- File extension
    file_size INT,                            -- File size in bytes
    upload_order INT DEFAULT 0,               -- Order of files in module
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
)
```

### 2. Backend Updates (admin/sections/internships.php)

#### New Helper Function
```php
function formatFileSize($bytes) {
    // Converts bytes to human-readable format (KB, MB, GB, etc.)
}
```

#### Enhanced Module Upload Handler
- **Old**: Single file upload via `$_FILES['module_file']`
- **New**: 
  - Still supports single file upload (backward compatibility)
  - Added multiple file upload via `$_FILES['module_files'][]`
  - Each file stored in `module_files` table with metadata
  - Files stored in `/public/uploads/modules/` directory

#### New Operations Added
1. **Add Module with Multiple Files**
   - Insert module record
   - Loop through uploaded files
   - Store each file in `module_files` table
   - Maintain upload order

2. **Update Module with Additional Files**
   - Update module details
   - Add new files without deleting old ones
   - Maintain upload order sequence

3. **Delete Individual Module File**
   - Handle: `?delete_module_file=<file_id>&module_id=<id>&internship_id=<id>`
   - Delete file from server
   - Delete record from `module_files` table

### 3. Frontend Updates (Admin Module Form)

#### Enhanced File Upload Interface
- **Multiple file selection**: `<input type="file" name="module_files[]" multiple>`
- **Drag & drop support**: Works with multiple files
- **File preview**: Shows all selected files before upload
- **File type icons**: Different icons for PDF, Word, Excel, PowerPoint, etc.
- **File size display**: Shows size in human-readable format

#### Existing Files Display
- Shows all uploaded files for the module
- Displays file type with icon
- Shows file size and upload date
- Individual download button for each file
- Individual delete button for each file
- Delete confirmation dialog

### 4. Student-Side Updates (Optional - for consistency)

**File**: `student/modules.php` (if updated in future)
Currently still shows only the legacy `module_file` field, but can be updated to show all files from `module_files` table:

```php
// Get all files for the module
$files = $conn->query("SELECT * FROM module_files 
                      WHERE module_id=$module_id 
                      ORDER BY upload_order");
```

## Features

### Admin Side (Upload & Manage)
✅ Upload multiple files per module
✅ Drag & drop multiple files
✅ View all uploaded files with metadata
✅ Delete individual files
✅ Maintain file upload order
✅ File type icons for visual identification
✅ File size display
✅ Upload date tracking
✅ Backward compatible with old single-file system

### File Type Support
- Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT
- Archives: ZIP, RAR
- Media: MP4, MP3, JPG, JPEG, PNG, GIF
- Expandable to support additional formats

### Visual Indicators
- Different colored icons based on file type
- Red for PDF
- Blue for Word documents
- Green for Excel spreadsheets
- Orange for PowerPoint
- Yellow for archives
- Purple for videos/audio
- Pink for images

## File Locations

### Core Files Modified
1. `/setup.php` - Database schema
2. `/admin/sections/internships.php` - Upload logic & UI
3. `/migrate_module_files.php` - Migration script

### Directories
- Upload location: `/public/uploads/modules/`
- Database table: `module_files`

## Usage

### For Admin Users

**1. Add Module with Multiple Files**
- Go to Internship Content page
- Click "Add Module"
- Fill in title, description, order
- **NEW**: Select multiple files using:
  - Click on upload area
  - Drag & drop files
  - Files appear with icons and sizes
- Click "Add Module" to save

**2. Add More Files to Existing Module**
- Click "Edit Details" on the module
- Scroll to "Uploaded Files" section showing current files
- Scroll down to "Upload Files" section
- Select additional files
- Click "Update Module"
- New files added to the list without removing old ones

**3. Delete a File**
- In "Uploaded Files" section
- Click "Delete" button next to the file
- Confirm deletion
- File removed from module

**4. Download a File**
- In "Uploaded Files" section
- Click "Download" button next to the file
- File opens/downloads in browser

## Database Migration

Run the migration script once to create the `module_files` table:

```
php migrate_module_files.php
```

Or use the setup.php file if creating a new database.

## Technical Details

### File Naming Convention
- Format: `<timestamp>_<index>_<sanitized_filename>`
- Example: `1735000123_0_course_materials.pdf`
- Purpose: Ensures unique filenames, prevents overwrites

### File Storage Path
- Stored in: `/public/uploads/modules/`
- Database stores: Relative path (`modules/filename.ext`)
- URL accessible: `http://localhost/ai/DigitalTarai/public/uploads/modules/filename.ext`

### Upload Order
- Tracked via `upload_order` field
- Automatically incremented for new files
- Maintains sequence when displaying files
- Can be reordered in future updates

## Backward Compatibility

✅ **Fully Compatible**
- Old `modules.module_file` field still works
- New system doesn't interfere with old data
- Both can coexist
- Migration only adds new table, doesn't modify existing

## Future Enhancements

Possible improvements:
- [ ] Drag-to-reorder files display
- [ ] Bulk file download (ZIP)
- [ ] File description/title per file
- [ ] File versioning
- [ ] Storage quota per module
- [ ] File preview in admin
- [ ] Student-side multiple file display
- [ ] File access analytics

## Testing Checklist

- [ ] Can select multiple files in add module form
- [ ] Drag & drop works for multiple files
- [ ] File preview shows all selected files
- [ ] Files upload successfully
- [ ] Files appear in existing files list
- [ ] File metadata displayed correctly (name, size, date)
- [ ] Can download each file
- [ ] Can delete individual files
- [ ] Can add more files to existing module
- [ ] Files maintain upload order
- [ ] Icons display correctly by file type
- [ ] Works in different browsers
- [ ] Large files handled correctly
- [ ] Filename special characters sanitized

