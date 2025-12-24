# Security Configuration

## Setup Instructions

1. Copy `api/config.example.php` to `api/config.php`
2. Update `api/config.php` with your actual database credentials and passwords
3. Never commit `api/config.php` to version control (it's already in .gitignore)

## Important Notes

- `api/config.php` contains sensitive credentials and is excluded from git
- Keep your database passwords secure and use strong passwords
- The example file shows the required structure without exposing real credentials
- Make sure `api/config.php` exists on your server with the correct credentials for the application to work
