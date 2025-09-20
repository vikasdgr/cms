# Docker Setup for CMS Application

This Docker configuration provides a complete development environment for the Laravel CMS application with Vue.js frontend.

## Quick Start

1. **Clone the repository and navigate to the project directory**
   ```bash
   cd D:\RSSB\cms
   ```

2. **Start all services**
   ```bash
   docker compose up -d
   ```

   This single command will:
   - Build and start all containers
   - Set up the MySQL database
   - Install PHP/Laravel dependencies
   - Install Node.js/Vue dependencies
   - Configure Laravel environment
   - Run database migrations
   - Start Vite development server

3. **Access the application**
   - **Main Application**: http://localhost:8000
   - **Vite Dev Server (HMR)**: http://localhost:5173
   - **Database Admin (Adminer)**: http://localhost:8080
     - Server: `db`
     - Username: `laravel_user`
     - Password: `secret`
     - Database: `cms`

## Services

### App (Laravel/PHP)
- **Port**: 8000
- **Features**: 
  - Automatic environment setup
  - Database connection waiting
  - Migration execution
  - Composer dependency installation
  - Laravel key generation

### Database (MySQL)
- **Port**: 3306
- **Database**: cms
- **Username**: laravel_user
- **Password**: secret
- **Root Password**: root_secret

### Node.js (Vite Development Server)
- **Port**: 5173
- **Features**:
  - Automatic npm dependency installation
  - Vite configuration for Docker
  - Hot Module Replacement (HMR)
  - Cache clearing on restart

### Adminer (Database Management)
- **Port**: 8080
- Web-based MySQL administration tool

## Commands

### Basic Operations
```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f

# Stop all services
docker compose down

# Rebuild and start (after code changes)
docker compose up -d --build
```

### Development Commands
```bash
# Access Laravel container shell
docker compose exec app sh

# Run Laravel Artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan make:controller TestController

# Access Node.js container shell
docker compose exec node sh

# Install new npm packages
docker compose exec node npm install package-name

# Build for production
docker compose exec node npm run build
```

### Database Operations
```bash
# Reset database (migrations)
docker compose exec app php artisan migrate:fresh

# Run seeders
docker compose exec app php artisan db:seed

# Access MySQL directly
docker compose exec db mysql -u laravel_user -p cms
```

## File Structure

```
├── docker-compose.yml      # Main Docker Compose configuration
├── Dockerfile             # PHP/Laravel container
├── Dockerfile.node        # Node.js/Vite container
├── docker-init.sh         # Laravel initialization script
├── .dockerignore          # Files to exclude from Docker context
└── DOCKER_README.md       # This file
```

## Automatic Setup Features

### Laravel Container
- ✅ Database connection waiting
- ✅ Environment file creation (.env)
- ✅ Application key generation
- ✅ Database migrations
- ✅ Storage link creation
- ✅ Permission setting
- ✅ Composer dependencies

### Node.js Container
- ✅ NPM dependencies installation
- ✅ Vite configuration for Docker
- ✅ Cache clearing
- ✅ Development server with HMR

## Troubleshooting

### Application not loading
1. Check if all containers are running: `docker compose ps`
2. Check logs: `docker compose logs app`
3. Ensure database is ready: `docker compose logs db`

### Vite/Frontend issues
1. Check Node container: `docker compose logs node`
2. Restart Node service: `docker compose restart node`
3. Clear Vite cache: `docker compose exec node rm -rf node_modules/.vite`

### Database connection issues
1. Wait for database initialization (can take 30-60 seconds on first run)
2. Check database logs: `docker compose logs db`
3. Verify connection: `docker compose exec app php artisan migrate:status`

### Permission issues
1. Fix Laravel permissions:
   ```bash
   docker compose exec app chown -R www-data:www-data /var/www/html/storage
   docker compose exec app chmod -R 775 /var/www/html/storage
   ```

## Development Workflow

1. Make code changes in your local environment
2. Changes are automatically reflected due to volume mounting
3. For frontend changes: Vite will hot-reload automatically
4. For backend changes: Refresh the browser
5. Database changes: Run migrations via `docker compose exec app php artisan migrate`

## Production Notes

For production deployment:
1. Update environment variables in docker-compose.yml
2. Change database credentials
3. Set `APP_ENV=production` and `APP_DEBUG=false`
4. Use `npm run build` instead of `npm run dev` for Node service
5. Consider using a reverse proxy (nginx) for production