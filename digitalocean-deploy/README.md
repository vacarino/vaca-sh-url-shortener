# 🚀 Vaca.Sh DigitalOcean VPS Deployment Guide

Complete production deployment package for **Vaca.Sh v2.0** Laravel URL Shortener on DigitalOcean Ubuntu 22.04 VPS.

## 📋 Prerequisites

Before deploying, ensure you have:

- **DigitalOcean Account** with billing set up
- **Domain name** (`vaca.sh`) purchased and ready to configure
- **GitHub Repository** with your Laravel code pushed
- **SSH Key** configured in your DigitalOcean account

## 🏗️ Step 1: Create DigitalOcean Droplet

### Create New Droplet

1. **Log into DigitalOcean Dashboard**
   - Go to [DigitalOcean Control Panel](https://cloud.digitalocean.com/)

2. **Create Droplet**
   - Click **"Create"** → **"Droplets"**

3. **Choose Image**
   - **Distribution**: Ubuntu 22.04 (LTS) x64

4. **Choose Size**
   - **Basic Plan**: $6/month (1GB RAM, 1 vCPU, 25GB SSD)
   - **Recommended**: $12/month (2GB RAM, 1 vCPU, 50GB SSD)

5. **Add Block Storage** (Optional)
   - Skip for basic setup

6. **Choose Datacenter**
   - Select region closest to your target audience

7. **Authentication**
   - **SSH Keys** (Recommended) - Select your uploaded SSH key
   - OR **Password** - Use strong password

8. **Finalize Details**
   - **Hostname**: `vaca-sh-production`
   - **Tags**: `vaca-sh`, `production`, `laravel`

9. **Create Droplet**
   - Click **"Create Droplet"**
   - Wait 2-3 minutes for creation

## 🌐 Step 2: Configure DNS

### Point Domain to Droplet

1. **Get Droplet IP Address**
   - Copy the Public IP from DigitalOcean dashboard (e.g., `165.227.123.45`)

2. **Configure DNS Records** (at your domain registrar)
   ```
   Type: A Record
   Name: @ (or leave blank)
   Value: YOUR_DROPLET_IP
   TTL: 300 (5 minutes)

   Type: A Record  
   Name: www
   Value: YOUR_DROPLET_IP
   TTL: 300 (5 minutes)
   ```

3. **Wait for DNS Propagation**
   - Can take 5 minutes to 24 hours
   - Test with: `ping vaca.sh`

## 📝 Step 3: Prepare GitHub Repository

### Update Repository URL in Setup Script

1. **Edit `setup.sh`**:
   ```bash
   # Find line 20 and update with your actual GitHub repository
   GITHUB_REPO="https://github.com/YOUR_USERNAME/vaca-sh-url-shortener.git"
   ```

2. **Push Changes to GitHub**:
   ```bash
   git add .
   git commit -m "Add DigitalOcean deployment configuration"
   git push origin main
   ```

## 🔧 Step 4: Connect to Your Droplet

### SSH Connection

```bash
# Replace YOUR_DROPLET_IP with actual IP address
ssh root@YOUR_DROPLET_IP

# If using SSH key authentication (recommended)
ssh -i ~/.ssh/your_key root@YOUR_DROPLET_IP
```

### Create Non-Root User (Security Best Practice)

```bash
# Create new user
adduser deploy
usermod -aG sudo deploy

# Copy SSH keys (if using SSH authentication)
rsync --archive --chown=deploy:deploy ~/.ssh /home/deploy

# Switch to new user
su - deploy
```

## 🚀 Step 5: Run Automated Setup

### Download and Execute Setup Script

```bash
# Clone your repository (temporary - just to get the setup script)
git clone https://github.com/YOUR_USERNAME/vaca-sh-url-shortener.git
cd vaca-sh-url-shortener

# Make setup script executable
chmod +x digitalocean-deploy/setup.sh

# Run the complete setup (takes 10-15 minutes)
./digitalocean-deploy/setup.sh
```

### What the Setup Script Does

The automated setup will:

1. ✅ **Update system packages**
2. ✅ **Install PHP 8.2 + required extensions**
3. ✅ **Install and secure MySQL 8.0**
4. ✅ **Install Nginx web server**
5. ✅ **Install Composer (PHP dependency manager)**
6. ✅ **Install Node.js 18 + NPM**
7. ✅ **Clone your application to `/var/www/vaca.sh`**
8. ✅ **Install Laravel dependencies**
9. ✅ **Build frontend assets**
10. ✅ **Configure environment variables**
11. ✅ **Set proper file permissions**
12. ✅ **Configure PHP-FPM**
13. ✅ **Setup Nginx virtual host**
14. ✅ **Install SSL certificate tools**
15. ✅ **Run database migrations**
16. ✅ **Seed admin user and invite codes**
17. ✅ **Configure firewall security**
18. ✅ **Start all services**
19. ✅ **Setup automated backups**
20. ✅ **Optimize for production**

## 🔒 Step 6: Enable HTTPS (SSL Certificate)

### Install Let's Encrypt SSL Certificate

```bash
# Run after DNS has propagated and site is accessible via HTTP
sudo certbot --nginx -d vaca.sh -d www.vaca.sh

# Follow the prompts:
# 1. Enter email address for urgent renewal notices
# 2. Agree to terms of service (A)
# 3. Choose whether to share email with EFF (Y/N)
# 4. Certbot will automatically configure HTTPS redirect
```

### Verify SSL Installation

- Visit `https://vaca.sh` - should show secure connection
- Check SSL rating at [SSL Labs](https://www.ssllabs.com/ssltest/)

## 🎯 Step 7: Access Your Application

### Application URLs

- **Website**: `https://vaca.sh`
- **Admin Panel**: `https://vaca.sh/admin`

### Default Admin Credentials

```
Email: admin@vaca.sh
Password: password
```

**⚠️ IMPORTANT**: Change this password immediately after first login!

### Invite Codes for User Registration

Users need invite codes to register. Default codes:
- `WELCOME2024`
- `BETA_TEST`
- `FRIEND_INVITE`

## 🛠️ Step 8: Post-Deployment Configuration

### Essential Security Steps

1. **Change Admin Password**
   ```bash
   # Via web interface: https://vaca.sh/admin/profile
   # Or via command line:
   cd /var/www/vaca.sh
   php artisan tinker
   >>> $admin = User::where('email', 'admin@vaca.sh')->first();
   >>> $admin->password = Hash::make('your-new-secure-password');
   >>> $admin->save();
   ```

2. **Configure Email Settings** (Optional)
   ```bash
   # Edit .env file
   sudo nano /var/www/vaca.sh/.env
   
   # Update email configuration
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email@vaca.sh
   MAIL_PASSWORD=your-email-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=hello@vaca.sh
   ```

3. **Setup Monitoring** (Optional)
   ```bash
   # Install system monitoring
   sudo apt install htop iotop nethogs
   
   # Check system resources
   htop
   ```

## 🔄 Step 9: Deploy Updates

### Using the Deployment Script

```bash
# Navigate to application directory
cd /var/www/vaca.sh

# Run deployment script
./digitalocean-deploy/deploy.sh
```

### Manual Deployment Steps

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Pull latest changes
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Fix permissions
sudo chown -R www-data:www-data /var/www/vaca.sh
sudo chmod -R 775 /var/www/vaca.sh/storage
sudo chmod -R 775 /var/www/vaca.sh/bootstrap/cache

# 7. Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

# 8. Disable maintenance mode
php artisan up
```

## 📊 Monitoring and Maintenance

### Important Log Files

```bash
# Application logs
tail -f /var/www/vaca.sh/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/vaca.sh-access.log
tail -f /var/log/nginx/vaca.sh-error.log

# System logs
tail -f /var/log/syslog
```

### Service Status Checks

```bash
# Check service status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# Restart services if needed
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
```

### Automated Backups

Backups run automatically at 2:00 AM daily:

```bash
# Manual backup
sudo /usr/local/bin/vaca-backup.sh

# View backup files
ls -la /backups/

# Restore from backup (if needed)
# Database restore:
mysql -u vacauser -p vaca_sh < /backups/database_YYYYMMDD_HHMMSS.sql

# Application restore:
cd /var/www
tar -xzf /backups/app_YYYYMMDD_HHMMSS.tar.gz
```

### Performance Optimization

```bash
# Enable OPcache status (optional)
echo "<?php phpinfo(); ?>" | sudo tee /var/www/vaca.sh/public/phpinfo.php

# Check OPcache status at: https://vaca.sh/phpinfo.php
# Delete afterwards: sudo rm /var/www/vaca.sh/public/phpinfo.php

# Monitor server resources
htop
df -h
free -m
```

## 🚨 Troubleshooting

### Common Issues and Solutions

#### Issue: Website shows "502 Bad Gateway"
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Check Nginx configuration
sudo nginx -t
```

#### Issue: Database connection errors
```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
cd /var/www/vaca.sh
php artisan tinker
>>> DB::connection()->getPdo();
```

#### Issue: File permission errors
```bash
# Fix Laravel permissions
cd /var/www/vaca.sh
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

#### Issue: SSL certificate renewal
```bash
# Test renewal
sudo certbot renew --dry-run

# Force renewal if needed
sudo certbot renew --force-renewal
```

### Getting Help

- **Laravel Documentation**: https://laravel.com/docs
- **DigitalOcean Tutorials**: https://www.digitalocean.com/community/tutorials
- **Nginx Documentation**: https://nginx.org/en/docs/

## 📈 Scaling Your Application

### Upgrading Droplet Resources

1. **Via DigitalOcean Dashboard**:
   - Navigate to your droplet
   - Click **"Resize"**
   - Choose new plan (requires reboot)

2. **Database Optimization**:
   ```bash
   # Optimize MySQL configuration
   sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
   
   # Add under [mysqld] section:
   innodb_buffer_pool_size = 512M
   query_cache_size = 64M
   max_connections = 200
   ```

3. **Add Caching (Redis)**:
   ```bash
   # Install Redis
   sudo apt install redis-server
   
   # Update .env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

## 🔐 Security Hardening

### Additional Security Measures

1. **Fail2Ban Configuration**:
   ```bash
   # Configure fail2ban for SSH
   sudo nano /etc/fail2ban/jail.local
   
   [sshd]
   enabled = true
   port = ssh
   filter = sshd
   logpath = /var/log/auth.log
   maxretry = 3
   bantime = 3600
   ```

2. **Regular Updates**:
   ```bash
   # Setup automatic security updates
   sudo apt install unattended-upgrades
   sudo dpkg-reconfigure unattended-upgrades
   ```

3. **Firewall Rules**:
   ```bash
   # View current rules
   sudo ufw status verbose
   
   # Add custom rules if needed
   sudo ufw deny from BAD_IP_ADDRESS
   ```

## ✅ Deployment Checklist

- [ ] DigitalOcean droplet created
- [ ] DNS records configured and propagated
- [ ] SSH access established
- [ ] Non-root user created
- [ ] Setup script executed successfully
- [ ] SSL certificate installed
- [ ] Admin password changed
- [ ] Application accessible at https://vaca.sh
- [ ] Admin panel accessible at https://vaca.sh/admin
- [ ] Email configuration tested (if applicable)
- [ ] Backup script tested
- [ ] Deployment script tested
- [ ] Monitoring setup complete

## 🎉 Congratulations!

Your **Vaca.Sh v2.0** Laravel URL shortener is now live and running on DigitalOcean!

**Next Steps:**
- Test all functionality thoroughly
- Set up monitoring and alerts
- Plan for regular backups and updates
- Consider implementing additional features
- Monitor performance and scale as needed

---

**Need Support?** Contact your development team or refer to the official Laravel and DigitalOcean documentation. 