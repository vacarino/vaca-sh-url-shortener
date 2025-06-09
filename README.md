# Professional URL Shortener

A full-stack web application similar to Bitly, built with Laravel (PHP) and MySQL. This application provides URL shortening services with comprehensive analytics, user management, and role-based access control.

## Features

### 🔗 URL Shortening
- Generate unique 6-character short codes
- Support for custom expiration dates
- Automatic duplicate prevention
- Clean, professional short URLs (`/r/{code}`)

### 📊 Analytics Dashboard
- Real-time click tracking
- Visitor analytics (IP, country, browser, platform)
- Geographic distribution insights
- Time-based analytics
- Paginated click logs

### 👥 User Management
- Laravel Breeze authentication
- Role-based access control (Admin/Collaborator)
- User-specific URL management
- Admin oversight capabilities

### 🛡️ Security & Validation
- URL format validation
- CSRF protection
- Role-based authorization
- Secure IP detection
- Input sanitization

### 🎨 Modern UI
- Responsive Tailwind CSS design
- Clean, professional interface
- Interactive elements with Alpine.js
- Mobile-friendly layout

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - enum('admin', 'collaborator')
- `created_at`, `updated_at` - Timestamps

### Short URLs Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `original_url` - The long URL to redirect to
- `short_code` - Unique 6-character identifier
- `clicks` - Click counter
- `expires_at` - Optional expiration timestamp
- `created_at`, `updated_at` - Timestamps

### Click Logs Table
- `id` - Primary key
- `short_url_id` - Foreign key to short_urls
- `ip_address` - Visitor's IP address
- `user_agent` - Browser user agent string
- `country` - Detected country from IP
- `browser` - Parsed browser name
- `platform` - Parsed operating system
- `created_at`, `updated_at` - Timestamps

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js & NPM (for asset compilation)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd url-shortener
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=url_shortener
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Create an admin user (optional)**
   ```bash
   php artisan tinker
   ```
   Then in the tinker console:
   ```php
   $user = new App\Models\User();
   $user->name = 'Admin User';
   $user->email = 'admin@example.com';
   $user->password = bcrypt('password');
   $user->role = 'admin';
   $user->save();
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   Open your browser and navigate to `http://localhost:8000`

## Usage

### For Regular Users (Collaborators)
1. Register for an account or log in
2. Navigate to "Shorten URL" to create new short links
3. View your dashboard to manage existing URLs
4. Click "View Details" on any URL to see analytics
5. Copy short URLs to share them

### For Administrators
- Access all users' URLs and analytics
- View comprehensive system-wide statistics
- Manage user accounts and permissions
- Monitor application usage

### API Endpoints

#### Public Routes
- `GET /` - Landing page
- `GET /r/{code}` - Redirect to original URL
- `GET /login` - Login page
- `GET /register` - Registration page

#### Protected Routes (Authenticated Users)
- `GET /urls` - Dashboard with user's URLs
- `GET /urls/create` - Create new short URL form
- `POST /urls` - Store new short URL
- `GET /urls/{id}` - View URL analytics
- `DELETE /urls/{id}` - Delete short URL

## Configuration

### GeoIP Service
The application uses a free GeoIP API for country detection. For production use, consider:
- MaxMind GeoIP2 database
- IPinfo.io API
- Other commercial GeoIP services

### Caching
For better performance in production:
- Enable Redis for session storage
- Configure database query caching
- Use CDN for static assets

### Queue System
For high-traffic applications:
- Configure queue workers for analytics processing
- Use Redis or database queues
- Process click logs asynchronously

## Security Considerations

### Production Deployment
- Use HTTPS in production
- Configure proper CORS settings
- Set up rate limiting
- Enable SQL injection protection
- Configure proper file permissions

### Environment Variables
- Generate strong APP_KEY
- Use secure database credentials
- Configure mail settings for notifications
- Set proper session configuration

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please create an issue in the GitHub repository or contact the development team.

---

Built with ❤️ using Laravel, Tailwind CSS, and Alpine.js 