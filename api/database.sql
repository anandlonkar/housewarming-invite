-- Database Schema for Housewarming RSVP System
-- This script creates the necessary table to store RSVP responses

-- Create database (optional - if you need to create it manually)
-- CREATE DATABASE IF NOT EXISTS housewarming_rsvp DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE housewarming_rsvp;

-- Drop table if it exists (only use if you want to start fresh)
-- DROP TABLE IF EXISTS rsvps;

-- Create the rsvps table
CREATE TABLE IF NOT EXISTS rsvps (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    guests INT(11) DEFAULT 1,
    response ENUM('accept', 'decline') NOT NULL,
    comments TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_response (response),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Create a view for easy querying
CREATE OR REPLACE VIEW rsvp_summary AS
SELECT 
    response,
    COUNT(*) as count,
    SUM(guests) as total_guests
FROM rsvps
GROUP BY response;

-- Sample queries you can use:

-- View all RSVPs
-- SELECT * FROM rsvps ORDER BY created_at DESC;

-- View only acceptances
-- SELECT * FROM rsvps WHERE response = 'accept' ORDER BY created_at DESC;

-- View only declines
-- SELECT * FROM rsvps WHERE response = 'decline' ORDER BY created_at DESC;

-- Count total guests accepting
-- SELECT SUM(guests) as total_guests FROM rsvps WHERE response = 'accept';

-- Get summary statistics
-- SELECT * FROM rsvp_summary;

-- Get recent RSVPs (last 7 days)
-- SELECT * FROM rsvps WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC;
