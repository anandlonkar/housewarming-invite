# Housewarming Invitation Website

A beautiful, self-contained invitation website for your housewarming party with RSVP tracking and email notifications.

## Features
- 🎨 Beautiful invitation display with custom header image
- ✅ RSVP form with Accept/Decline options
- 📧 Automatic email notifications for each RSVP
- 💾 MySQL database storage
- 🔐 Password-protected admin panel
- 📱 Fully responsive (mobile-friendly)
- 🚀 Easy deployment to iPage hosting

## Quick Start

1. **Configure Database & Email**
   - Open `api/config.php`
   - Update database credentials
   - Set your email address for notifications
   - Set admin password

2. **Deploy to iPage**
   - Create MySQL database
   - Import `api/database.sql`
   - Upload all files via FTP or File Manager
   - See `DEPLOYMENT.md` for detailed instructions

3. **Access Your Site**
   - Invitation: `http://lonkar.in`
   - Admin Panel: `http://lonkar.in/admin/view_rsvps.php`

## Technology Stack
- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP (native to iPage)
- **Database:** MySQL
- **Email:** PHP mail() function

## What Guests See
- Event details (date, time, location)
- Custom header image
- RSVP form collecting:
  - Name (required)
  - Email address
  - Phone number
  - Number of guests
  - Comments/message
- Accept or Decline buttons

## Admin Features
- View all RSVPs in real-time
- Filter by Accept/Decline
- Search by name, email, or phone
- Statistics dashboard:
  - Total RSVPs
  - Acceptance count
  - Decline count
  - Total guests attending

## File Structure
```
housewarming-invite/
├── index.html              # Main invitation page
├── invite_image.png        # Header image
├── css/style.css          # Styling
├── js/script.js           # Form handling
├── api/
│   ├── config.php         # Configuration
│   ├── rsvp.php           # RSVP processing
│   └── database.sql       # Database schema
└── admin/
    └── view_rsvps.php     # Admin panel
```

## Party Details
- **When:** Saturday, January 3rd at 12:00 PM
- **Where:** 10964 Hollydale Ln, Frisco, TX 75035
- **RSVP By:** December 25th

## Need Help?
See `DEPLOYMENT.md` for complete deployment instructions and troubleshooting.

---

**Hosts:** Anand Lonkar and Poornima Chand
