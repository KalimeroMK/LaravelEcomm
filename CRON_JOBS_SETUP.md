# 🕐 Cron Jobs Setup Guide

## 📋 Overview

This guide explains how to set up cron jobs for your Laravel e-commerce application to automate various maintenance and optimization tasks.

## 🚀 Front Module Optimization Cron Job

### **Purpose**

The `front:optimize` command automatically:

-   Clears expired cache keys
-   Warms up frequently accessed data
-   Maintains optimal cache performance
-   Prevents cache fragmentation
-   Ensures consistent front-end performance

### **Schedule Configuration**

```php
// In app/Console/Kernel.php
$schedule->command('front:optimize --force')
    ->everyFifteenMinutes()      // Run every 15 minutes
    ->withoutOverlapping()        // Prevent multiple instances
    ->onQueue('optimization')     // Use dedicated queue
    ->runInBackground();          // Run in background
```

### **Why Every 15 Minutes?**

-   **Cache TTL Strategy**:
    -   Search results: 15 minutes
    -   Recent products: 30 minutes
    -   Product listings: 1 hour
-   **Performance Balance**: Frequent enough to maintain performance, not too frequent to waste resources
-   **User Experience**: Ensures fresh data while maintaining speed

## 🔧 Server Cron Job Setup

### **1. Edit Crontab**

```bash
# Open crontab editor
crontab -e

# Add Laravel scheduler
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### **2. Verify Crontab**

```bash
# List current cron jobs
crontab -l

# Check if cron service is running
sudo systemctl status cron
```

### **3. Test Cron Job**

```bash
# Test the command manually
php artisan front:optimize --force

# Check if it's working
php artisan schedule:list
```

## 📊 Queue Worker Setup

### **1. Start Queue Workers**

```bash
# Start optimization queue worker
php artisan queue:work --queue=optimization --tries=3 --timeout=300

# Start all queue workers
php artisan queue:work --tries=3 --timeout=300
```

### **2. Supervisor Configuration (Production)**

```ini
[program:laravel-optimization-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --queue=optimization --tries=3 --timeout=300
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/optimization-worker.log
```

### **3. Start Supervisor**

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-optimization-worker:*

# Check status
sudo supervisorctl status
```

## 🕐 Alternative Cron Schedules

### **High Traffic Sites**

```php
// Run every 10 minutes for high traffic
$schedule->command('front:optimize --force')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onQueue('optimization')
    ->runInBackground();
```

### **Low Traffic Sites**

```php
// Run every 30 minutes for low traffic
$schedule->command('front:optimize --force')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onQueue('optimization')
    ->runInBackground();
```

### **Custom Schedule**

```php
// Run at specific times
$schedule->command('front:optimize --force')
    ->cron('*/20 * * * *')  // Every 20 minutes
    ->withoutOverlapping()
    ->onQueue('optimization')
    ->runInBackground();
```

## 📈 Monitoring & Logs

### **1. Check Command Logs**

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View queue logs
tail -f storage/logs/optimization-worker.log
```

### **2. Monitor Queue Status**

```bash
# Check queue status
php artisan queue:work --queue=optimization --once

# View failed jobs
php artisan queue:failed
```

### **3. Performance Monitoring**

```bash
# Check cache performance
php artisan front:optimize

# Monitor Redis
redis-cli info
```

## 🚨 Troubleshooting

### **Common Issues**

#### Cron Job Not Running

```bash
# Check cron service
sudo systemctl status cron

# Verify crontab
crontab -l

# Test manually
php artisan schedule:run
```

#### Queue Workers Not Processing

```bash
# Check queue status
php artisan queue:work --queue=optimization --once

# Restart queue workers
php artisan queue:restart
```

#### Memory Issues

```bash
# Check memory usage
php artisan front:optimize

# Clear all caches
php artisan cache:clear
```

### **Performance Tuning**

#### Adjust Schedule Frequency

```php
// For very high traffic sites
->everyFiveMinutes()

// For development/testing
->hourly()
```

#### Queue Configuration

```php
// Increase timeout for complex operations
->onQueue('optimization')
->timeout(600)  // 10 minutes
```

## 🔮 Advanced Configuration

### **1. Multiple Environments**

```php
// Different schedules for different environments
if (app()->environment('production')) {
    $schedule->command('front:optimize --force')
        ->everyFifteenMinutes()
        ->withoutOverlapping()
        ->onQueue('optimization')
        ->runInBackground();
} else {
    $schedule->command('front:optimize --force')
        ->hourly()
        ->withoutOverlapping()
        ->onQueue('optimization')
        ->runInBackground();
}
```

### **2. Conditional Execution**

```php
// Only run during business hours
$schedule->command('front:optimize --force')
    ->everyFifteenMinutes()
    ->between('08:00', '18:00')
    ->withoutOverlapping()
    ->onQueue('optimization')
    ->runInBackground();
```

### **3. Health Checks**

```php
// Add health check before running
$schedule->command('front:optimize --force')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onQueue('optimization')
    ->runInBackground()
    ->before(function () {
        // Check system health
        if (!app()->isHealthy()) {
            throw new Exception('System not healthy');
        }
    });
```

## 📚 Best Practices

### **1. Resource Management**

-   Use dedicated queues for optimization tasks
-   Set appropriate timeouts
-   Monitor memory usage
-   Implement retry logic

### **2. Monitoring**

-   Log all optimization activities
-   Monitor cache hit rates
-   Track performance metrics
-   Set up alerts for failures

### **3. Maintenance**

-   Regularly review cron schedules
-   Clean up old logs
-   Monitor queue performance
-   Update optimization strategies

## 🤝 Support

For questions or issues related to cron job setup:

1. Check the troubleshooting section above
2. Verify cron service status
3. Test commands manually
4. Review Laravel logs
5. Contact the development team

---

**Last Updated**: January 2025  
**Version**: 1.0.0  
**Maintained By**: Development Team
