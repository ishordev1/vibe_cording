# Important Database Update Required

## Review Notes Feature

To enable admin review notes that are visible to authors, you need to add a new column to the database.

### Run this SQL command in phpMyAdmin:

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select the `research_db` database
3. Go to the SQL tab
4. Copy and paste the following SQL:

```sql
ALTER TABLE papers 
ADD COLUMN review_notes TEXT NULL AFTER status;
```

5. Click "Go" to execute

### What This Does:

- Adds a `review_notes` column to the `papers` table
- Allows admins to write review comments when changing paper status
- Authors will see these notes on their paper view page

### Files Modified:

- `admin/review.php` - Now saves review notes to database
- `paper_view.php` - Displays review notes to paper owner
- `sql/add_review_notes.sql` - SQL migration file

---

## Certificate Settings Feature

The certificate settings feature automatically creates its own database table when you first visit the page.

**No manual setup required!**

Just navigate to: Admin Panel → Certificate Settings

---

## How to Use:

### Review Notes:
1. Admin goes to a paper review page
2. Admin selects status and writes notes
3. Notes are saved and visible to the author

### Certificate Customization:
1. Admin clicks "Certificate Settings" in sidebar
2. Customize colors, borders, fonts, text
3. Preview changes in real-time
4. Save settings
5. All future certificates use these settings

---

**Questions?** Check the main INSTALLATION.md file for more details.
