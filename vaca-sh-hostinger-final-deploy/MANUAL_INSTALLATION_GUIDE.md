# Vaca.Sh - Manual Installation Guide for WPX.net

## 🚀 Complete Manual Setup Instructions (No install.php required)

### **Prerequisites**
- WPX.net shared hosting account
- PHP 8.1+ enabled
- MySQL database access
- phpMyAdmin access
- Local PHP environment to generate APP_KEY

---

## **Step 1: Prepare Your Local Environment**

### **1.1 Generate APP_KEY Locally**
On your local machine with PHP installed:
```bash
# Option 1: If you have Laravel/artisan locally
php artisan key:generate --show

# Option 2: Use online generator or this PHP snippet
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```
**Copy the generated key** (should look like: `base64:abc123def456...`)

### **1.2 Export Database Structure**
From your local development environment:
```bash
# Export database structure and data
mysqldump -u username -p database_name > vaca_sh_database.sql
```
Or export via phpMyAdmin → Export → SQL format

---

## **Step 2: WPX.net Setup**

### **2.1 Create Database**
1. Login to **WPX Control Panel**
2. Go to **MySQL Databases**
3. Create new database: `yourusername_vacash`
4. Create database user with full privileges
5. **Note down**: Database name, username, password, host

### **2.2 Upload Files**
1. **Extract** the `vaca.sh.zip` file locally
2. **Upload ALL contents** to: `/home/yourusername/domains/vaca.sh/public_html/`

**Final structure should be:**
```
public_html/
├── index.php           ← Laravel entry point
├── .htaccess           ← URL rewrite rules  
├── build/              ← Compiled assets
├── css/                ← Static files
├── js/                 ← Static files
├── app/                ← Laravel app
├── bootstrap/          ← Laravel bootstrap
├── config/             ← Configuration
├── database/           ← Migrations
├── resources/          ← Views
├── routes/             ← Routes
├── storage/            ← Storage
├── vendor/             ← Dependencies
├── .env.template       ← Environment template
├── artisan
├── composer.json
└── composer.lock
```

---

## **Step 3: Database Import**

### **3.1 Import via phpMyAdmin**
1. Access **phpMyAdmin** from WPX control panel
2. Select your database: `yourusername_vacash`
3. Click **Import** tab
4. Choose your `vaca_sh_database.sql` file
5. Click **Go** to import

### **3.2 Verify Tables Created**
Ensure these tables exist:
- `users`
- `short_urls` 
- `click_logs`
- `invite_codes`
- `migrations`

---

## **Step 4: Environment Configuration**

### **4.1 Create .env File**
1. **Copy** `.env.template` to `.env`
2. **Edit** the `.env` file with your details:

```env
APP_NAME=Vaca.Sh
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://vaca.sh

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_vacash
DB_USERNAME=yourusername_dbuser
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

### **4.2 Important Notes**
- Replace `YOUR_GENERATED_KEY_HERE` with the key from Step 1.1
- Replace database credentials with values from Step 2.1
- Ensure `APP_URL=https://vaca.sh` (your actual domain)

---

## **Step 5: Set Permissions**

### **5.1 Directory Permissions**
In WPX File Manager, set permissions to **755** for:
- `storage/` (and all subdirectories)
- `storage/app/`
- `storage/framework/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`
- `bootstrap/cache/`

### **5.2 File Permissions**
Set **644** for all files in storage directories.

---

## **Step 6: Test Installation**

### **6.1 Verify Website**
1. Visit: `https://vaca.sh`
2. Should show Vaca.Sh landing page
3. Test navigation and asset loading

### **6.2 Test Admin Login**
1. Visit: `https://vaca.sh/login`
2. Login with default credentials:
   - Email: `admin@vaca.sh`
   - Password: `password`
3. **IMMEDIATELY change these credentials!**

### **6.3 Test Core Features**
- ✅ URL shortening works
- ✅ Analytics dashboard loads
- ✅ Admin panel accessible at `/admin/users`
- ✅ QR code generation works
- ✅ Click tracking functional

---

## **Step 7: Security & Cleanup**

### **7.1 Change Default Credentials**
1. Login as admin
2. Go to Profile/Settings
3. Change email and password immediately

### **7.2 Optional Cleanup**
You can remove these files if not needed:
- `.env.template`
- `MANUAL_INSTALLATION_GUIDE.md`

---

## **🔧 Troubleshooting**

### **Problem: Blank Page**
- Check WPX error logs
- Verify `.env` file exists and has correct database credentials
- Ensure `storage/` directories are writable (755)

### **Problem: "No Application Encryption Key"**
- Verify `APP_KEY` in `.env` starts with `base64:`
- Generate new key locally and update `.env`

### **Problem: Database Connection Error**
- Double-check database credentials in `.env`
- Verify database exists and user has privileges
- Try `127.0.0.1` instead of `localhost` for DB_HOST

### **Problem: Assets Not Loading**
- Check that `build/`, `css/`, `js/` directories uploaded correctly
- Verify `.htaccess` exists in root directory
- Clear browser cache

### **Problem: Routes Not Working**
- Ensure `.htaccess` exists in root (`public_html/`)
- Verify mod_rewrite is enabled (default on WPX)
- Check file permissions on `.htaccess` (644)

---

## **📞 Support Resources**

**WPX-Specific Issues:**
- Contact WPX support for server configuration
- They can check PHP version, modules, and permissions
- They can assist with database connection issues

**Application Issues:**
- Check error logs in `storage/logs/laravel.log`
- Verify all files uploaded correctly
- Ensure database tables imported successfully

---

## **🎯 Success Checklist**

- [ ] Database created and imported successfully
- [ ] All files uploaded to `public_html/`
- [ ] `.env` file created with correct credentials
- [ ] APP_KEY generated and added to `.env`
- [ ] Directory permissions set (755 for storage/)
- [ ] Website loads at `https://vaca.sh`
- [ ] Admin login works
- [ ] URL shortening functional
- [ ] Analytics dashboard accessible
- [ ] Default credentials changed

---

## **🌟 Benefits of Manual Installation**

✅ **No server-side commands required**  
✅ **Full control over environment setup**  
✅ **Works on any shared hosting**  
✅ **No dependency on exec() or shell_exec()**  
✅ **Step-by-step verification process**  
✅ **Compatible with WPX.net and similar hosts**

---

**Vaca.Sh** is now ready for production use! 🎉 