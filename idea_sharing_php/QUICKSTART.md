# Quick Start Guide

## Getting Started in 5 Minutes

### 1. Database Setup
Open phpMyAdmin (http://localhost/phpmyadmin) and run:

```sql
-- Create database
CREATE DATABASE idea_sharing_platform;
```

Then import the schema file:
- Click "Import" tab
- Choose: `C:\xampp\htdocs\idea_sharing_website\database\schema.sql`
- Click "Go"

### 2. Access the Application
Open your browser and visit:
```
http://localhost/idea_sharing_website
```

### 3. Test Login Credentials

**For Idea Creators:**
- Email: `john@creator.com`
- Password: `password123`

**For Investors:**
- Email: `mike@investor.com`
- Password: `password123`

## What to Test

### As an Idea Creator:
1. Sign in with creator account
2. Go to "Post Idea" from navigation
3. Create a new business idea
4. View your dashboard statistics
5. Check "My Ideas" to manage your posts

### As an Investor:
1. Sign in with investor account
2. Browse ideas from "Browse Ideas" menu
3. Click on an idea to view details
4. Click "I'm Interested" button
5. Start a chat conversation
6. Schedule a meeting in the chat

### Test the Chat System:
1. Open two browser windows (or one normal + one incognito)
2. Sign in as creator in one window
3. Sign in as investor in the other
4. Investor shows interest and starts chat
5. Send messages between both users
6. Watch messages appear in real-time (polls every 3 seconds)

## File Upload Testing

When creating/editing ideas:
- Supported formats: JPEG, PNG, GIF, WebP
- Maximum size: 5MB per image
- Can upload multiple images
- First image becomes the primary/featured image

## Directory Structure (Important Folders)

```
idea_sharing_website/
├── uploads/          ← Images go here (auto-created)
├── pages/            ← All page files
├── actions/          ← Form processing
├── config/           ← Settings
└── database/         ← SQL schema
```

## Common URLs

- **Homepage**: http://localhost/idea_sharing_website
- **Sign In**: http://localhost/idea_sharing_website/pages/signin.php
- **Sign Up**: http://localhost/idea_sharing_website/pages/signup.php
- **Browse Ideas**: http://localhost/idea_sharing_website/pages/ideas.php
- **Creator Dashboard**: http://localhost/idea_sharing_website/pages/creator/dashboard.php
- **Investor Dashboard**: http://localhost/idea_sharing_website/pages/investor/dashboard.php

## Need Help?

Check the main README.md file for:
- Detailed installation steps
- Troubleshooting guide
- Full feature documentation
- Customization options
