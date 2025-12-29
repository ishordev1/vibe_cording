# IdeaConnect - Business Idea Sharing Platform

A complete web platform where entrepreneurs can post their business ideas and investors can browse, show interest, and connect through a real-time chat system.

## Features

### Core Functionality
- **User Authentication**: Secure sign-up and sign-in with password hashing
- **Dual User Types**: Separate interfaces for idea creators and investors
- **Idea Management**: Full CRUD operations for business ideas
- **Image Upload**: Support for multiple images per idea
- **Search & Filter**: Browse ideas by category and search terms
- **Interest System**: Investors can mark interest in ideas
- **Real-time Chat**: Ajax-based polling chat system
- **Meeting Scheduler**: Schedule meetings directly in chat
- **Responsive Design**: Mobile-friendly Bootstrap UI

### User Roles

#### Idea Creators
- Post business ideas with descriptions and images
- Edit and delete their own ideas
- View statistics (views, interested investors)
- Chat with interested investors
- Manage conversation threads

#### Investors
- Browse and search business ideas
- Filter by categories
- Mark interest in ideas
- Start conversations with idea creators
- Schedule meetings

## Tech Stack

- **Backend**: PHP (vanilla, no frameworks)
- **Frontend**: HTML5, CSS3, JavaScript
- **UI Framework**: Bootstrap 5.3
- **Database**: MySQL
- **Ajax**: jQuery for real-time features
- **Icons**: Bootstrap Icons

## Project Structure

```
idea_sharing_website/
├── actions/                    # Backend action handlers
│   ├── create-idea-action.php
│   ├── delete-idea.php
│   ├── edit-idea-action.php
│   ├── get-meetings.php
│   ├── get-messages.php
│   ├── logout.php
│   ├── mark-interested.php
│   ├── schedule-meeting.php
│   ├── send-message.php
│   ├── signin-action.php
│   ├── signup-action.php
│   ├── start-conversation.php
│   └── update-profile.php
├── assets/                     # Static assets
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── config/                     # Configuration files
│   ├── config.php             # App configuration
│   └── database.php           # Database connection
├── database/                   # Database files
│   └── schema.sql             # Database schema with seed data
├── includes/                   # Reusable components
│   ├── footer.php
│   └── header.php
├── pages/                      # Application pages
│   ├── chat/
│   │   ├── conversation.php
│   │   └── conversations.php
│   ├── creator/
│   │   ├── create-idea.php
│   │   ├── dashboard.php
│   │   ├── edit-idea.php
│   │   └── my-ideas.php
│   ├── investor/
│   │   └── dashboard.php
│   ├── 404.php
│   ├── idea-detail.php
│   ├── ideas.php
│   ├── profile.php
│   ├── signin.php
│   └── signup.php
├── uploads/                    # User uploaded files
└── index.php                   # Homepage
```

## Installation Instructions

### Prerequisites
- XAMPP (or any PHP 7.4+ and MySQL environment)
- Web browser (Chrome, Firefox, etc.)

### Step-by-Step Setup

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start Apache and MySQL services

2. **Copy Project Files**
   - Copy the entire `idea_sharing_website` folder to `C:\xampp\htdocs\`

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "New" to create a new database
   - Or run the SQL file directly:
     - Click "Import" tab
     - Choose file: `C:\xampp\htdocs\idea_sharing_website\database\schema.sql`
     - Click "Go"
   
   Alternatively, you can run this SQL command:
   ```sql
   CREATE DATABASE idea_sharing_platform;
   ```
   Then import the schema.sql file.

4. **Configure Database Connection** (if needed)
   - Open `config/database.php`
   - Update credentials if your MySQL setup is different:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');  // Your MySQL password
     define('DB_NAME', 'idea_sharing_platform');
     ```

5. **Set Folder Permissions**
   - Ensure the `uploads/` directory is writable
   - On Windows, this should work by default
   - On Linux/Mac: `chmod 755 uploads/`

6. **Access the Application**
   - Open your browser and go to: http://localhost/idea_sharing_website
   - You should see the homepage

## Default Test Accounts

The database comes with pre-populated test accounts:

### Idea Creators
- **Username**: john_creator
  - Email: john@creator.com
  - Password: password123

- **Username**: sarah_creator
  - Email: sarah@creator.com
  - Password: password123

- **Username**: david_creator
  - Email: david@creator.com
  - Password: password123

### Investors
- **Username**: mike_investor
  - Email: mike@investor.com
  - Password: password123

- **Username**: emma_investor
  - Email: emma@investor.com
  - Password: password123

## Database Schema

### Tables

1. **users** - Stores user accounts (creators and investors)
2. **ideas** - Business ideas posted by creators
3. **idea_media** - Images/media files for ideas
4. **investors_interested** - Tracks investor interest in ideas
5. **conversations** - Chat conversations between users
6. **messages** - Chat messages
7. **meetings** - Scheduled meetings

## Key Features Explained

### Authentication System
- Passwords are hashed using PHP's `password_hash()` with bcrypt
- Session-based authentication
- Protected routes require login

### Idea Management
- Creators can create, read, update, and delete their ideas
- Support for multiple image uploads (max 5MB per image)
- Ideas have statuses: published, draft, archived
- View tracking for analytics

### Chat System
- Ajax polling every 3 seconds for new messages
- Real-time message updates without page refresh
- Unread message tracking
- Message history stored in database

### Meeting Scheduler
- Schedule meetings with date, time, and location
- Notes/agenda support
- Meeting status tracking (pending, confirmed, cancelled, completed)

### Search & Filter
- Full-text search in titles and descriptions
- Category-based filtering
- Pagination support (9 ideas per page)

## Customization

### Changing App Name
Edit `config/config.php`:
```php
define('APP_NAME', 'Your App Name');
```

### Changing App URL
Edit `config/config.php`:
```php
define('APP_URL', 'http://your-domain.com');
```

### Changing Categories
Edit `config/config.php`:
```php
define('IDEA_CATEGORIES', [
    'Your Category 1',
    'Your Category 2',
    // Add more categories
]);
```

### Styling
- Main CSS file: `assets/css/style.css`
- Bootstrap variables can be customized
- Uses Bootstrap 5.3 color scheme

## Troubleshooting

### Common Issues

1. **Database connection error**
   - Check MySQL is running in XAMPP
   - Verify database credentials in `config/database.php`
   - Ensure database `idea_sharing_platform` exists

2. **Images not uploading**
   - Check `uploads/` directory exists
   - Verify directory is writable
   - Check file size (max 5MB)
   - Ensure file type is image (JPEG, PNG, GIF, WebP)

3. **Page not found errors**
   - Ensure Apache is running
   - Check the URL starts with `http://localhost/idea_sharing_website`
   - Verify `.htaccess` settings if using mod_rewrite

4. **Chat not updating**
   - Check browser console for JavaScript errors
   - Ensure jQuery is loading correctly
   - Verify Ajax requests are completing

## Security Notes

- All user inputs are sanitized using `htmlspecialchars()`
- SQL queries use prepared statements to prevent SQL injection
- Passwords are hashed with bcrypt
- File uploads are validated for type and size
- Session management for authentication

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## Future Enhancements

Potential features to add:
- Email notifications
- WebSocket for real-time chat
- Advanced search with filters
- User ratings and reviews
- Investment amount tracking
- Document uploads
- Video pitch support
- Social media sharing

## Support

For issues or questions:
- Check the troubleshooting section
- Review PHP error logs in `C:\xampp\logs\`
- Check browser console for JavaScript errors

## License

This project is created for educational purposes.

## Credits

- **UI Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **JavaScript**: jQuery 3.6.0
- **PHP**: 7.4+
- **Database**: MySQL 5.7+

---

**Version**: 1.0.0  
**Last Updated**: November 2025  
**Author**: IdeaConnect Development Team
