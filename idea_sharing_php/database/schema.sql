
DROP DATABASE IF EXISTS idea_sharing_platform;

-- Create database
CREATE DATABASE idea_sharing_platform;
USE idea_sharing_platform;

-- =============================================
-- Table: users
-- Stores both idea creators and investors
-- =============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_type ENUM('creator', 'investor') NOT NULL,
    bio TEXT,
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_type (user_type),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: ideas
-- Stores business ideas posted by creators
-- =============================================
CREATE TABLE ideas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: idea_media
-- Stores images/media for ideas
-- =============================================
CREATE TABLE idea_media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    INDEX idx_idea_id (idea_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: investors_interested
-- Tracks which investors are interested in ideas
-- =============================================
CREATE TABLE investors_interested (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    investor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_interest (idea_id, investor_id),
    INDEX idx_idea_id (idea_id),
    INDEX idx_investor_id (investor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: conversations
-- Stores chat conversations between users
-- =============================================
CREATE TABLE conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    creator_id INT NOT NULL,
    investor_id INT NOT NULL,
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_conversation (idea_id, creator_id, investor_id),
    INDEX idx_creator_id (creator_id),
    INDEX idx_investor_id (investor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: messages
-- Stores chat messages
-- =============================================
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_sender_id (sender_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: meetings
-- Stores scheduled meetings
-- =============================================
CREATE TABLE meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    location VARCHAR(255),
    notes TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (scheduled_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_meeting_date (meeting_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table: notifications
-- Stores user notifications
-- =============================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('message', 'meeting_scheduled', 'meeting_updated', 'meeting_confirmed', 'meeting_cancelled', 'interest') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id INT,
    related_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Seed Data
-- =============================================

-- Insert sample users (password is 'password123' hashed with bcrypt)
INSERT INTO users (username, email, password, full_name, user_type, bio) VALUES
('john_creator', 'john@creator.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', 'creator', 'Passionate entrepreneur with 5 years of experience in tech startups.'),
('sarah_creator', 'sarah@creator.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Johnson', 'creator', 'Marketing expert turned entrepreneur looking to revolutionize e-commerce.'),
('mike_investor', 'mike@investor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Anderson', 'investor', 'Angel investor focused on SaaS and fintech startups.'),
('emma_investor', 'emma@investor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Davis', 'investor', 'Venture capitalist with expertise in healthcare and AI technologies.'),
('david_creator', 'david@creator.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Lee', 'creator', 'Serial entrepreneur passionate about sustainable solutions.');

-- Insert sample ideas
INSERT INTO ideas (user_id, title, description, category, status) VALUES
(1, 'AI-Powered Personal Finance Manager', 'A mobile app that uses artificial intelligence to help users manage their finances, track spending patterns, and provide personalized savings recommendations. The app will integrate with bank accounts and use machine learning to predict future expenses and suggest budget optimizations.', 'Technology', 'published'),
(1, 'Smart Home Energy Optimizer', 'IoT device that monitors home energy consumption in real-time and automatically adjusts heating, cooling, and lighting to minimize costs while maintaining comfort. Includes a mobile dashboard showing savings and environmental impact.', 'Technology', 'published'),
(2, 'Sustainable Fashion Marketplace', 'An online platform connecting eco-conscious consumers with sustainable fashion brands. Features include carbon footprint tracking for each purchase, clothing swap marketplace, and educational content about sustainable fashion practices.', 'E-commerce', 'published'),
(2, 'Virtual Interior Design Service', 'AI-powered platform that allows users to visualize furniture and decor in their actual living spaces using AR technology. Includes personalized design recommendations and direct purchase integration.', 'Design', 'published'),
(5, 'Healthy Meal Prep Subscription', 'Weekly meal prep service delivering pre-portioned, organic ingredients with simple recipe cards. Focuses on dietary restrictions (keto, vegan, gluten-free) and reduces food waste through precise portioning.', 'Food & Beverage', 'published'),
(5, 'Local Artisan Marketplace', 'Platform connecting local craftspeople and artisans with customers in their area. Features include live streaming of creation process, custom order management, and local pickup or delivery options.', 'E-commerce', 'published');

-- Insert sample idea media
INSERT INTO idea_media (idea_id, file_path, file_type, is_primary) VALUES
(1, 'uploads/finance-app-1.jpg', 'image/jpeg', TRUE),
(2, 'uploads/smart-home-1.jpg', 'image/jpeg', TRUE),
(3, 'uploads/fashion-1.jpg', 'image/jpeg', TRUE),
(4, 'uploads/interior-1.jpg', 'image/jpeg', TRUE),
(5, 'uploads/meal-prep-1.jpg', 'image/jpeg', TRUE),
(6, 'uploads/artisan-1.jpg', 'image/jpeg', TRUE);

-- Insert sample interests
INSERT INTO investors_interested (idea_id, investor_id) VALUES
(1, 3),
(1, 4),
(2, 3),
(3, 4),
(5, 3);

-- Insert sample conversations
INSERT INTO conversations (idea_id, creator_id, investor_id, last_message_at) VALUES
(1, 1, 3, NOW()),
(1, 1, 4, NOW()),
(3, 2, 4, NOW());

-- Insert sample messages
INSERT INTO messages (conversation_id, sender_id, message, is_read) VALUES
(1, 3, 'Hi John, I\'m very interested in your AI-Powered Personal Finance Manager idea. Could we schedule a call to discuss the technical details?', TRUE),
(1, 1, 'Hello Mike! I\'d be happy to discuss this with you. I have availability this week. What works best for you?', TRUE),
(1, 3, 'Great! How about Thursday at 2 PM?', FALSE),
(2, 4, 'John, your finance app looks promising. What\'s your current stage of development?', TRUE),
(2, 1, 'Thanks Emma! We have a working prototype and are currently testing with 50 beta users.', FALSE),
(3, 4, 'Sarah, I love the sustainability angle of your marketplace. Do you have traction data?', TRUE),
(3, 2, 'Hi Emma! Yes, we\'ve onboarded 25 brands and have 500 registered users in our waitlist.', FALSE);

-- Insert sample meetings
INSERT INTO meetings (conversation_id, scheduled_by, meeting_date, meeting_time, location, notes, status) VALUES
(1, 3, '2025-11-20', '14:00:00', 'Zoom Meeting', 'Discuss technical architecture and funding requirements', 'confirmed'),
(3, 4, '2025-11-22', '10:00:00', 'Coffee Shop Downtown', 'Review business model and growth strategy', 'pending');



CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('message', 'meeting_scheduled', 'meeting_updated', 'meeting_confirmed', 'meeting_cancelled', 'interest') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id INT,
    related_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
