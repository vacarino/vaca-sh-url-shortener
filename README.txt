# VacaSh Laravel - Hetzner VPS Deployment Guide

## 🚀 One-Click Production Deployment for Ubuntu 22.04

This package contains everything needed to deploy VacaSh Laravel URL shortener 
on a fresh Hetzner VPS with Ubuntu 22.04.

## 📦 Package Contents

- `vaca.sh/` - Complete Laravel application
- `setup.sh` - Automated deployment script
- `README.txt` - This instruction file

## 🎯 Quick Deployment (5 minutes)

### Prerequisites
- Fresh Ubuntu 22.04 Hetzner VPS
- Root or sudo access
- Domain name (vaca.sh) pointing to your VPS IP

### Step 1: Upload and Extract
```bash
# Upload the hetzner.zip to your VPS
scp hetzner.zip user@your-server-ip:~/

# Connect to your VPS
ssh user@your-server-ip

# Extract the package
unzip hetzner.zip
cd hetzner
```

### Step 2: Run Deployment Script
```bash
# Make the script executable
chmod +x setup.sh

# Run the deployment (takes 3-5 minutes)
bash setup.sh
```

### Step 3: Update DNS Records
Point your domain to your VPS IP address:
- A record: vaca.sh → YOUR_VPS_IP
- A record: www.vaca.sh → YOUR_VPS_IP

## ✅ What the Script Does

### System Setup
- ✅ Updates Ubuntu 22.04 packages
- ✅ Installs Nginx web server
- ✅ Installs PHP 8.2 with all Laravel extensions
- ✅ Installs MySQL 8.0 server
- ✅ Installs Composer and Node.js

### Database Configuration
- ✅ Creates secure MySQL installation
- ✅ Creates database: `vaca_sh`
- ✅ Creates user: `vacauser` / `Durimi,.123`
- ✅ Sets proper privileges

### Laravel Setup
- ✅ Installs dependencies with Composer
- ✅ Configures .env for production
- ✅ Generates application key
- ✅ Runs database migrations
- ✅ Optimizes for production (caching)
- ✅ Sets correct file permissions

### Web Server Configuration  
- ✅ Configures Nginx for Laravel
- ✅ Sets up proper PHP-FPM pool
- ✅ Enables gzip compression
- ✅ Configures asset caching

### Security & SSL
- ✅ Installs Let's Encrypt SSL certificate
- ✅ Configures automatic SSL renewal
- ✅ Sets up UFW firewall
- ✅ Applies security hardening

### Production Features
- ✅ Automatic daily backups
- ✅ Laravel task scheduler (cron)
- ✅ Log rotation
- ✅ Performance optimizations

## 🔧 Post-Deployment

### Access Your Application
- **Website**: https://vaca.sh
- **Admin Panel**: https://vaca.sh/admin
- **Login**: admin@vaca.sh / password

### Default Database Credentials
- **Host**: localhost
- **Database**: vaca_sh  
- **Username**: vacauser
- **Password**: Durimi,.123

### Important Directories
- **Application**: `/var/www/vaca.sh`
- **Nginx Config**: `/etc/nginx/sites-available/vaca.sh`
- **SSL Certificates**: `/etc/letsencrypt/live/vaca.sh/`
- **Logs**: `/var/www/vaca.sh/storage/logs/`

## 🛠️ Management Commands

### Service Control
```bash
# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql

# Check service status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm  
sudo systemctl status mysql
```

### Laravel Commands
```bash
cd /var/www/vaca.sh

# Clear caches
sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan view:clear

# Run migrations
sudo php artisan migrate

# Check application status
sudo php artisan about
```

### SSL Certificate Management
```bash
# Check certificate status
sudo certbot certificates

# Renew certificate manually
sudo certbot renew

# Test renewal process
sudo certbot renew --dry-run
```

### Backup Management
```bash
# Manual backup
sudo /usr/local/bin/vaca-backup.sh

# View backups
ls -la /backups/

# Restore database from backup
mysql -u vacauser -pDurimi,.123 vaca_sh < /backups/database_YYYYMMDD_HHMMSS.sql
```

## 🔍 Troubleshooting

### Website Not Loading
1. Check Nginx status: `sudo systemctl status nginx`
2. Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
3. Check Nginx error logs: `sudo tail -f /var/log/nginx/error.log`
4. Check Laravel logs: `sudo tail -f /var/www/vaca.sh/storage/logs/laravel.log`

### Database Connection Issues
1. Check MySQL status: `sudo systemctl status mysql`
2. Test database connection:
   ```bash
   mysql -u vacauser -pDurimi,.123 -e "SELECT 1;"
   ```
3. Check Laravel database config: `cat /var/www/vaca.sh/.env | grep DB_`

### SSL Certificate Issues
1. Check certificate status: `sudo certbot certificates`
2. Check Nginx SSL config: `sudo nginx -t`
3. Renew certificate: `sudo certbot renew`

### Permission Issues
```bash
# Fix file permissions
cd /var/www/vaca.sh
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

## 📊 Performance Monitoring

### System Resources
```bash
# Check memory usage
free -h

# Check disk usage  
df -h

# Check CPU usage
htop

# Check active connections
sudo netstat -tulnp | grep :80
sudo netstat -tulnp | grep :443
```

### Application Performance
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Monitor PHP-FPM pool
sudo tail -f /var/log/php8.2-fpm.log

# Check Laravel queue (if using)
cd /var/www/vaca.sh
sudo php artisan queue:work --daemon
```

## 🔒 Security Best Practices

### Regular Updates
```bash
# Update system packages monthly
sudo apt update && sudo apt upgrade -y

# Update Composer dependencies
cd /var/www/vaca.sh
sudo composer update --no-dev
```

### Monitor Logs
```bash
# Check access logs
sudo tail -f /var/log/nginx/access.log

# Check error logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/vaca.sh/storage/logs/laravel.log
```

### Firewall Management
```bash
# Check firewall status
sudo ufw status

# Allow new services (if needed)
sudo ufw allow [port/service]

# Check failed login attempts
sudo tail -f /var/log/auth.log
```

## 📞 Support

### Configuration Files
- **Nginx**: `/etc/nginx/sites-available/vaca.sh`
- **PHP**: `/etc/php/8.2/fpm/conf.d/99-laravel.ini`
- **Laravel**: `/var/www/vaca.sh/.env`
- **MySQL**: `/etc/mysql/mysql.conf.d/mysqld.cnf`

### Log Files
- **Nginx Access**: `/var/log/nginx/access.log`
- **Nginx Error**: `/var/log/nginx/error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **Laravel**: `/var/www/vaca.sh/storage/logs/laravel.log`
- **MySQL**: `/var/log/mysql/error.log`

## 🎉 Success Indicators

Your deployment is successful when:
- ✅ https://vaca.sh loads without errors
- ✅ SSL certificate is valid (green lock icon)
- ✅ User registration/login works
- ✅ URL shortening functionality works
- ✅ Admin panel is accessible
- ✅ Database operations are working
- ✅ All services are running and enabled

## 💡 Production Tips

1. **Regular Backups**: Automated daily backups are configured
2. **SSL Renewal**: Automatic renewal is set up via cron
3. **Performance**: OPcache and various optimizations are enabled
4. **Security**: Firewall, secure headers, and hardening applied
5. **Monitoring**: Log rotation and system monitoring ready

---

**Deployment Package**: VacaSh Laravel v2.0
**Target OS**: Ubuntu 22.04 LTS
**Web Server**: Nginx 1.18+
**PHP**: 8.2 with FPM
**Database**: MySQL 8.0
**SSL**: Let's Encrypt with auto-renewal
**Deployment Time**: ~5 minutes

*Production-ready Laravel deployment with enterprise-grade configuration!* 🚀 