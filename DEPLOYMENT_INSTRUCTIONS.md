# Deploying Laravel Application on Render

## Prerequisites

1. Create a free account at [Render](https://render.com)
2. Connect your GitHub/GitLab account to Render
3. Ensure your Laravel application is in a Git repository

## Deployment Steps

### 1. Prepare Your Repository

Make sure your repository contains all necessary files:

- `render.yaml` - Render service configuration
- `Dockerfile` - Container configuration (or `Dockerfile.alpine-redis` if you encounter PECL errors)
- `nginx.conf` - Web server configuration
- `supervisord.conf` - Process manager configuration
- `.env.example.production` - Production environment variables
- `docker-compose.yml` - Optional local development configuration

### 2. Create a New Web Service on Render

1. Go to your Render dashboard
2. Click "New +" and select "Web Service"
3. Connect your Git repository
4. Select the branch you want to deploy (usually `main` or `master`)
5. Give your service a unique name (e.g., `laravel-app-12345`)
6. For the environment, select "Docker" (since we're using a Dockerfile)
7. Leave the build command and start command fields empty (we're using render.yaml)
8. Set the region to your preferred location
9. Click "Create Web Service"

### 3. Configure Environment Variables

After the service is created, go to your service settings and set the following environment variables:

#### Required Environment Variables:
```
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-service-name.onrender.com
```

#### Database Variables (these are automatically set by render.yaml if using the database service):
- `DB_HOST` - Will be automatically set from the database service
- `DB_PORT` - Will be automatically set
- `DB_DATABASE` - Will be automatically set
- `DB_USERNAME` - Will be automatically set
- `DB_PASSWORD` - Will be automatically set

#### Additional Variables (if needed):
```
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_URL=redis://:password@your-redis-host:6379
```

### 4. Wait for Deployment

- Render will automatically build and deploy your application
- Monitor the build logs in the Render dashboard
- The first build may take several minutes

### 5. Access Your Application

- Once deployed, your application will be accessible at the URL provided in your Render dashboard
- The URL typically follows the format: `https://your-service-name.onrender.com`

## Running Migrations After Initial Deployment

After the first successful deployment, you'll need to run database migrations:

1. Go to your Render dashboard
2. Select your web service
3. Click on "Dashboard" tab
4. Click on "Manual Deploy"
5. Select "Deploy with command" and enter:
   ```
   php artisan migrate --force
   ```
6. Click "Deploy"

> **Note:** Only run migrations once during initial setup. Subsequent deployments will use the same database.

## Queue Worker Configuration

The queue worker service is configured in `render.yaml` as a separate worker service. This service will automatically start and run `php artisan queue:work` to process queued jobs.

## Database Setup

If you're using the MySQL service defined in `render.yaml`, your database will be automatically created and configured. The connection details are automatically set through the `fromDatabase` references in the render.yaml file.

## Redis Setup

If you're using the Redis service defined in `render.yaml`, your Redis instance will be automatically created and configured. Connection details are automatically passed through the `fromService` reference in the render.yaml file.

## Environment Variables Best Practices

1. **APP_KEY**: Generate a new app key after initial deployment:
   - In your Render dashboard, go to your service settings
   - Add a new environment variable: `APP_KEY`
   - Generate the value by running `php artisan key:generate --show` locally and copying the key

2. **Sensitive Information**: Never commit actual passwords or private keys to your repository
   - Use environment variables for all sensitive information
   - Use Render's secret environment variables for sensitive data

## Troubleshooting

### Common Issues

1. **Application not loading**:
   - Check the logs in your Render dashboard
   - Verify that the `APP_URL` environment variable is set correctly
   - Ensure all required environment variables are present

2. **Database connection errors**:
   - Verify that the database service is running
   - Check that the database environment variables are set correctly
   - Run migrations if this is the first deployment

3. **File permissions issues**:
   - Make sure the storage directory permissions are set correctly in the Dockerfile
   - Ensure the web server can write to storage and bootstrap/cache directories

4. **Asset loading issues**:
   - Make sure to run `npm run build` during the build process
   - Verify your asset URLs are correct for the production environment

5. **PECL Redis installation errors**:
   - If you encounter errors during the Redis PECL installation, try using the alternative Dockerfile (`Dockerfile.alpine-redis`)
   - The alternative Dockerfile uses the Redis extension from Alpine's package repository instead of PECL

6. **Tokenizer extension build errors**:
   - If you encounter errors related to the tokenizer extension during build, this has been addressed in the updated Dockerfiles
   - The tokenizer extension is built-in to PHP and doesn't need explicit installation

7. **Laravel package:discover errors**:
   - If you encounter errors with the `artisan package:discover` command during build, this has been addressed
   - The Dockerfiles now use `--no-scripts` flag during composer install and run artisan commands separately

8. **npm run build errors**:
   - If you encounter errors with the `npm run build` command during build, this has been addressed
   - The Dockerfiles now install development dependencies temporarily for building, then install only production dependencies

9. **Laravel queue worker errors**:
   - Queue worker is configured in both supervisord (for local development) and as a separate service in Render
   - For production on Render, the queue worker runs in a separate worker service as defined in render.yaml
   - A 10-second delay has been added before starting the worker to allow the environment to be ready
   - Basic .env file is created from .env.example and application key is generated during build

### Debugging

- Check the build logs in the Render dashboard for compilation errors
- Check the runtime logs for application errors
- Use `php artisan tinker` to debug your application in a console environment
- Add temporary logging to your application to debug specific issues

## Scaling Your Application

1. To scale your web service, go to the Render dashboard
2. Select your service
3. Click on "Settings"
4. Adjust the instance type and scaling options as needed
5. For the queue worker service, you can scale similarly

## Security Considerations

- Enable HTTPS (Render automatically provides SSL certificates)
- Use environment variables for all sensitive configuration
- Keep dependencies updated
- Use Laravel's built-in security features (CSRF, XSS protection, etc.)
- Regularly review your application logs for suspicious activity

## Updating Your Application

1. Push your code changes to the connected Git branch
2. Render will automatically deploy the new version
3. Monitor the deployment logs for any issues
4. Test the updated application after deployment

## Monitoring

- Use Render's built-in log viewer to monitor your application
- Set up Laravel logging to write to appropriate channels
- Consider using external monitoring services for production applications