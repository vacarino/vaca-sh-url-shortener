# Vaca.Sh v2.0 - Production Deployment Guide

## 📦 What's Included

This `vaca.sh.zip` contains a production-ready Laravel 10+ URL shortener application with:

- ✅ **Complete Laravel Application** - All core files and dependencies
- ✅ **Production Assets** - Compiled CSS/JS with Vite
- ✅ **Standalone Installer** - `install.php` for easy setup
- ✅ **Admin System** - User management and invite codes
- ✅ **Analytics Dashboard** - Click tracking and statistics
- ✅ **QR Code Generation** - Built-in QR codes for short URLs

## 🚀 Quick Deployment (WPX.net & Shared Hosting)

### Step 1: Upload Files
1. Extract `vaca.sh.zip` to your local computer
2. Upload the entire `url-shortener/` folder contents to your hosting directory
   - For subdomain: Upload to `public_html/` 
   - For subdirectory: Upload to `public_html/vaca/` or similar

### Step 2: Run Installer
1. Visit: `https://your-domain.com/install.php?code=setup123`
2. Fill out the installation form:
   - **App URL**: Your domain (e.g., `https://vaca.sh`)
   - **Database Details**: Host, name, username, password from your hosting panel
3. Click "Install Vaca.Sh"
4. Delete `install.php` when prompted for security

### Step 3: Login & Configure
1. Visit: `https://your-domain.com/login`
2. Default admin credentials:
   - **Email**: `admin@vaca.sh`
   - **Password**: `password`
3. **IMPORTANT**: Change these credentials immediately!

## 🔧 Manual Setup (If installer fails)

If the automatic installer doesn't work:

1. **Create .env file** manually from `.env.example.production`
2. **Run commands** via hosting terminal/SSH:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan config:cache
   ```
3. **Set permissions** on `storage/` and `bootstrap/cache/` to 775

## 📋 System Requirements

- **PHP**: 8.1 or higher
- **MySQL**: 5.7+ or MariaDB 10.3+
- **Extensions**: PDO, mbstring, OpenSSL, tokenizer, ctype, JSON
- **Composer**: Dependencies already included

## 🎯 Features Ready to Use

- **URL Shortening**: Create custom short codes
- **Analytics**: Track clicks, countries, referrers
- **QR Codes**: Automatic generation for all URLs
- **Admin Panel**: User management at `/admin/users`
- **Invite System**: Controlled registration at `/admin/invite-codes`
- **Dark Mode**: Built-in theme switching
- **Responsive**: Mobile-friendly design

## 🔐 Security Notes

- Change default admin credentials immediately
- Delete `install.php` after setup
- Use strong database passwords
- Enable HTTPS on your domain

## 📞 Support

For issues or questions:
- Check hosting error logs
- Verify PHP version and extensions
- Ensure database credentials are correct
- Contact your hosting provider for server-specific help

---

**Vaca.Sh v2.0** - Professional URL Shortener
Ready for production deployment on any shared hosting platform. 