# Template Customization Setup

## What Was Added

### 1. **Customize Buttons** (in both Certificates and Offer Letters sections)
- Added a new card in the Total Approved stat area with a "Template Settings" button
- Clicking the palette icon opens the customization interface
- Certificates button: Purple theme
- Offer Letters button: Blue theme

### 2. **Customization Interface** (`/admin/sections/customize-template.php`)

The new customization page allows admins to customize:

#### Organization Information
- Organization Name
- Organization Address

#### Signatories
- Director Name  
- Coordinator Name

#### Template Content
- Certificate/Letter Title
- Footer Text

#### Color Scheme
- Primary Color (for highlights)
- Secondary Color (for text)
- Live color picker with hex value display

#### Files Upload
- Organization Logo (optional)
- Director Signature Image (optional)
- Files stored in `/public/uploads/logos/` and `/public/uploads/signatures/`

### 3. **Database Table**
Created `template_settings` table to store customization data:

```sql
CREATE TABLE `template_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `template_type` VARCHAR(50) NOT NULL UNIQUE,
    `settings` LONGTEXT NOT NULL (JSON format),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Installation

### Step 1: Create the Database Table
Run this SQL in your MySQL database:

```bash
mysql -u root -p DigitalTarai < migrations/add_template_settings_table.sql
```

Or copy and paste the SQL from the migration file into phpMyAdmin.

### Step 2: Create Upload Directories
The system will automatically create these directories when you first upload files:
- `/public/uploads/logos/`
- `/public/uploads/signatures/`

### Step 3: Access the Customization Interface
1. Go to Admin Dashboard
2. Navigate to **Certificates** or **Offer Letters**
3. Click the **Template Settings** button (purple palette icon) in the stat card
4. Fill in your customization details
5. Click **Save Settings**

## Features

✅ **Live Preview** - See changes in real-time on the right side panel  
✅ **Color Picker** - Visual color selection with hex code display  
✅ **Logo & Signature Upload** - Upload custom images  
✅ **Responsive Form** - Works on all screen sizes  
✅ **Settings Persistence** - Settings saved to database and loaded automatically  
✅ **Separate Templates** - Different settings for certificates and offer letters  

## How Templates Use These Settings

### Currently Implemented
- Organization name and address stored in database

### Ready for Integration
The settings are stored and accessible. Next phase can integrate:
1. **Certificate Template** - Load custom colors, logo, and signature from `template_settings`
2. **Offer Letter Template** - Load custom colors, logo, and signature from `template_settings`
3. **Dynamic PDF Generation** - Use saved settings when generating new documents

## Next Steps

To use the customized settings in actual certificate/offer letter generation:

1. Query the `template_settings` table before generating documents
2. Extract JSON settings and use them to:
   - Replace color values in CSS
   - Insert logo image from file path
   - Insert signature image from file path
   - Update text content (titles, footer, names)

Example query:
```php
$result = $conn->query("SELECT settings FROM template_settings WHERE template_type='certificates'");
$settings = json_decode($result->fetch_assoc()['settings'], true);
// Now use $settings['primary_color'], $settings['logo_path'], etc.
```

## File Structure

```
admin/sections/
├── certificates.php           (Modified - added customize check)
├── offer-letters.php          (Modified - added customize check)
└── customize-template.php     (New - customization interface)

migrations/
└── add_template_settings_table.sql (New - database migration)

public/uploads/
├── logos/                     (Auto-created for logo uploads)
└── signatures/                (Auto-created for signature uploads)
```
