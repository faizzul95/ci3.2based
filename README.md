# CODEIGNITER 3-BASED (CUSTOM)

Framework : CodeIgniter 3 (version 3.2.0-dev) <br/>
Status : <i> support </i> <br/>
Last update : 13/09/2023

======================================================================

<details> 
<summary> INSTRUCTION </summary>
<hr>
  
- HOW TO START A NEW PROJECT?
	<ol type="1">
		<li> Download this project </li>
		<li> Rename project folder to any name </li>
		<li> Run command "composer install/update" using CMD/Terminal (make sure to install composer!) </li>
		<li> Run command "npm install/update" using CMD/Terminal (make sure to install Node.js!) </li>
		<li> Configure the .env files for ENVIRONMENT, DATABASE & APP </li>
	</ol>

- HOW TO INSTALL GRUNT ASSET BUNDLER?
	<ol type="1">
		<li> Make sure node.js already install! </li>
		<li> Open Terminal/Command Prompt (copy & paste code below) </li> 
			<ol type="A">
				<li> npm ls -g grunt-cli  (To check if grunt already install or not) </li>
				<li> npm install -g grunt-cli </li>
				<li> npm install grunt grunt-contrib-concat grunt-contrib-uglify grunt-contrib-clean grunt-contrib-cssmin grunt-babel @babel/core @babel/preset-env --save-dev </li>
			</ol>
	</ol>

<br/>
</details> 

======================================================================

<details> 
<summary> FEATURES </summary>
<hr>
  
- SECURITY
	1) XSS Protection (validate data from malicious code using middleware)
	2) Google Authenticator (Use for 2FA)
	3) Google ReCAPTCHA v2 (Reduce DDos Attack)
	4) Login Attempt (Reduce Brute Force Attack)
	5) Custom Front-end Validation in JS (Data integrity)
	6) Custom Route & Middleware (Protect URL & Page) - Thanks <a href="https://github.com/ingeniasoftware/luthier-ci" target="_blank"> Luthier CI </a> for amazing library
	7) CSRF Token & Cookie (Built in CI3)
	8) Rate Limiting Trait (API Request limiter using Middleware)

- SYSTEM
	1) Custom Model DB Query. 
	2) Job Queue (Worker) - Running in the background (Thanks to <a href="https://github.com/yidas/codeigniter-queue-worker" target="_blank"> Yidas </a> for Queue Worker library)
	3) Maintenance Mode (With custom page)
	4) Blade Templating Engine (Increase security & caching) - (Credit to team <a href="https://github.com/EFTEC/BladeOne" target="_blank">BladeOne</a>)
	5) SSL Force redirect (production mode)
	6) System logger (Log error system in database & files)
	7) Audit Trail (Log data insert, update, delete in the database)
	8) CRUD Log (Log data insert, update, delete in files)
	9) Cron Scheduler - (Credit to <a href="https://github.com/peppeocchi/php-cron-scheduler" target="_blank">Peppeocchi</a>)

- HELPER
	<ol type="A">
	<li> Front-end </li> 
	<ol type="1">
		<li> Call API (POST, GET), Upload API, Delete API wrapper (using Axios) </li>
		<li> Dynamic modal & Form loaded </li>
		<li> Generate datatable (server-side & client-side rendering) </li>
		<li> Print DIV (use <a href="https://jasonday.github.io/printThis/" target="_blank">printThis</a> library) </li>
	</ol> 
	<br>
	<li> Backend-end </li> 
	<ol type="1">
		<li> Array helper </li>
		<li> Data Helper </li>
		<li> Date Helper </li>
		<li> Upload Helper (upload, move, compress image) </li>
		<li> QR Generate Helper (using <a href="https://github.com/endroid/qr-code" target="_blank">Endroid</a> library) </li>
		<li> Read/Import Excel (using <a href="https://github.com/PHPOffice/PhpSpreadsheet" target="_blank">PHPSpreadsheet</a> library) </li>
		<li> Mailer (using <a href="https://github.com/PHPMailer/PHPMailer" target="_blank">PHPMailer</a> library) </li>
	</ol>
	</ol>
			
- SERVICES
	1) Backup system folder (with exceptions file or folder)
	2) Backup database (MySQL tested)
	3) Upload file backup to google drive (need to configure)

- MODULE BUNDLER
	1) Concat, uglify JavaScript using Grunt JS (read more <a href="https://gruntjs.com/" target="_blank">Grunt Website</a>)

<br/>
</details> 

======================================================================

<details> 
<summary> COMMAND </summary>
<hr>

Command (Terminal / Command Prompt):-

<ol type="A">
	<li> Cache </li> 
		<ol type="1">
			<li> php struck clear view (remove blade cache)  </li>
			<li> php struck clear cache (remove ci session cache)  </li>
      <li> php struck clear all (remove ci session cache, blade cache & logs file)  </li>
      <li> php struck optimize (remove blade cache & logs file)  </li>
		</ol> 
	<br>
	<li> Backup (use as an ordinary cron job) </li> 
		<ol type="1">
			<li> php struck cron database (backup the database in folder project) </li>
			<li> php struck cron system (backup system folder in folder project) </li>
			<li> php struck cron database upload (backup the database & upload to google drive) </li>
			<li> php struck cron system upload (backup system folder & upload to google drive) </li>
		</ol> 
	<br>
	<li> Jobs (Queue Worker) </li> 
		<ol type="1">
			<li> php struck jobs (temporary run until jobs completed) </li>
			<li> php struck jobs:work (temporary run until jobs completed) </li>
			<li> php struck jobs:listen (permanent until services kill) - use in Linux environment </li>
			<li> php struck queue:retry < replace with UUID > </li>
			<li> php struck queue:retry all</li>
		</ol> 
	<br>
		<li> Cron Scheduler (Laravel Task Scheduling) </li> 
		<ol type="1">
			<li> php struck schedule:run </li>
			<li> php struck schedule:list </li>
			<li> php struck schedule:work </li>
			<li> php struck schedule:fail </li>
		</ol> 
	<br>
	<li> Module Bundler </li> 
		<ol type="1">
			<li> grunt </li>
			<li> grunt watch (keep detecting changes) </li>
		</ol> 
	<br>
</ol>
 <br/>
</details> 

======================================================================

<details> 
<summary> CUSTOM COMMAND </summary>
<hr>
  
This Ci3Based also includes stub files for creating controllers & models. Please change according to the suitability of the project

Notes : 
- $fileName is required.
- $tableName is optional & use for model only.
- $type is required & only support for 'model' and 'controller'.

Command to run using terminal or cmd (without $) :
- php struck create $type $fileName $tableName
- php struck structure $fileName $tableName
- php struck generate services $moduleName $fileName $modelName $tableName

Example :
<ol type="A">
	<li> Model </li> 
		<ol type="1">
			<li> php struck create model MasterRoles (will create a basic model) </li>
			<li> php struck create model MasterRoles master_role (will create model with table columns from database) </li>
		</ol> 
	<br>
	<li> Controller </li> 
		<ol type="1">
			<li> php struck create controller MasterRoles (will create controller) </li>
		</ol> 
	<br>
	<li> Structure </li> 
		<ol type="1">
			<li> php struck structure MasterRoles (will create controller & basic model) </li>
			<li> php struck structure MasterRoles master_role (will create controller & model with table columns from database) </li>
		</ol> 
	<br>
	<li> Services </li> 
		<ol type="1">
			<li> php struck generate services core profile usersProfile users_profile </li>
		</ol> 
	<br>
</ol>

<br/>
</details> 
