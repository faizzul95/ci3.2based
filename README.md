Features ready: -

- SECURITY: 
	1) XSS Protection (validate data from malicious code)
	2) Google Authenticator (Use for 2FA)
	3) Google ReCAPTCHA v2 (Reduce DDos Attack)
	4) Login Attempt (Reduce Brute Force Attack)
	5) Custom Front-end Validation in JS (Data integrity)
	6) Custom Route & Middleware (Protect URL & Page) - Thanks LuthierCI for amazing library
	7) CSRF Token & Cookie (Built in CI3)

- SYSTEM:
	1) Custom Model & DB Query
	2) Backup DB
	3) Job Queue (Worker) - Running in background (Thanks to yidas for queue library)
	4) Maintenance Mode (With custom page)
	5) Blade Templating Engine (Increase security & caching)
	6) SSL Force redirect (production mode)
	7) System logger (Log error system in database & files)
	8) Audit Trail (Log data insert, update, delete in database)
	9) CRUD Log (Log data insert, update, delete in files)

- HELPER
	A) Front-end (folder : public/custom/js & public/custom/php)
			1) Call API (POST, GET), Upload API, Delete API wrapper (using axios)
			2) Dynamic modal & Form loaded
			3) Generate datatable (server-side & client-side rendering)
			4) Print DIV (use printThis library)

	B) Backend-end (folder : application/helper)
			1) Array helper
			2) Data Helper
			3) Date Helper
			4) Upload Helper (upload, move, compress image)
			5) QR Generate Helper (using Endroid library)
			6) Read/Import Excel (using PHPSpreadsheet library)
			7) Mailer (using PHPMailer library)

This Ci3Based also includes stub files for creating controllers & models. Please change according to the suitability of the project

Command to run using terminal or cmd (without $) :
- php struck create $type $fileName $tableName
- php struck structure $fileName $tableName

Notes : 
- $tableName is optional & use for model only, 
- $type is only support for 'model' and 'controller'

Example :

Model
	1) php struck create model MasterRoles (will create basic model)
	2) php struck create model MasterRoles master_role (will create model with table columns from database)

Controller
	1) php struck create controller MasterRoles (will create controller)

Structure
	1) php struck structure MasterRoles (will create controller & basic model)
	2) php struck structure MasterRoles master_role (will create controller & model with table columns from database)
