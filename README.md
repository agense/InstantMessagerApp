## LARAVEL/VUE INSTANT MESSAGING APPLICATION

### PROJECT DESCRIPTION

Instant messaging single page app created with Laravel and Vue.js. The project implements broadcasting via private channels using  a combination of self-hosted websocket server  (beyondcode/laravel-websockets), Pusher Channels HTTP PHP Library and Laravel Echo Javascript Library. It also implements stateful authentication system via Laravel Sanctum. 
The functionalities included are: searching contacts between existing users, adding and removing other users to/from ones own contact list, sending instant messages to associated contacts and managing own profile: uploading profile images, changing passwords and account data.
As this app is highly dependent on queue processing due to the real time broadcasting features, it includes several custom artisan commands that can help handling jobs failures.
In addition, it includes failed jobs reporting functionality, allowing to create PDF reports on jobs that failed withing specific time interval and sending them by email. This procedure can be automated using sheduling.

### INSTALLATION & CONFIGURATION

#### CLONE THE PROJECT
#### INSTALL COMPOSER DEPENDENCIES
```bash
composer install
```
#### CREATE A .env FILE. 
Use as example the existing .env.example file. It includes all environment variables that are needed to be set for this specific app to function along with some recommended  default values.
#### SETUP DATABASE
  *  Create mysql database 
  *  Set the database credentials in .env file
```code
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
  * Create database tables.
  ```bash
  php artisan migrate
  ```
  If fake data is needed, seed the database.
  ```bash
  php artisan db:seed 
  ```
#### SETUP WEBSOCKETS 
#### In .env file set the broadcasting credentials.
#### !!! Note, because this app uses self-hosted websockets server and not the real Pusher API, do not set real Pusher API credentials  for PUSHER_APP_ID, PUSHER_APP_KEY and PUSHER_APP_SECRET. These variables can be any self-invented strings.
#### !!! The value for BROADCAST_DRIVER must be pusher
```code
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids

PUSHER_APP_ID=
PUSHER_APP_KEY= 
PUSHER_APP_SECRET= 
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```
#### INSTALL FRONTEND DEPENDENCIES AND COMPILE THE PROJECT 
```bash
npm install
npm run dev
```
### RUNNING THE APP
Start App Server:
```bash
php artisan serve
```
Start Websockets Server:
```bash
php artisan websockets:serve
```
Start Queue Worker Process:
```bash
php artisan queue:work
```
### HELPER COMMANDS TO HANDLE FAILED JOBS
#### Push failed jobs back to the queue for processing:
```bash
php artisan failedjobs:retry
```
##### !!! Note: this command will only retry jobs that failed during specified time interval before the current moment (defined in minutes). The default value is 120 minutes, i.e. only jobs that failed within 120 minutes from command calling moment will be retried. This value can be customized in .env file as FAILED_JOBS_RETRY_WITHIN_MINUTES option. Alternatively, it is possible to pass a --minutes= switch to the command itself when calling it.

#### Delete failed jobs from failed_jobs table and log them to failed_jobs.log file:
```bash
php artisan failedjobs:clean
```
##### !!! Note: this command will only delete jobs that failed earlier than the specified time before the command calling moment (defined in hours). The default value is 48, i.e. only jobs that failed 48 hours ago or earlier will be deleted. This value can be customized in .env file as FAILED_JOBS_CLEAN_EVERY_HOURS option. Alternatively, it is possible to pass a --hours= switch to the command itself when calling it.

#### !!! Note: to shedule the above commands to run by default, uncomment the following lines in App\Console\Kernel.php file, shedule() method. You can specify sheduling interval as needed.
```code
$schedule->command('failedjobs:retry')->everyFiveMinutes();
$schedule->command('failedjobs:clean')->dailyAt('00:01');
```
#### SETUP FAILED JOBS REPORTING
To issue a report on failed jobs for specific period in time, call the following command:
```bash
failedjobs:report
```
This can be set to execute automaticaly every specified interval by uncommenting this line in App\Console\Kernel.php file, shedule() method. You can specify sheduling interval as needed.
```code
 $schedule->command('failedjobs:report')->dailyAt('00:00');
```
!!! Before calling failed jobs reporting command, configure the mailer in .env file.
In addition, setup the following environment variables in .env file:
```code
ISSUE_FAILED_JOBS_REPORTS_FOR_HOURS=24
FAILED_JOBS_REPORTING_DATA_SRC=log
FAILED_JOBS_REPORT_EMAIL=
```
* FAILED_JOBS_REPORT_EMAIL is an email to which the report should be sent. Reporting will not work if this value is not set.
* ISSUE_FAILED_JOBS_REPORTS_FOR_HOURS specifies the interval in hours within which from the report creation time the job must have failed to be included in the report. If report creation is scheduled, its best to use the sheduling interval as this value to achieve consistency in repoting, for example, a report issued every 24 hours would include jobs that failed withing 24 hours.
* FAILED_JOBS_REPORTING_DATA_SRC can be set to either database or log. When set to database, failed jobs will be retrieved directly from databases failed_jobs table. When set to log, the values will be retrieved from failed_jobs.log file. 
!!! Note:  if this option is set to database, ensure that when using failedjobs:clean command, the interval in hours for cleaning jobs is bigged than the interval for reporting jobs, otherwise some jobs may be deleted before they are reported. Alternatively, set this option to log. Log option is more reliable if using the failedjobs:clean command frequently, as jobs are always logged before deleting them from database and also logged again before issuing a report to ensure none is missing (however, no job is logged twice).
#### The configuration file for failedjobs commands, inluding reporting is config/failed_jobs.php

### MAIN PLUGINS AND LIBRARIES IN PROJECT

* Self Hosted Web Sockets Server [beyondcode
/
laravel-websockets](https://github.com/beyondcode/laravel-websockets)
* [Pusher Channels HTTP PHP Library](https://github.com/pusher/pusher-http-php)
* Authentication [Laravel Sanctum](https://github.com/laravel/sanctum)
* PHP image handling and manipulation library [Intervention Image](http://image.intervention.io/)
* Javascript library [Laravel Echo](https://github.com/laravel/echo)
* Javascript Frontend Framework [Vue.js](https://vuejs.org/)
* Promise based HTTP client [Axios](https://github.com/axios/axios)
* [Bootstrap](https://getbootstrap.com/)
* Icon Fonts [Font Awesome 5](https://fontawesome.com/)
* Plugin for Notifications [Toastr](https://github.com/CodeSeven/toastr)
* Toast notifications for Vue.js [vue-toastr-2
](https://www.npmjs.com/package/vue-toastr-2)
* DOMPDF Wrapper for Laravel [barryvdh/laravel-dompdf
](https://github.com/barryvdh/laravel-dompdf)
