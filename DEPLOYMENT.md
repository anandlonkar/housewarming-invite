# Housewarming Invitation Website - Deployment Guide

## 📋 Project Overview
This is a complete housewarming invitation website similar to Evite, with RSVP tracking and email notifications.

### Features
- Beautiful invitation display with custom header image
- RSVP form (Accept/Decline)
- Email notifications for each RSVP
- MySQL database storage
- Password-protected admin panel
- Mobile-responsive design

---

## 🚀 Deployment Steps for iPage

### Step 1: Create MySQL Database

1. Log in to your iPage control panel
2. Navigate to **MySQL Databases**
3. Create a new database:
   - Database Name: `housewarming_rsvp` (or your choice)
   - Username: Create a new user
   - Password: Create a strong password
   - **IMPORTANT:** Write down these credentials!

4. Import the database schema:
   - Go to **phpMyAdmin** in iPage control panel
   - Select your database
   - Click on **Import** tab
   - Upload the file: `api/database.sql`
   - Click **Go** to create the table

### Step 2: Configure the Application

1. Open `api/config.php` in a text editor
2. Update the following values:

```php
// Database credentials
define('DB_HOST', 'localhost');                    // Usually 'localhost'
define('DB_NAME', 'your_database_name');           // Your database name from Step 1
define('DB_USER', 'your_database_user');           // Your database username from Step 1
define('DB_PASS', 'your_database_password');       // Your database password from Step 1

// Email configuration
define('ADMIN_EMAIL', 'your-email@example.com');   // Your email to receive notifications

// Admin panel password
define('ADMIN_PASSWORD', 'your_secure_password');  // Choose a strong password
```

3. Save the file

### Step 3: Upload Files to iPage

#### Option A: Using File Manager (Easier)
1. Log in to iPage control panel
2. Open **File Manager**
3. Navigate to your domain's root directory (usually `public_html` or `www`)
4. Upload all files maintaining the folder structure:
   ```
   public_html/
   ├── index.html
   ├── invite_image.png
   ├── css/
   │   └── style.css
   ├── js/
   │   └── script.js
   ├── api/
   │   ├── config.php
   │   ├── rsvp.php
   │   └── database.sql
   └── admin/
       └── view_rsvps.php
   ```

#### Option B: Using FTP (Advanced)
1. Download an FTP client (like FileZilla)
2. Connect to iPage using FTP credentials from control panel
3. Upload all files to the appropriate directory

### Step 4: Set File Permissions

1. In File Manager, set permissions for PHP files:
   - `api/config.php` → 644
   - `api/rsvp.php` → 644
   - `admin/view_rsvps.php` → 644

### Step 5: Test the Installation

1. **Test the invitation page:**
   - Visit: `http://lonkar.in` or `http://www.lonkar.in`
   - You should see your invitation with the header image

2. **Test RSVP submission:**
   - Fill out the form
   - Click Accept or Decline
   - Submit the form
   - You should receive an email notification

3. **Test admin panel:**
   - Visit: `http://lonkar.in/admin/view_rsvps.php`
   - Enter the admin password you set in config.php
   - You should see the RSVP you just submitted

---

## 🔧 Troubleshooting

### Problem: Database connection errors
**Solution:** 
- Double-check database credentials in `api/config.php`
- Verify database exists in phpMyAdmin
- Ensure database user has proper permissions

### Problem: Email notifications not working
**Solution:**
- Verify ADMIN_EMAIL is correct in `api/config.php`
- Check your spam/junk folder
- Contact iPage support to ensure mail() function is enabled

### Problem: Blank page or errors
**Solution:**
- Enable error display temporarily in `api/config.php`:
  ```php
  ini_set('display_errors', 1);
  ```
- Check PHP error logs in iPage control panel
- Verify all files uploaded correctly

### Problem: Images not loading
**Solution:**
- Ensure `invite_image.png` is in the root directory
- Check file name matches exactly (case-sensitive)
- Verify file uploaded successfully

### Problem: Can't access admin panel
**Solution:**
- Ensure you're using the correct password from `api/config.php`
- Clear browser cookies/cache
- Try accessing in incognito/private window

---

## 📧 Email Notification Setup

The system uses PHP's built-in `mail()` function. iPage should have this enabled by default, but if emails aren't sending:

1. Contact iPage support to verify mail functionality
2. Consider using SMTP (requires additional PHP library)
3. Check email doesn't go to spam folder

---

## 🔐 Security Recommendations

1. **Change default admin password** immediately in `api/config.php`
2. **Use HTTPS** - Set up SSL certificate through iPage (usually free)
3. **Keep database credentials secure** - Never commit to public repositories
4. **Regular backups** - Back up database regularly through phpMyAdmin
5. **Update display_errors** - Set to 0 in production:
   ```php
   ini_set('display_errors', 0);
   ```

---

## 📱 Accessing Your Site

- **Invitation Page:** `http://lonkar.in`
- **Admin Panel:** `http://lonkar.in/admin/view_rsvps.php`

---

## 🎨 Customization

### Change Colors
Edit `css/style.css` and modify color values:
- Primary color: `#3498db` (blue)
- Accept button: `#27ae60` (green)
- Decline button: `#e74c3c` (red)

### Update Invitation Text
Edit `index.html` and modify the text in the `.invitation-content` section

### Change Email Template
Edit `api/rsvp.php` and modify the `$email_body` HTML

---

## 📊 Viewing RSVPs

1. Go to: `http://lonkar.in/admin/view_rsvps.php`
2. Enter your admin password
3. View statistics:
   - Total RSVPs
   - Accepted count
   - Declined count
   - Total guests attending
4. Filter by Accept/Decline
5. Search by name, email, or phone

---

## 📁 File Structure Reference

```
housewarming-invite/
├── index.html              # Main invitation page
├── invite_image.png        # Header image
├── requirements.txt        # Python deps (not needed for PHP)
├── css/
│   └── style.css          # All styling
├── js/
│   └── script.js          # Form validation & AJAX
├── api/
│   ├── config.php         # Database & email config
│   ├── rsvp.php           # RSVP processing
│   └── database.sql       # Database schema
└── admin/
    └── view_rsvps.php     # Admin panel
```

---

## ✅ Post-Deployment Checklist

- [ ] Database created and schema imported
- [ ] `api/config.php` updated with correct credentials
- [ ] All files uploaded to iPage
- [ ] `invite_image.png` displays correctly
- [ ] Test RSVP submission works
- [ ] Email notification received
- [ ] Admin panel accessible with password
- [ ] Mobile responsiveness tested
- [ ] SSL certificate installed (HTTPS)
- [ ] Error display turned off in production

---

## 🆘 Support

If you encounter issues:
1. Check the Troubleshooting section above
2. Review PHP error logs in iPage control panel
3. Contact iPage support for hosting-related issues
4. Verify all configuration values are correct

---

## 🎉 You're All Set!

Your housewarming invitation website is now live! Share `http://lonkar.in` with your guests and track RSVPs through the admin panel.

**Remember:** RSVP deadline is December 25th. Party is January 3rd at 12:00 PM!
