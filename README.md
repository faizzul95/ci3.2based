# CODEIGNITER 3-BASED (CUSTOM)

Framework : CodeIgniter 3 (version 3.2.0-dev) <br/>
Status : <i> support </i> <br/>
Last update : 29/09/2024

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

======================================================================

# EXTENDED

## Path: `application/core`

### MY_Model_Custom

#### Description
`MY_Model_Custom` is an extended model class for CodeIgniter 3 that introduces advanced query capabilities, improved relationship handling, fixes the N+1 query issue, and adds security layers for interacting with databases. 

#### Database Support
`MySQL`

#### Example Model

<details> 
<summary> Click to view model example </summary>
  
```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName>_model extends MY_Model
{
    // (OPTIONAL) The connection to database based on configuration, default is 'default'
    protected $connection = 'default';

    // (REQUIRED) The name of the table
    public $table = '';
    
    // (REQUIRED) The primary key column name, default is 'id'
    public $primaryKey = 'id'; 
    
    // (REQUIRED) The fields that can be filled by insert/update
    public $fillable = [];
    
    // (OPTIONAL) Enable or disable timestamps (created_at & updated_at)
    public $timestamps = TRUE;

    // (OPTIONAL) The timestamp format to save in database, default is 'Y-m-d H:i:s'
    protected $timestamps_format = 'Y-m-d H:i:s';

    // (OPTIONAL) The timestamp column that expected to be save
    protected $_created_at_field = 'created_at';
    protected $_updated_at_field = 'updated_at';
    
    // (OPTIONAL) Will append the query result with new data from a specific function
    public $appends = [];
    
    // (OPTIONAL) Will remove specific columns from the query result
    public $hidden = [];
    
    // (OPTIONAL) Columns defined here will not be updated or inserted
    public $protected = [];

    // (OPTIONAL) The validation rules for insert & update
    protected $_validation = [];

    // (OPTIONAL) The validation rules for insert only, will override the $_validation if exists.
    protected $_insertValidation = []; 

    // (OPTIONAL) The validation rules for update only, will override the $_validation if exists.
    protected $_updateValidation = [];

    public function __construct()
    {
        parent::__construct();
    }
}
```
</details> 

<hr>

#### QUERY Functions

| Function        | Description                                                                                                                                       |
|-----------------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| `rawQuery()`    | Execute raw SQL queries directly. Useful for complex queries not supported by active record.                                                      |
| `table()`       | Specifies the database table for the query.                                                                                                       |
| `select()`      | Defines the columns to retrieve in a query. Similar to CodeIgniter’s `select()`.                                                                  |
| `where()`       | Adds a basic WHERE clause to the query. Similar to Laravel's `where()`.                                                                            |
| `orWhere()`     | Adds an OR WHERE clause. Similar to Laravel's `orWhere()`.                                                                                        |
| `whereNull()`   | Adds a WHERE clause to check for `NULL` values. Similar to Laravel's `whereNull()`.                                                               |
| `orWhereNull()` | Adds an OR WHERE clause to check for `NULL` values. Similar to Laravel's `orWhereNull()`.                                                         |
| `whereNotNull()`| Adds a WHERE clause to check for non-NULL values. Similar to Laravel's `whereNotNull()`.                                                          |
| `orWhereNotNull()`| Adds an OR WHERE clause to check for non-NULL values. Similar to Laravel's `orWhereNotNull()`.                                                   |
| `whereExists()` | Adds a WHERE EXISTS clause. Similar to Laravel's `whereExists()`.                                                                                 |
| `orWhereExists()`| Adds an OR WHERE EXISTS clause. Similar to Laravel's `orWhereExists()`.                                                                          |
| `whereNotExists()`| Adds a WHERE NOT EXISTS clause. Similar to Laravel's `whereNotExists()`.                                                                         |
| `orWhereNotExists()`| Adds an OR WHERE NOT EXISTS clause. Similar to Laravel's `orWhereNotExists()`.                                                                 |
| `whereNot()`    | Adds a WHERE NOT clause for negating conditions. Similar to Laravel's `whereNot()`.                                                               |
| `orWhereNot()`  | Adds an OR WHERE NOT clause for negating conditions. Similar to Laravel's `orWhereNot()`.                                                         |
| `whereTime()`   | Adds a WHERE clause for a time comparison. Similar to Laravel's `whereTime()`.                                                                    |
| `orWhereTime()` | Adds an OR WHERE clause for a time comparison. Similar to Laravel's `orWhereTime()`.                                                              |
| `whereDate()`   | Adds a WHERE clause for a date comparison. Similar to Laravel's `whereDate()`.                                                                    |
| `orWhereDate()` | Adds an OR WHERE clause for a date comparison. Similar to Laravel's `orWhereDate()`.                                                              |
| `whereDay()`    | Adds a WHERE clause for a specific day. Similar to Laravel's `whereDay()`.                                                                        |
| `orWhereDay()`  | Adds an OR WHERE clause for a specific day. Similar to Laravel's `orWhereDay()`.                                                                  |
| `whereYear()`   | Adds a WHERE clause for a specific year. Similar to Laravel's `whereYear()`.                                                                      |
| `orWhereYear()` | Adds an OR WHERE clause for a specific year. Similar to Laravel's `orWhereYear()`.                                                                |
| `whereMonth()`  | Adds a WHERE clause for a specific month. Similar to Laravel's `whereMonth()`.                                                                    |
| `orWhereMonth()`| Adds an OR WHERE clause for a specific month. Similar to Laravel's `orWhereMonth()`.                                                              |
| `whereIn()`     | Adds a WHERE IN clause. Similar to Laravel's `whereIn()`.                                                                                         |
| `orWhereIn()`   | Adds an OR WHERE IN clause. Similar to Laravel's `orWhereIn()`.                                                                                   |
| `whereNotIn()`  | Adds a WHERE NOT IN clause. Similar to Laravel's `whereNotIn()`.                                                                                  |
| `orWhereNotIn()`| Adds an OR WHERE NOT IN clause. Similar to Laravel's `orWhereNotIn()`.                                                                            |
| `whereBetween()`| Adds a WHERE BETWEEN clause. Similar to Laravel's `whereBetween()`.                                                                               |
| `orWhereBetween()`| Adds an OR WHERE BETWEEN clause. Similar to Laravel's `orWhereBetween()`.                                                                        |
| `whereNotBetween()`| Adds a WHERE NOT BETWEEN clause. Similar to Laravel's `whereNotBetween()`.                                                                      |
| `orWhereNotBetween()`| Adds an OR WHERE NOT BETWEEN clause. Similar to Laravel's `orWhereNotBetween()`.                                                              |
| `join()`        | Adds an INNER JOIN to the query. Similar to CodeIgniter’s `join()`.                                                                                |
| `rightJoin()`   | Adds a RIGHT JOIN to the query. Similar to Laravel's `rightJoin()`.                                                                                |
| `leftJoin()`    | Adds a LEFT JOIN to the query. Similar to Laravel's `leftJoin()`.                                                                                 |
| `innerJoin()`   | Adds an INNER JOIN to the query. Same as `join()`.                                                                                                |
| `outerJoin()`   | Adds an OUTER JOIN to the query. Similar to Laravel's `outerJoin()`.                                                                               |
| `limit()`       | Limits the number of records returned. Similar to CodeIgniter's `limit()`.                                                                        |
| `offset()`      | Skips a number of records before starting to return records. Similar to CodeIgniter's `offset()`.                                                 |
| `orderBy()`     | Adds an ORDER BY clause. Similar to Laravel's `orderBy()`.                                                                                        |
| `groupBy()`     | Adds a GROUP BY clause. Similar to Laravel's `groupBy()`.                                                                                         |
| `groupByRaw()`  | Adds a raw GROUP BY clause. Similar to Laravel's `groupByRaw()`.                                                                                  |
| `having()`      | Adds a HAVING clause. Similar to Laravel's `having()`.                                                                                            |
| `havingRaw()`   | Adds a raw HAVING clause. Similar to Laravel's `havingRaw()`.                                                                                     |
| `chunk()`       | Process data in chunks to handle large datasets efficiently. Similar to Laravel's `chunk()`.                                                      |
| `get()`         | Retrieves all data from the database based on the specified criteria.                                                                              |
| `fetch()`       | Retrieves a single record from the database based on the specified criteria.                                                                       |
| `first()`       | Retrieves the first record based on the query.                                                                                                     |
| `last()`        | Retrieves the last record based on the query.                                                                                                      |
| `count()`       | Counts the number of records matching the specified criteria.                                                                                      |
| `find()`        | Finds a record by its primary key (ID).                                                                                                            |
| `toSql()`       | Returns the SQL query string (without eager loading query).                                                                                        |
| `toSqlPatch()`  | Returns the SQL query string for updating data.                                                                                                    |
| `toSqlCreate()` | Returns the SQL query string for inserting data.                                                                                                   |
| `toSqlDestroy()`| Returns the SQL query string for deleting data.                                                                                                    |

<hr>

#### Pagination Functions

| Function                 | Description                                                                                                                                      |
|--------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPaginateFilterColumn()` | Sets the filter conditions for pagination. If not set, all columns from the main table are queried.                                           |
| `paginate()`             | Custom pagination method that works without the datatable library. Allows paginating results based on the specified criteria.                    |
| `paginate_ajax()`        | Pagination method specifically designed to work with AJAX requests and integrate with datatables.                                                |

<hr>

#### Relationship Functions (in model only)

| Function      | Description                                                                                                                                      |
|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `hasMany()`   | Defines a one-to-many relationship. Similar to Laravel's `hasMany()`.                                                                            |
| `hasOne()`    | Defines a one-to-one relationship. Similar to Laravel's `hasOne()`.                                                                              |
| `belongsTo()` | Defines an inverse one-to-many or one-to-one relationship. Similar to Laravel's `belongsTo()`.                                                   |

<details> 
<summary> Example Usage of hasMany($modelName, $foreignKey, $localKey) </summary>
  
#### Description
<b>Parameters:</b><br>
`$modelName` (string): Indicate the model that want to has the relation [REQUIRED]<br>
`$foreignKey` (string): Indicate the foreign key in related model [REQUIRED] <br>
`$localKey` (string): Indicate the key in current model, usually will taking from $primaryKey value [OPTIONAL]
<br>

```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function relatedModelWithLocalKey()
    {
         return $this->hasMany('Related_model', 'foreign_id', 'id');
    }

    public function relatedModelWithoutLocalKey()
    {
         return $this->hasMany('Related_model', 'foreign_id');
         // Remark : will use $primaryKey value as the localKey
    }
}
```
</details> 

<details> 
<summary> Example Usage of hasOne($modelName, $foreignKey, $localKey) </summary>
  
#### Description
<b>Parameters:</b><br>
`$modelName` (string): Indicate the model that want to has the relation [REQUIRED]<br>
`$foreignKey` (string): Indicate the foreign key in related model [REQUIRED] <br>
`$localKey` (string): Indicate the key in current model, usually will taking from `$primaryKey` value [OPTIONAL]
<br>

```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function relatedModelWithLocalKey()
    {
         return $this->hasOne('Related_model', 'foreign_id', 'id');
    }

    public function relatedModelWithoutLocalKey()
    {
         return $this->hasOne('Related_model', 'foreign_id');
         // Remark : will use $primaryKey value as the localKey
    }
}
```
</details> 

<details> 
<summary> Example Usage of belongsTo($modelName, $foreignKey, $ownerKey) </summary>
  
#### Description
<b>Parameters:</b><br>
`$modelName` (string): Indicate the model that want to has the relation [REQUIRED]<br>
`$foreignKey` (string): Indicate the foreign key in related model [REQUIRED] <br>
`$ownerKey` (string): Indicate the key in current model, usually will taking from `$primaryKey` value [OPTIONAL]
<br>

```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function relatedModelWithOwnerKey()
    {
         return $this->belongsTo('Related_model', 'foreign_id', 'id');
    }

    public function relatedModelWithoutOwnerKey()
    {
         return $this->belongsTo('Related_model', 'foreign_id');
         // Remark : will use $primaryKey value as the ownerKey
    }
}
```
</details> 

<hr>

#### Eager Load Functions

| Function   | Description                                                                                                                                      |
|------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `with()`   | Eager loads related models to avoid the N+1 query issue. Similar to Laravel's `with()`.                                                          |

<details> 
<summary> Example Usage of with($relation) </summary>
  
#### Description
<b>Parameters:</b><br>
`$relation` (array/string/callback): An relation to extends the result with related table. 
<br>

```php
<?php

# MODEL : MAIN / PARENT

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function related1()
    {
        return $this->hasMany('Related1_model', 'anyPK_id', 'id');
    }
}

# MODEL : RELATED / CHILD

class Related1_model extends MY_Model
{
    public $table = 'relatedTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'columnRelated1',
        'columnRelated2',
        'columnRelated3',
        'columnRelated4',
        'anyPK_id'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function function1()
    {
        return $this->hasMany('Related2_model', 'anyRelatedPK_id', 'id');
    }

    public function function2()
    {
        return $this->hasOne('Related3_model', 'anyRelatedPK_id', 'id');
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function simpleEagerLoadUsingParam()
    {
        return $this->any_model->select('id, column1, column2')
                ->whereYear('created_at', '>=', '2024')
                ->orderBy('id', 'DESC')
                ->with('related1', 'related1.function1', 'related1.function2') // USE PARAMS
                ->get(); // can used get(), fetch(), paginate(), first(), last().
    }

    public function simpleEagerLoadUsingArray()
    {
        return $this->any_model->select('id, column1, column2')
                ->whereYear('created_at', '>=', '2024')
                ->orderBy('id', 'DESC')
                ->with(['post', 'post.comment', 'post.like']) // USE ARRAY 
                ->fetch(); // can used get(), fetch(), paginate(), first(), last().
    }

    public function advancedEagerLoadUsingCallback()
    {
        return $this->model->select('id, name, email, nickname, password, username')
                ->whereYear('created_at', '>=', '2024')
                ->orderBy('id', 'DESC')
                ->with(['related1' => function ($query) {
                    $query->select('columnRelated1, columnRelated2')->whereMonth('created_at', '>=', 2);
                }])
                ->with(['related1.function1' => function ($query) {
                    $query->select('columnRelatedFunc1, columnRelatedFunc4');
                }])
                ->with('related1.function2')
                ->paginate(10, 3);  // can used get(), fetch(), paginate(), first(), last(). 
    }
}
```
</details> 

<hr>

#### CRUD Functions

| Function           | Description                                                                                                                                      |
|--------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `create()`         | Inserts a new record in the database based on the provided data.                                                                                 |
| `patch()`          | Updates a specific record by its primary key (ID) set at $primaryKey property in model.                                                          |
| `destroy()`        | Deletes a specific record by its primary key (ID) set at $primaryKey property in model.                                                          |
| `insertOrUpdate()` | Determines whether to insert or update a record based on given conditions. Similar to Laravel's `updateOrInsert()`.                              |

<details> 
<summary> Example Usage of create($data) </summary>
  
#### Description
<b>Parameters:</b><br>
`$data` (array): An array of data to be inserted to database. 
<br>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleCreate()
    {
        $data = [
            'column1' => $this->input->post('column1', TRUE),
            'column2' => $this->input->post('column1', TRUE),
            'column3' => $this->input->post('column1', TRUE)
        ];

        echo json_encode($this->any_model->create($data)); // return as the json to ajax
    }
}
```
</details> 

<details> 
<summary> Example Usage of patch($data, $id) </summary>
  
#### Description
<b>Parameters:</b><br>
`$data` (array): An array of data to be update. <br>
`$id` (string): An id (must be a PK set at $primaryKey in model) value to specify which data to be updated. 
<br>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleUpdate()
    {
        $id = $this->input->post('id', TRUE);

        $data = [
            'column1' => $this->input->post('column1', TRUE),
            'column2' => $this->input->post('column1', TRUE),
            'column3' => $this->input->post('column1', TRUE)
        ];

        echo json_encode($this->any_model->patch($data, $id)); // return as the json to ajax
    }
}
```
</details> 

<details> 
<summary> Example Usage of destroy($id) </summary>
  
#### Description
<b>Parameters:</b><br>
`$id` (string): An id (must be a PK set at $primaryKey in model) value to specify which data to be deleted. 
<br>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleDelete()
    {
        $id = $this->input->post('id', TRUE);
        echo json_encode($this->any_model->destroy($id)); // return as the json to ajax
    }
}
```
</details> 

<details> 
<summary> Example Usage of insertOrUpdate($condition, $data) </summary>
  
#### Description
<b>Parameters:</b><br>
`$condition` (array): An array of data to be use as the condition to determine the records are exist or not in database. <br>
`$data` (array): An array of data to be insert or update. 
<br>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleInsertOrUpdate1()
    {
        $data = [
            'column1' => $this->input->post('column1', TRUE),
            'column2' => $this->input->post('column1', TRUE),
            'column3' => $this->input->post('column1', TRUE)
        ];

        echo json_encode($this->any_model->insertOrUpdate(['id' => 'value'], $data)); // return as the json to ajax
    }

    public function exampleInsertOrUpdate2()
    {
        $condition = ['id' => 'value', 'column4' => 'example']; // this both condition must be fit in database to be updated, otherwise, it will insert a new record.

        $data = [
            'column1' => $this->input->post('column1', TRUE),
            'column2' => $this->input->post('column1', TRUE),
            'column3' => $this->input->post('column1', TRUE)
        ];

        echo json_encode($this->any_model->insertOrUpdate($condition, $data)); // return as the json to ajax
    }
}
```
</details> 

<hr>

#### CRUD Validation Functions

| Function                   | Description                                                                                                                                      |
|----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `ignoreValidation()`        | Ignores all validation rules for inserts and updates.                                                                                           |
| `setValidationRules()`      | Sets or overrides existing validation rules for the model on the fly.                                                                           |
| `setCustomValidationRules()`| Adds or changes existing validation rules that are already set in the model.                                                                    |

<details> 
<summary> Example Usage of ignoreValidation() </summary>
  
```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    protected $_validation = [
        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'required'],
        'column4' => ['field' => 'column4', 'label' => 'Column 4', 'rules' => 'required|trim'] 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleCreate()
    {
        return $this->any_model->ignoreValidation()->create($dataToInsert);
    }

    public function exampleUpdate()
    {
        return $this->any_model->ignoreValidation()->patch($dataToUpdate, $id);
    }
}
```
</details> 

<details> 
<summary> Example Usage of setValidationRules($rules) </summary>

#### Description
<b>Parameters:</b><br>
`$rules` (array): An array containing the set of rules. 
<br>
  
```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    // NO VALIDATION RULES SET HERE

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleCreate()
    {
        return $this->any_model
                    ->setValidationRules([
                        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'required|trim|max_length[255]'], // with required
                        'column2' => ['field' => 'column2', 'label' => 'Column 2', 'rules' => 'required|trim|valid_email', 'errors' => ['required' => 'Column 2 adalah wajib.']]
                    ])
                    ->create($dataToInsert);
    }

    public function exampleUpdate()
    {
        return $this->any_model
                    ->setValidationRules([
                        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'trim|max_length[255]'], // without required 
                        'column2' => ['field' => 'column2', 'label' => 'Column 2', 'rules' => 'required|trim', 'errors' => ['required' => 'Column 2 adalah wajib.']],
                    ])
                    ->patch($dataToUpdate, $id);
    }
}
```
</details> 

<details> 
<summary> Example Usage of setCustomValidationRules($rules) </summary>

#### Description
<b>Parameters:</b><br>
`$rules` (array): An array containing the set of rules to change or add. 
<br>
  
```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    protected $_validation = [
        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'required'], // Only have required
        'column4' => ['field' => 'column4', 'label' => 'Column 4', 'rules' => 'required|trim'] 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleCreate()
    {
        return $this->any_model
                    ->setCustomValidationRules([
                        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'required|trim|max_length[255]'], // will override the existing validation on models.
                        'column2' => ['field' => 'column2', 'label' => 'Column 2', 'rules' => 'required|trim|valid_email'] // will add new validation for column 2
                    ])
                    ->create($dataToInsert);
    }

    public function exampleUpdate()
    {
        return $this->any_model
                    ->setCustomValidationRules([
                        'column1' => ['field' => 'column1', 'label' => 'Column 1', 'rules' => 'trim|max_length[255]'], // will override the existing validation on models.
                        'column3' => ['field' => 'column3', 'label' => 'Column 3', 'rules' => 'required|trim|valid_email', 'errors' => ['required' => 'Column 3 adalah wajib.']] // will add new validation for column 3
                    ])
                    ->patch($dataToUpdate, $id);
    }
}
```
</details> 

<hr>

#### Security Functions

| Function                   | Description                                                                                                                                      |
|----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `safeOutput()`             | Escapes output to prevent XSS attacks. All data, including eager loaded and appended data, will be filtered.                                     |
| `safeOutputWithException()`| Same as `safeOutput()`, but allows specific fields to be excluded from escaping.                                                                 |

<details> 
<summary> Example Usage of safeOutput() </summary>
  
```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function simpleSafeOutput()
    {
        return $this->any_model->where('column', 'value')->safeOutput()->get();
    }

    public function exampleEagerLoadSafeOutput()
    {
        return $this->any_model->where()
               ->with(['keyRelation' => function ($query) {
                     $query->safeOutput();
                }])
                ->safeOutput()
                ->get();
    }
}
```
</details> 

<details> 
<summary> Example Usage of safeOutputWithException($column) </summary>
  
#### Description
<b>Parameters:</b><br>
`$column` (array): An array containing the column to exclude from escaping. 
<br>

```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function simpleSafeOutputWithException()
    {
        return $this->any_model->where('column', 'value')->safeOutputWithException(['column1', 'column2'])->get();
    }

    public function exampleEagerLoadSafeOutputWithException()
    {
        return $this->any_model->where()
               ->with(['keyRelation' => function ($query) {
                     $query->safeOutputWithException(['columnRelation1', 'columnRelation2']);
                }])
              ->safeOutputWithException(['column1', 'column2'])
              ->get();
    }
}
```
</details> 

<hr>

#### Helper Functions

| Function                   | Description                                                                                                                                      |
|----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `toArray()`                | Converts the result set to an array format (Default).                                                                                            |
| `toObject()`               | Converts the result set to an object format.                                                                                                     |
| `toJson()`                 | Converts the result set to JSON format.                                                                                                          |
| `showColumnHidden()`       | Displays hidden columns by removing the `$hidden` property temporarily.                                                                          |
| `setColumnHidden()`        | Dynamically sets columns to be hidden, similar to Laravel's `$hidden` model property.                                                            |
| `setAppends()`             | Dynamically appends custom attributes to the result set, similar to Laravel's `$appends` model property.                                         |

<details> 
<summary> Example Usage of toArray() / toObject() / toJson() </summary>
  
```php
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function example returnAsArrayData()
    {
        return $this->any_model->where('column', 'value')->toArray()->get();
    }

    public function example returnAsObjectData()
    {
        return $this->any_model->where('column', 'value')->toObject()->first();
    }

    public function example returnAsJsonData()
    {
        echo $this->any_model->where('column', 'value')->toJson()->fetch();
    }
}
```
</details> 


<details> 
<summary> Example Usage of showColumnHidden() </summary>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    protected $hidden = ['column3']; // removed the column3 from showing in the result

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleReturnWitColumnHiddenInModel()
    {
        return $this->any_model->where('column', 'value')->fetch();
        // Result : ['column1', 'column2', 'column4'];
        // Remark : column3 is not showing in the result because its already set hidden in the model.
    }

    public function exampleReturnWithoutColumnHidden()
    {
        return $this->any_model->where('column', 'value')->showColumnHidden()->fetch();
        // Result : ['column1', 'column2', 'column3', 'column4'];
        // Remark : Will show all the column. 
    }
}
```
</details> 

<details> 
<summary> Example Usage of setColumnHidden($column) </summary>
  
#### Description
<b>Parameters:</b><br>
`$column` (array): An array containing the column to exclude from showing in the result. 
<br>

```php
<?php

# MODEL

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Any_model extends MY_Model
{
    public $table = 'anyTable';
    public $primaryKey = 'id'; 
    
    public $fillable = [
        'column1',
        'column2',
        'column3',
        'column4'
    ];

    protected $hidden = ['column3']; // removed the column3 from showing in the result

    public function __construct()
    {
        parent::__construct();
    }
}

# CONTROLLER

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class <ClassName> extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('any_model');
    }

    public function exampleReturnWithoutColumnHidden()
    {
        return $this->any_model->where('column', 'value')->fetch();
        // Result : ['column1', 'column2', 'column4'];
        // Remark : column3 is not showing in the result because its already set hidden in the model.
    }

    public function exampleReturnWithColumnHidden()
    {
        return $this->any_model->where('column', 'value')->setColumnHidden(['column1'])->fetch();
        // Result : ['column2', 'column3', 'column4'];
        // Remark : will override the $hidden in model. 
    }

    public function exampleReturnWithColumnHiddenSetToEmpty()
    {
        return $this->any_model->where('column', 'value')->setColumnHidden([])->fetch();
        // Result : ['column1', 'column2', 'column3', 'column4'];
        // Remark : will override the $hidden in model. Showing all the data since no column hidden are being set. Use `showColumnHidden()` instead.
    }
}
```
</details> 