# 📚 SAMPLE DATA & QUICK START

## 🔑 DEFAULT LOGIN CREDENTIALS

### Admin Account (Already Created)
```
Email: admin@internship.com
Password: admin123
Role: Admin
```

### To Create Intern Account:
1. Go to: http://localhost/internship-provider/auth/register.php
2. Fill registration form
3. Login with your credentials

---

## 📋 SAMPLE INTERNSHIP DATA

After login as admin, create these sample internships:

### Internship 1: Web Development
```
Title: Junior Web Developer
Role: Web Developer
Duration: 2-3 months
Type: Task-based
Remote: Yes (Hybrid)
Description: 
  Build responsive web applications using modern technologies. 
  You'll work on real-world projects, collaborate with teams, 
  and develop your full-stack development skills.

Skills Required:
  HTML5, CSS3, JavaScript, React, PHP, MySQL
```

### Internship 2: Data Analytics
```
Title: Data Analytics Intern
Role: Data Analyst
Duration: 1-2 months
Type: Learning
Remote: Yes (Fully Remote)
Description:
  Learn data analysis and visualization. Work with real datasets,
  create dashboards, and develop insights from data.

Skills Required:
  Excel, Python, SQL, Tableau, Statistics
```

### Internship 3: Content Writing
```
Title: Content Writer
Role: Content Writer
Duration: 1-4 months (Flexible)
Type: Task-based
Remote: Yes
Description:
  Create engaging content for blogs, social media, and marketing.
  Learn SEO, copywriting, and content strategy.

Skills Required:
  English Proficiency, SEO, WordPress, Content Writing
```

---

## 📝 SAMPLE TASKS

### Level 1 - Foundation (All Internships)
1. **Setup Development Environment**
   - Format: Link (GitHub or Drive)
   - Deliverable: Screenshot of setup + github repo link
   - Description: Set up your local development environment with required tools

2. **Complete Onboarding Quiz**
   - Format: Text
   - Deliverable: Quiz answers (5-10 questions)
   - Description: Test your knowledge of company processes

### Level 2 - Intermediate
1. **Build First Project**
   - Format: Link
   - Deliverable: GitHub repository link with README
   - Description: Create a small but complete project

2. **Code Review & Documentation**
   - Format: Text
   - Deliverable: Document your code architecture
   - Description: Explain your code structure and design patterns

### Level 3 - Advanced
1. **Complex Feature Implementation**
   - Format: Link
   - Deliverable: Live demo link + GitHub repo
   - Description: Build a complex feature with multiple components

2. **Performance Optimization**
   - Format: Text
   - Deliverable: Optimization report with metrics
   - Description: Identify and optimize bottlenecks

### Level 4 - Expert
1. **Lead a Mini Project**
   - Format: Link
   - Deliverable: Project repository with team contributions
   - Description: Lead and deliver a complete project

2. **Mentoring & Knowledge Sharing**
   - Format: Text
   - Deliverable: Documentation for next batch
   - Description: Create training materials for future interns

---

## 🚀 STEP-BY-STEP DEMO

### Day 1: Admin Setup
```
1. Login as admin (admin@internship.com / admin123)
2. Create first internship "Web Developer"
3. Publish the internship
4. Create 2 tasks for Level 1
5. Create 2 tasks for Level 2
```

### Day 2-3: User Flow
```
1. Open incognito/new browser
2. Register as new intern (John Doe, john@example.com)
3. Login and browse internships
4. Apply for "Web Developer" position
5. Upload screenshot as ₹500 proof
6. Submit application
```

### Day 4: Admin Approval
```
1. Login as admin
2. Go to Applications
3. View pending application
4. Approve application
5. Offer letter is auto-generated
6. Check database to see tables populated
```

### Day 5: Intern Tasks
```
1. Login as intern
2. Dashboard shows active internship
3. Click "My Tasks"
4. Submit Level 1 Task 1 (GitHub link)
5. Submit Level 1 Task 2 (Text response)
```

### Day 6: Admin Review
```
1. Login as admin
2. Go to "Review Submissions"
3. View submitted tasks
4. Approve both tasks
5. Level 1 marked as complete
```

### Day 7: Certificate
```
1. Complete all levels (repeat submissions)
2. Go to "Generate Certificates"
3. Generate certificate for intern
4. Login as intern
5. View and print certificate
```

---

## 📊 DATABASE SAMPLE QUERIES

### Check Users
```sql
SELECT * FROM users;
-- Shows: admin account + registered interns
```

### Check Applications
```sql
SELECT a.id, u.full_name, i.title, a.status 
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id;
```

### Check Tasks
```sql
SELECT t.id, l.level_number, t.title, t.submission_format 
FROM tasks t
JOIN levels l ON t.level_id = l.id
ORDER BY l.level_number, t.order_sequence;
```

### Check Submissions
```sql
SELECT ts.id, u.full_name, t.title, ts.status, ts.submitted_at
FROM task_submissions ts
JOIN users u ON ts.user_id = u.id
JOIN tasks t ON ts.task_id = t.id;
```

---

## 🎯 TEST SCENARIOS

### Scenario 1: Happy Path
```
Outcome: Intern completes all levels and gets certificate
Steps:
1. Register → Apply → Get Approved
2. Submit all level tasks
3. All approved by admin
4. Certificate generated
5. Download certificate
```

### Scenario 2: Rejection Path
```
Outcome: Application rejected
Steps:
1. Register → Apply
2. Admin rejects with reason
3. Intern sees rejection
4. Can apply to other internships
```

### Scenario 3: Rework Path
```
Outcome: Intern fixes and resubmits task
Steps:
1. Submit task
2. Admin requests rework
3. Intern resubmits
4. Admin approves
5. Continue to next level
```

---

## 💾 DATABASE INFORMATION

### Sample Data Created Automatically:
- ✅ Admin user: admin@internship.com
- ✅ 4 Levels (Foundation, Intermediate, Advanced, Expert)
- ✅ Database ready for internships
- ✅ All tables with relationships

### Manual Data to Add:
- Internships (via admin panel)
- Tasks (via admin panel)
- Applications (via user panel)
- Attendance (via admin panel)

---

## 🧪 TESTING CHECKLIST

### Admin Tests:
- [ ] Login as admin
- [ ] Create internship
- [ ] Publish internship
- [ ] View applications
- [ ] Approve/Reject application
- [ ] Create tasks
- [ ] Review submissions
- [ ] Mark attendance
- [ ] Generate certificate

### User Tests:
- [ ] Register account
- [ ] Login as user
- [ ] View internships
- [ ] Apply for internship
- [ ] Upload payment proof
- [ ] View application status
- [ ] Submit tasks
- [ ] View attendance
- [ ] Download certificate

---

## 🔐 Security Testing

### Password Requirements:
- Minimum 8 characters
- Uppercase letter
- Lowercase letter
- Number
- Example: `Admin@123`

### File Upload Tests:
- Upload PDF, JPG, PNG (Allowed)
- Upload EXE, ZIP (Blocked)
- File size > 5MB (Rejected)

### Form Validation:
- Email format validation
- Phone number (10 digits)
- Required fields enforcement
- XSS prevention (htmlspecialchars)

---

## 📱 RESPONSIVE DESIGN TEST

### Desktop (1920x1080)
- [ ] All content visible
- [ ] Sidebar works
- [ ] Tables display properly

### Tablet (768x1024)
- [ ] Responsive grid
- [ ] Navigation adjusts
- [ ] Forms work

### Mobile (375x812)
- [ ] Stack layout
- [ ] Touch buttons
- [ ] Readable text

---

## 🎨 UI ELEMENTS TO TEST

### Forms:
- [ ] Registration form validation
- [ ] Login form works
- [ ] Application form submission
- [ ] Task submission form

### Tables:
- [ ] Data displays correctly
- [ ] Rows are readable
- [ ] Buttons responsive

### Buttons:
- [ ] Create/Edit/Delete works
- [ ] Approve/Reject works
- [ ] Upload functionality works

### Alerts:
- [ ] Success messages show
- [ ] Error messages show
- [ ] Warnings appear

---

## 📈 PERFORMANCE TIPS

### Database:
- Indexes on foreign keys
- Prepared statements for security
- Connection pooling ready

### Code:
- Minimal database calls
- Efficient loops
- Proper error handling

### UI:
- Bootstrap minified
- CSS optimized
- Icons from CDN

---

## 🔍 DEBUGGING TIPS

### Check Console:
```
Press F12 in browser
Look for any JS errors
Check Network tab
```

### Check Logs:
```
PHP error logs
MySQL error logs
Session logs
```

### Check Database:
```
phpMyAdmin
View table structures
Check data relationships
```

---

## 📞 COMMON QUESTIONS

**Q: How do I reset admin password?**
A: Update in database: 
```sql
UPDATE users SET password = '<bcrypt-hash>' WHERE email = 'admin@internship.com';
```

**Q: How do I create a new internship?**
A: Login as admin → Manage Internships → Create New

**Q: How do interns apply?**
A: Browse Internships → Apply Now → Upload proof

**Q: When are certificates generated?**
A: After completing all 4 levels

**Q: How to reset database?**
A: Delete tables and run db_schema.php again

---

## ✅ YOU'RE READY!

The platform is fully functional. Start with:
1. Login as admin
2. Create sample data
3. Register as intern
4. Test complete workflow

Enjoy exploring the platform! 🎓

---

**For Issues**: Check console logs and database
**For Help**: Read README.md and SETUP_GUIDE.md
