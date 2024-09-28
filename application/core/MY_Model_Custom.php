<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Model Class
 *
 * @category  Model
 * @Description  An extended model class for CodeIgniter 3 with advanced querying capabilities, relationship handling, and security features.
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @link      -
 * @version   0.0.7.0
 */

class MY_Model_Custom extends CI_Model
{
    protected $table;
    protected $primaryKey = 'id';

    protected $db;
    protected $query;
    protected $connection = 'default';

    /**
     * @var null|array
     * Specifies fields to be protected from mass-assignment.
     * If set to null, it will be initialized as an array containing only the primary key.
     * If set as an array, it will retain its value without modifications.
     * Note: An empty array will not automatically include the primary key.
     */
    protected $protected = null;

    /**
     * @var null|array
     * Specifies additional attributes to be appended to the model's array and JSON representations.
     * If set to null, it will be initialized as an empty array.
     * If set as an array, it will retain its value without modifications.
     */
    protected $appends = null;

    /**
     * @var array|null
     * Specifies fields to be hidden from array and JSON representation.
     * If null, it will be initialized as an empty array when accessed.
     * If set as an array, it will contain the names of fields to be hidden.
     * Hidden fields are typically sensitive data like passwords or internal attributes.
     */
    protected $hidden = null;

    /**
     * @var null|array
     * Sets fillable fields.
     * If value is set as null, the $fillable property will be set as an array with all the table fields (except the primary key) as elements.
     * If value is set as an array, there won't be any changes done to it (ie: no field of the table will be updated or inserted).
     */
    protected $fillable = null;

    protected $relations = [];
    protected $eagerLoad = [];
    protected $returnType = 'array';
    protected $_secureOutput = false;
    protected $_secureOutputException = [];
    protected $allowedOperators = ['=', '!=', '<', '>', '<=', '>=', '<>', 'LIKE', 'NOT LIKE'];

    protected $timestamps = true;
    protected $timestamps_format = 'Y-m-d H:i:s';

    protected $_created_at_field = 'created_at';
    protected $_updated_at_field = 'updated_at';
    protected $_deleted_at_field = 'deleted_at';

    protected $parallelMaxWorker = 3;
    protected $parallelStatus = false;
    protected $parallelTimeout = 3600;
    protected $parallelTempDir = '';

    protected $_paginateColumn = [];
    protected $_paginateSearchValue = '';

    protected $_validation = []; // will be used for both insert & update
    protected $_validationCustomize = []; // will be used to customize/add-on validation for insert & update
    protected $_insertValidation = []; // will be used for insert only
    protected $_updateValidation = []; // will be used for update only
    protected $_overrideValidation = []; // to override the validation for update & insert
    protected $_ignoreValidation = false; // will ignore the validation
    protected $_validationError = []; // used to store the validation error message

    public function __construct()
    {
        $this->db = $this->load->database($this->connection, TRUE);
    }

    /**
     * Set the table name
     *
     * @param string $table Table name to be set
     * @return $this
     */
    public function table($table)
    {
        $this->table = trim($table);
        return $this;
    }

    /**
     * Select columns for the query
     *
     * @param string $columns Columns to select
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->db->select(trim($columns));
        return $this;
    }

    /**
     * Add a WHERE clause to the query
     *
     * @param string|array|Closure $column Column name, array of conditions, or Closure
     * @param mixed $operator Operator or value
     * @param mixed $value Value (if operator is provided)
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        // If it's a Closure, we'll handle it separately
        if ($column instanceof Closure) {
            return $this->whereNested($column);
        }

        // If it's an array, we'll assume it's a key-value pair of conditions
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->where($key, $val);
            }
            return $this;
        }

        // If only two parameters are given, we'll assume it's column and value
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('where', $column, $value, $operator);
        return $this;
    }

    /**
     * Add an OR WHERE clause to the query
     *
     * @param string|array|Closure $column Column name, array of conditions, or Closure
     * @param mixed $operator Operator or value
     * @param mixed $value Value (if operator is provided)
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        // If it's a Closure, we'll handle it separately
        if ($column instanceof Closure) {
            return $this->whereNested($column);
        }

        // If it's an array, we'll assume it's a key-value pair of conditions
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->orWhere($key, $val);
            }
            return $this;
        }

        // If only two parameters are given, we'll assume it's column and value
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('or_where', $column, $value, $operator);
        return $this;
    }

    public function whereNull($column)
    {
        $this->db->where($column . ' IS NULL');
        return $this;
    }

    public function orWhereNull($column)
    {
        $this->db->or_where($column . ' IS NULL');
        return $this;
    }

    public function whereNotNull($column)
    {
        $this->db->where($column . ' IS NOT NULL');
        return $this;
    }

    public function orWhereNotNull($column)
    {
        $this->db->or_where($column . ' IS NOT NULL');
        return $this;
    }

    public function whereExists(Closure $callback)
    {
        $subQuery = $this->forSubQuery($callback);
        $this->db->where("EXISTS ($subQuery)", NULL, FALSE);
        return $this;
    }

    public function orWhereExists(Closure $callback)
    {
        $subQuery = $this->forSubQuery($callback);
        $this->db->or_where("EXISTS ($subQuery)", NULL, FALSE);
        return $this;
    }

    public function whereNotExists(Closure $callback)
    {
        $subQuery = $this->forSubQuery($callback);
        $this->db->where("NOT EXISTS ($subQuery)", NULL, FALSE);
        return $this;
    }

    public function orWhereNotExists(Closure $callback)
    {
        $subQuery = $this->forSubQuery($callback);
        $this->db->or_where("NOT EXISTS ($subQuery)", NULL, FALSE);
        return $this;
    }

    public function whereColumn($first, $operator = null, $second = null)
    {
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }

        $this->db->where("$first $operator $second", NULL, FALSE);
        return $this;
    }

    public function orWhereColumn($first, $operator = null, $second = null)
    {
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }

        $this->db->or_where("$first $operator $second", NULL, FALSE);
        return $this;
    }

    public function whereNot($column, $operator = null, $value = null)
    {
        $this->where($column, $operator, $value)->where($column . ' IS NOT', null);
        return $this;
    }

    public function orWhereNot($column, $operator = null, $value = null)
    {
        $this->orWhere($column, $operator, $value)->orWhere($column . ' IS NOT', null);
        return $this;
    }

    public function whereJsonContains($column, $value)
    {
        $this->db->where("JSON_CONTAINS($column, " . $this->sanitizeValue(json_encode($value)) . ")", NULL, FALSE);
        return $this;
    }

    public function orWhereJsonContains($column, $value)
    {
        $this->db->or_where("JSON_CONTAINS($column, " . $this->sanitizeValue(json_encode($value)) . ")", NULL, FALSE);
        return $this;
    }

    # WHERE TIME, DATE, DAY, MONTH, YEAR SECTION

    public function whereTime($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('where', "TIME($column)", $value, $operator);
        return $this;
    }

    public function orWhereTime($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('or_where', "TIME($column)", $value, $operator);
        return $this;
    }

    public function whereDate($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('where', "DATE($column)", $value, $operator);
        return $this;
    }

    public function orWhereDate($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->applyCondition('or_where', "DATE($column)", $value, $operator);
        return $this;
    }

    public function whereDay($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateDayMonth($value);
        $this->applyCondition('where', "DAY($column)", $value, $operator);
        return $this;
    }

    public function orWhereDay($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateDayMonth($value);
        $this->applyCondition('or_where', "DAY($column)", $value, $operator);
        return $this;
    }

    public function whereYear($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateYear($value);
        $this->applyCondition('where', "YEAR($column)", $value, $operator);
        return $this;
    }

    public function orWhereYear($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateYear($value);
        $this->applyCondition('or_where', "YEAR($column)", $value, $operator);
        return $this;
    }

    public function whereMonth($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateDayMonth($value, true);
        $this->applyCondition('where', "MONTH($column)", $value, $operator);
        return $this;
    }

    public function orWhereMonth($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->validateDayMonth($value, true);
        $this->applyCondition('or_where', "MONTH($column)", $value, $operator);
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->db->where_in($column, $values);
        return $this;
    }

    public function whereNotIn($column, $values)
    {
        $this->db->where_not_in($column, $values);
        return $this;
    }

    public function orWhereIn($column, $values)
    {
        $this->db->or_where_in($column, $values);
        return $this;
    }

    public function orWhereNotIn($column, $values)
    {
        $this->db->or_where_not_in($column, $values);
        return $this;
    }

    public function whereBetween($column, $start, $end)
    {
        $this->db->where("$column BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function whereNotBetween($column, $start, $end)
    {
        $this->db->where("$column NOT BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function orWhereBetween($column, $start, $end)
    {
        $this->db->or_where("$column BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function orWhereNotBetween($column, $start, $end)
    {
        $this->db->or_where("$column NOT BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    /**
     * Execute a raw SQL query
     *
     * @param string $query Raw SQL query
     * @param array $binding Binding parameters
     * @return $this
     */
    public function rawQuery($query, $binding = [])
    {
        $query = $this->db->compile_binds($query, $binding);
        $this->db = $this->db->query($query);
        return $this;
    }

    public function join($table, $condition, $type = 'inner')
    {
        $this->db->join($table, $condition, $type);
        return $this;
    }

    public function rightJoin($table, $condition)
    {
        $this->db->join($table, $condition, 'right');
        return $this;
    }

    public function leftJoin($table, $condition)
    {
        $this->db->join($table, $condition, 'left');
        return $this;
    }

    public function innerJoin($table, $condition)
    {
        $this->db->join($table, $condition, 'inner');
        return $this;
    }

    public function outerJoin($table, $condition)
    {
        $this->db->join($table, $condition, 'outer');
        return $this;
    }

    public function limit($limit)
    {
        $limit = $this->validateInteger($limit, 'Limit');
        $this->db->limit($limit);
        return $this;
    }

    public function offset($offset)
    {
        $offset = $this->validateInteger($offset, 'Offset', false);
        $this->db->offset($offset);
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->db->order_by($column, strtoupper($direction));
        return $this;
    }

    public function groupBy($column)
    {
        $this->db->group_by($column);
        return $this;
    }

    public function groupByRaw($expression)
    {
        $this->db->group_by($expression, FALSE);
        return $this;
    }

    public function having($column, $value, $operator = '=')
    {
        $this->db->having("$column $operator", $value);
        return $this;
    }

    public function havingRaw($condition)
    {
        $this->db->having($condition, NULL, FALSE);
        return $this;
    }

    public function chunk($size, callable $callback)
    {
        $offset = 0;

        // Store the original query state
        $originalState = [
            'table' => $this->table,
            'db' => clone $this->db,
            'primaryKey' => $this->primaryKey,
            'relations' => $this->relations,
            'eagerLoad' => $this->eagerLoad,
            'returnType' => $this->returnType
        ];

        while (true) {
            // Restore the original query state
            $this->table = $originalState['table'];
            $this->db = clone $originalState['db'];
            $this->primaryKey = $originalState['primaryKey'];
            $this->relations = $originalState['relations'];
            $this->eagerLoad = $originalState['eagerLoad'];
            $this->returnType = $originalState['returnType'];

            // Apply limit and offset
            $this->limit($size)->offset($offset);

            // Get results 
            $results = $this->get();

            if (empty($results)) {
                break;
            }

            if (call_user_func($callback, $results) === false) {
                break;
            }

            $offset += $size;

            // Clear the results to free memory
            unset($results);
        }

        // Reset internal properties for next query
        $this->resetQuery();

        return $this;
    }

    public function count()
    {
        $query = $this->db->count_all_results();
        $this->resetQuery();
        return $query;
    }

    public function toSql()
    {
        $query = $this->db->get_compiled_select('', false);
        $this->resetQuery();
        return $query;
    }

    public function toSqlPatch($id = null, $data = [])
    {
        if (!empty($data)) {

            $data = $this->filterData($data);

            if ($this->timestamps) {
                $data[$this->_updated_at_field] = date($this->timestamps_format);
            }

            $this->db->set($data);
        }

        if ($id !== null) {
            $this->db->where($this->primaryKey, $id);
        }

        $query = $this->db->get_compiled_update($this->table, false);
        $this->resetQuery();
        return $query;
    }

    public function toSqlCreate($data = [])
    {
        if (!empty($data)) {

            $data = $this->filterData($data);

            if ($this->timestamps) {
                $data[$this->_created_at_field] = date($this->timestamps_format);
            }

            $this->db->set($data);
        }

        $query = $this->db->get_compiled_insert($this->table, false);
        $this->resetQuery();
        return $query;
    }

    public function toSqlDestroy($id = null)
    {
        if ($id !== null) {
            $this->db->where($this->primaryKey, $id);
        }

        $query = $this->db->get_compiled_delete($this->table, false);
        $this->resetQuery();
        return $query;
    }

    /**
     * Get the results of the query
     *
     * @return array|object|json Results based on returnType
     */
    public function get()
    {
        // Store the original memory limit
        $originalMemoryLimit = ini_get('memory_limit');

        try {
            // Increase memory limit for this operation
            ini_set('memory_limit', '1G');

            $result = $this->db->get($this->table)->result_array();
            $result = $this->loadRelations($result);
            $formattedResult = $this->formatResult($result);
            $this->resetQuery();

            // Restore the original memory limit
            ini_set('memory_limit', $originalMemoryLimit);

            return $formattedResult;
        } catch (Exception $e) {
            // Ensure memory limit is restored even if an exception occurs
            ini_set('memory_limit', $originalMemoryLimit);
            throw $e; // Re-throw the exception after cleanup
        }
    }

    /**
     * Fetch a single row from the query results
     *
     * @return array|object|json Result based on returnType
     */
    public function fetch()
    {
        $result = $this->db->get($this->table)->row_array();
        $result = $this->loadRelations([$result]);
        $this->resetQuery();
        return $this->formatResult($result[0]);
    }

    public function first()
    {
        $this->orderBy($this->primaryKey, 'ASC');
        $this->limit(1);
        return $this->fetch();
    }

    public function last()
    {
        $this->orderBy($this->primaryKey, 'DESC');
        $this->limit(1);
        return $this->fetch();
    }

    public function find($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

    # PAGINATION SECTION

    public function setPaginateFilterColumn($column = [])
    {
        $this->_paginateColumn = is_array($column) ? $column : [];
        return $this;
    }

    /**
     * Paginate the query results
     *
     * @param int $perPage Items per page
     * @param int|null $page Current page
     * @return array Paginated results
     */
    public function paginate($perPage = 10, $page = null, $searchValue = '')
    {
        $page = $page ?: ($this->input->get('page') ? $this->input->get('page') : 1);
        $offset = ($page - 1) * $perPage;

        $this->_paginateSearchValue = trim($searchValue);
        $columns = $this->db->list_fields($this->table);

        // Count total rows before filter
        $countTempTotal = clone $this->db;
        $totalRecords = (int) $countTempTotal->count_all_results($this->table);
        unset($countTempTotal);

        // Apply filter
        $this->_paginateFilter($columns);

        // Count total rows after filter
        $countTempQuery = clone $this->db;
        $total = (int) $countTempQuery->count_all_results($this->table);
        unset($countTempQuery);

        // Fetch only the required page of results
        $this->limit($perPage)->offset($offset);
        $data = $this->get();

        // Calculate pagination details
        $totalPages = (int) ceil($total / $perPage);
        $nextPage = ($page < $totalPages) ? $page + 1 : null;
        $previousPage = ($page > 1) ? $page - 1 : null;

        // Configure pagination
        $this->load->library('pagination');
        $config = [
            'base_url' => current_url(),
            'total_rows' => $total,
            'per_page' => $perPage,
            'use_page_numbers' => TRUE,
            'page_query_string' => TRUE,
            'query_string_segment' => 'page',
            'full_tag_open' => '<ul class="pagination">',
            'full_tag_close' => '</ul>',
            'first_link' => '&laquo;',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',
            'last_link' => '&raquo;',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',
            'next_link' => '&gt;',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',
            'prev_link' => '&lt;',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',
            'cur_tag_open' => '<li class="page-item active"><a class="page-link">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',
            'attributes' => ['class' => 'page-link'],
        ];

        $this->pagination->initialize($config);

        return [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $total,
            'data' => $data,
            'current_page' => $page,
            'next_page' => $nextPage,
            'previous_page' => $previousPage,
            'last_page' => $totalPages,
            'error' => $page > $totalPages ? "Current page ({$page}) is more than total pages ({$totalPages})" : '',
            'links' => $this->pagination->create_links()
        ];
    }

    public function paginate_ajax($dataPost)
    {
        $this->_paginateSearchValue = trim($dataPost['search']['value']);
        $columns = empty($this->_paginateColumn) ? $this->db->list_fields($this->table) : $this->_paginateColumn;

        // Count total rows before filter
        $countTempTotal = clone $this->db;
        $totalRecords = (int) $countTempTotal->count_all_results($this->table);
        unset($countTempTotal);

        // Apply filter
        $this->_paginateFilter($columns);

        // Count total rows after filter
        $countTempQuery = clone $this->db;
        $total = (int) $countTempQuery->count_all_results($this->table);
        unset($countTempQuery);

        // Fetch only the required page of results
        $this->limit($dataPost['length'])->offset($dataPost['start']);

        // Apply Order data if exists
        $orderBy = $dataPost['order'];
        if (!empty($orderBy)) {
            $this->orderBy($columns[$orderBy[0]['column']], $orderBy[0]['dir']);
        }

        return [
            'draw' => $dataPost['draw'],
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $total,
            'data' => $this->get(),
        ];
    }

    private function _paginateFilter($columns)
    {
        $searchValue = $this->_paginateSearchValue;

        if (empty($searchValue)) {
            return;
        }

        $i = 0;
        $this->db->group_start();
        foreach ($columns as $column) {
            if (!empty($column)) {
                if ($i === 0) {
                    $this->db->like($column, $searchValue);
                } else {
                    $this->db->or_like($column, $searchValue);
                }
            }
            $i++;
        }
        $this->db->group_end();
    }

    # RELATION SECTION

    public function hasMany($modelName, $foreignKey, $localKey = null)
    {
        $this->relations[$modelName] = ['type' => 'hasMany', 'model' => $modelName, 'foreignKey' => $foreignKey, 'localKey' => $localKey ?: $this->primaryKey];
        return $this;
    }

    public function hasOne($modelName, $foreignKey, $localKey = null)
    {
        $this->relations[$modelName] = ['type' => 'hasOne', 'model' => $modelName, 'foreignKey' => $foreignKey, 'localKey' => $localKey ?: $this->primaryKey];
        return $this;
    }

    public function belongsTo($modelName, $foreignKey, $ownerKey = null)
    {
        $this->relations[$modelName] = ['type' => 'belongsTo', 'model' => $modelName, 'foreignKey' => $foreignKey, 'ownerKey' => $ownerKey ?: $this->primaryKey];
        return $this;
    }

    # EAGER LOADING SECTION

    public function setParallelWorkers($workers)
    {
        $this->parallelMaxWorker = max(1, min(10, (int)$workers));
        return $this;
    }

    public function setParallelStatus($status)
    {
        $this->parallelStatus = (bool)$status;
        return $this;
    }

    public function setParallelTimeout($timeout)
    {
        $this->parallelTimeout = (int)$timeout;
        return $this;
    }

    public function setParallelTempDir($dir)
    {
        $this->parallelTempDir = $dir;
        return $this;
    }

    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        foreach ($relations as $name => $constraints) {
            if (is_numeric($name)) {
                $name = $constraints;
                $constraints = null;
            }

            $this->eagerLoad[$name] = $constraints;
        }

        return $this;
    }

    private function loadRelations($results)
    {
        if (empty($this->eagerLoad) || empty($results)) {
            return $results;
        }

        foreach ($this->eagerLoad as $relation => $constraints) {
            $relations = explode('.', $relation);
            $this->loadNestedRelation($this, $results, $relations, $constraints);
        }

        return $results;
    }

    private function loadNestedRelation($currentInstance, &$results, $relations, $constraints = null)
    {
        if (count($relations) == 1) {
            $currentRelation = $relations[0];
            $relatedInstance = $currentInstance;
        } else {
            $model = ucfirst($relations[0]) . '_model';
            $this->load->model($model);
            $relatedInstance = $this->{$model};
            $currentRelation = $relations[1];
        }

        if (method_exists($relatedInstance, $currentRelation)) {
            $configRelation = $relatedInstance->{$currentRelation}();

            if (isset($configRelation->relations)) {
                foreach ($configRelation->relations as $modelName => $rels) {
                    $relationType = $rels['type'];
                    $foreignKey = $rels['foreignKey'];

                    $this->load->model($modelName);
                    $relationInstance = $this->{$modelName};

                    if ($constraints instanceof Closure) {
                        $constraints($relationInstance);
                    }

                    switch ($relationType) {
                        case 'hasMany':
                        case 'hasOne':
                            $localKey = $rels['localKey'];
                            $parentIds = array_unique(array_filter(count($relations) > 1 ? $this->searchRelatedKeys($results, $relations[0] . '.' . $localKey) : array_column($results, $localKey)));
                            $relatedData = $this->whereInWithPossibleParallel($relationInstance, $foreignKey, $parentIds);
                            $this->matchRelations($results, $relatedData, $currentRelation, $localKey, $foreignKey, $relationType, count($relations) > 1 ? $relations[0] : null);
                            break;

                        case 'belongsTo':
                            $ownerKey = $rels['ownerKey'];
                            $foreignIds = array_unique(array_filter(count($relations) > 1 ? $this->searchRelatedKeys($results, $relations[0] . '.' . $foreignKey) : array_column($results, $foreignKey)));
                            $relatedData = $this->whereInWithPossibleParallel($relationInstance, $ownerKey, $foreignIds);
                            $this->matchRelations($results, $relatedData, $currentRelation, $foreignKey, $ownerKey, $relationType, count($relations) > 1 ? $relations[0] : null);
                            break;
                    }
                }
            }
        } else {
            throw new Exception("Method {$currentRelation} does not exist in the model " . get_class($this));
        }
    }

    private function whereInWithPossibleParallel($model, $column, $values, $chunkSize = 1000)
    {
        if (count($values) < 1000 || !$this->parallelStatus) {
            // Use regular approach for less than 1000 values or if parallel processing is disabled
            return $this->regularWhereIn($model, $column, $values, $chunkSize);
        } else {
            // Use parallel approach for 2000 or more values
            return $this->parallelWhereIn($model, $column, $values, $chunkSize, $this->parallelMaxWorker);
        }
    }

    private function regularWhereIn($model, $column, $values, $chunkSize = 1000)
    {
        $chunks = array_chunk($values, $chunkSize);
        $result = [];

        foreach ($chunks as $chunk) {
            $result = array_merge($result, $model->whereIn($column, $chunk)->get());
        }

        return $result;
    }

    private function parallelWhereIn($model, $column, $values, $chunkSize = 1000, $maxWorkers = 3)
    {
        $chunks = array_chunk($values, $chunkSize);
        $totalChunks = count($chunks);
        $result = [];

        for ($i = 0; $i < $totalChunks; $i += $maxWorkers) {
            $workers = [];
            for ($j = 0; $j < $maxWorkers && ($i + $j) < $totalChunks; $j++) {
                $workers[] = new ParallelWorker(function () use ($model, $column, $chunks, $i, $j) {
                    return $model->whereIn($column, $chunks[$i + $j])->get();
                }, $this->parallelTempDir, $this->parallelTimeout);
            }

            foreach ($workers as $worker) {
                $worker->start();
            }

            foreach ($workers as $worker) {
                try {
                    $workerResult = $worker->getResult();
                    if ($workerResult !== false) {
                        $result = array_merge($result, $workerResult);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Parallel processing error: ' . $e->getMessage());
                }
            }
        }

        return $result;
    }

    private function matchRelations(&$results, $relatedData, $relation, $localKey, $foreignKey, $type, $parentRelation = null)
    {
        $relatedDataMap = [];
        foreach ($relatedData as $item) {
            $relatedDataMap[$item[$foreignKey]][] = $item;
        }

        if (is_null($parentRelation)) {
            foreach ($results as &$result) {
                $key = $result[$localKey];
                if (isset($relatedDataMap[$key])) {
                    $result[$relation] = $type === 'hasOne' ? $relatedDataMap[$key][0] : $relatedDataMap[$key];
                } else {
                    $result[$relation] = $type === 'hasOne' ? null : [];
                }
            }
        } else {
            foreach ($results as &$result) {
                if (isset($result[$parentRelation])) {
                    foreach ($result[$parentRelation] as &$nestedResult) {
                        if (isset($nestedResult[$localKey])) {
                            $key = $nestedResult[$localKey];
                            if (isset($relatedDataMap[$key])) {
                                $nestedResult[$relation] = $type === 'hasOne' ? $relatedDataMap[$key][0] : $relatedDataMap[$key];
                            } else {
                                $nestedResult[$relation] = $type === 'hasOne' ? null : [];
                            }
                        } else {
                            $key = $result[$parentRelation][$localKey] ?? null;
                            if (!empty($key) && isset($relatedDataMap[$key])) {
                                $result[$parentRelation][$relation] = $type === 'hasOne' ? $relatedDataMap[$key][0] : $relatedDataMap[$key];
                            } else {
                                $result[$parentRelation][$relation] = null;
                            }
                        }
                    }
                }
            }
        }
    }

    # CRUD functions

    /**
     * Ignore validation rules temporarily
     *
     * @return $this
     */
    public function ignoreValidation()
    {
        $this->_ignoreValidation = true;
        return $this;
    }

    /**
     * Set override validation rules
     *
     * @param array $validation This will override all the validation set in the model.
     * @return $this
     */
    public function setValidationRules($validation)
    {
        if (!is_array($validation)) {
            throw new Exception('Validation rules must be an array.');
        }

        $this->_overrideValidation = $validation;
        return $this;
    }

    /**
     * Set custom validation rules
     *
     * @param array $validation This will set the custom validation or the addition rules.
     * @return $this
     */
    public function setCustomValidationRules($validation)
    {
        if (!is_array($validation)) {
            throw new Exception('Additional validation rules must be an array.');
        }

        if (empty($this->_validation) && empty($this->_insertValidation) && empty($this->_updateValidation)) {
            throw new Exception('No validation rules found. Please set the validation in model first.');
        }

        $this->_validationCustomize = $validation;
        return $this;
    }

    /**
     * Insert a new record or update an existing one
     *
     * @param array $attributes The attributes to search for
     * @param array $values The values to update or insert
     * @return array Response with status code, data, action, and primary key
     */
    public function insertOrUpdate($attributes = [], $values = [])
    {
        // Merge $attributes and $values
        $data = array_merge($attributes, $values);

        // Check if a record exists with the given attributes
        $existingRecord = get_instance()->db->from($this->table)->where($attributes)->get()->row_array();

        if ($existingRecord) {
            // If record exists, update it
            $id = $existingRecord[$this->primaryKey];
            return $this->patch($data, $id);
        } else {
            // If record doesn't exist, create it
            return $this->create($data);
        }
    }

    /**
     * Create a new record
     *
     * @param array $data Data to insert
     * @return array Response with status code, data, action, and primary key
     */
    public function create($data)
    {
        $data = $this->filterData($data);
        $validationRules = !empty($this->_insertValidation) ? $this->_insertValidation : $this->_validation;
        if ($this->_runValidation($data, $validationRules, 'create')) {
            try {

                if ($this->timestamps) {
                    $data[$this->_created_at_field] = date($this->timestamps_format);
                }

                $success = $this->db->insert($this->table, $data);
                $insertId = $this->db->insert_id();

                if (!$success) {
                    throw new Exception('Failed to insert record');
                }

                $this->resetQuery();

                return [
                    'code' => 201,
                    $this->primaryKey => $insertId,
                    'data' => $data,
                    'message' => 'Inserted successfully',
                    'action' => 'create',
                ];
            } catch (Exception $e) {
                log_message('error', 'Create Error: ' . $e->getMessage());
                return [
                    'code' => 500,
                    'error' => $e->getMessage(),
                    'message' => 'Failed to insert new data',
                    'action' => 'create',
                ];
            }
        } else {
            return $this->_validationError;
        }
    }

    /**
     * Update an existing record
     *
     * @param array $data Data to update
     * @param mixed $id ID of the record to update
     * @return array Response with status code, data, action, and primary key
     */
    public function patch($data, $id = NULL)
    {
        $data = $this->filterData($data);
        $validationRules = !empty($this->_updateValidation) ? $this->_updateValidation : $this->_validation;

        if ($this->_runValidation($data, $validationRules, 'update')) {
            try {

                if ($this->timestamps) {
                    $data[$this->_updated_at_field] = date($this->timestamps_format);
                }

                $success = $this->db->where($this->primaryKey, $id)->update($this->table, $data);

                if (!$success) {
                    throw new Exception('Failed to update record');
                }

                $this->resetQuery();

                return [
                    'code' => 200,
                    $this->primaryKey => $id,
                    'data' => $data,
                    'message' => 'Updated successfully',
                    'action' => 'update',
                ];
            } catch (Exception $e) {
                log_message('error', 'Update Error: ' . $e->getMessage());
                return [
                    'code' => 500,
                    'error' => $e->getMessage(),
                    'message' => 'Failed to update data',
                    'action' => 'update',
                ];
            }
        } else {
            return $this->_validationError;
        }
    }

    /**
     * Delete a record
     *
     * @param mixed $id ID of the record to delete
     * @return array Response with status code, data, action, and primary key
     */
    public function destroy($id = NULL)
    {
        try {
            $data = $this->find($id);

            if (!$data) {
                throw new Exception('Record not found');
            }

            $this->db->trans_start();
            $success = $this->db->delete($this->table, [$this->primaryKey => $id]);
            $this->db->trans_complete();

            if (!$success || $this->db->trans_status() === FALSE) {
                throw new Exception('Failed to delete record');
            }

            return [
                'code' => 200,
                $this->primaryKey => $id,
                'data' => $data,
                'message' => 'Removed successfully',
                'action' => 'delete',
            ];
        } catch (Exception $e) {
            log_message('error', 'Delete Error: ' . $e->getMessage());
            $this->db->trans_rollback(); // Rollback the transaction
            return [
                'code' => 500,
                'error' => $e->getMessage(),
                'message' => 'Failed to removed data',
                'action' => 'delete',
            ];
        }
    }

    /**
     * Filter data based on fillable and protected fields
     *
     * @param array $data Data to filter
     * @return array Filtered data
     */
    private function filterData($data)
    {
        if (!empty($data) && is_array($data)) {
            if ($this->fillable !== null) {
                $data = array_intersect_key($data, array_flip($this->fillable));
            }

            if ($this->protected !== null) {
                $data = array_diff_key($data, array_flip($this->protected));
            }
        }

        return $data;
    }

    /**
     * Runs validation on the provided data using the specified validation rules.
     *
     * @param array $data The data to validate.
     * @param array $validationRules The validation rules to apply.
     * @param string $action The action being performed (e.g., "create", "update").
     * @return bool True if validation passes, false otherwise.
     */
    private function _runValidation($data, $validationRules, $action)
    {
        // Reset old validation error
        $this->_validationError = [];

        if (!$this->_ignoreValidation) {
            // Merge validation rules and override if any
            $validation = !empty($this->_overrideValidation) ? $this->_overrideValidation : array_merge($validationRules, $this->_validationCustomize);

            if (!empty($validation)) {
                // Filter out validation rules that don't have corresponding keys in $data
                $filteredValidation = array_filter($validation, function ($rule, $key) use ($data) {
                    return array_key_exists($key, $data);
                }, ARRAY_FILTER_USE_BOTH);

                if (!empty($filteredValidation)) {
                    // Load the form validation library
                    $this->load->library('form_validation');

                    // Reset validation to clear previous rules and data
                    $this->form_validation->reset_validation();

                    // Set the data to be validated
                    $this->form_validation->set_data($data);

                    // Set the filtered validation rules
                    $this->form_validation->set_rules($filteredValidation);

                    // Run validation and return errors if any
                    if (!$this->form_validation->run()) {
                        // Collect validation error messages and format as an unordered list without <p> tags
                        $errors = validation_errors();
                        
                        // Remove <p> tags from errors
                        $errors = strip_tags($errors); // Remove all HTML tags
                        $errorsArray = explode("\n", trim($errors));
    
                        // Build the unordered list
                        $errorsList = "<ul>";
                        foreach ($errorsArray as $error) {
                            if (!empty($error)) {
                                $errorsList .= "<li>" . htmlspecialchars(trim($error)) . "</li>";
                            }
                        }
                        $errorsList .= "</ul>";
    
                        log_message('error', ucfirst($action) . ' Validation Error: ' . $errorsList);
                        $this->_validationError = [
                            'code' => 422,
                            'data' => $data,
                            'message' => ucfirst($action) . ' operation failed: ' . $errorsList,
                            'action' => $action,
                            'error' => $errorsList // Save the formatted error list
                        ];
                        return false;
                    }
                }
            }
        }

        return true;
    }

    # HELPER SECTION

    private function searchRelatedKeys($data, $keyToSearch)
    {
        $result = [];

        $keys = explode('.', $keyToSearch);

        $searchRecursive = function ($array, $keys, $currentDepth = 0) use (&$searchRecursive, &$result) {
            foreach ($array as $key => $value) {
                if ($key === $keys[$currentDepth]) {
                    if ($currentDepth === count($keys) - 1) {
                        $result[] = $value;
                    } elseif (is_array($value)) {
                        $searchRecursive($value, $keys, $currentDepth + 1);
                    }
                } elseif (is_array($value)) {
                    $searchRecursive($value, $keys, $currentDepth);
                }
            }
        };

        $searchRecursive($data, $keys);

        return $result;
    }

    private function whereNested(Closure $callback)
    {
        $this->db->group_start();
        $callback($this);
        $this->db->group_end();
        return $this;
    }

    private function forSubQuery(Closure $callback)
    {
        $query = $this->db->from($this->table);
        $callback($query);
        return $query->get_compiled_select();
    }

    /**
     * Apply a condition to the query
     *
     * @param string $method Query method to use
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @param string $operator Comparison operator
     * @throws InvalidArgumentException
     */
    private function applyCondition($method, $column, $value, $operator)
    {
        static $operatorCache = [];

        $upperOperator = strtoupper($operator);

        // Cache the result of in_array check
        if (!isset($operatorCache[$upperOperator])) {
            $operatorCache[$upperOperator] = in_array($upperOperator, $this->allowedOperators);
        }

        if (!$operatorCache[$upperOperator]) {
            throw new InvalidArgumentException("Invalid operator: $operator");
        }

        switch ($upperOperator) {
            case '=':
                $this->db->$method($column, $value);
                break;
            case 'LIKE':
            case 'NOT LIKE':
                $this->db->$method("$column $upperOperator", $value);
                break;
            default:
                $this->db->$method($column . $operator, $value);
        }
    }

    private function validateDayMonth($value, $month = false)
    {
        $max = $month ? 12 : 31;
        if (!is_numeric($value) || $value < 1 || $value > $max) {
            throw new InvalidArgumentException("Invalid value for day/month: $value");
        }
    }

    protected function validateYear($value)
    {
        if (!is_numeric($value) || strlen((string) $value) !== 4) {
            throw new \InvalidArgumentException('Invalid year. Must be a four-digit number.');
        }

        if ($value < 1900 || $value > date('Y')) {
            throw new InvalidArgumentException("Invalid year: $value");
        }
    }

    protected function validateInteger($value, $type, $positive = true)
    {
        if (!is_numeric($value) || ($positive && $value <= 0)) {
            throw new InvalidArgumentException("Invalid $type value: $value");
        }
        return (int) $value;
    }

    /**
     * Sanitize a value for database input
     *
     * @param mixed $value Value to sanitize
     * @return mixed
     */
    protected function sanitizeValue($value)
    {
        if (is_array($value)) {
            return array_map([$this->db, 'escape'], $value);
        }

        return $this->db->escape($value);
    }

    public function toArray()
    {
        $this->returnType = 'array';
        return $this;
    }

    public function toObject()
    {
        $this->returnType = 'object';
        return $this;
    }

    public function toJson()
    {
        $this->returnType = 'json';
        return $this;
    }

    private function formatResult($result)
    {
        $resultFormat = null;

        if (!empty($result) && $this->hidden) {
            $result = $this->removeHiddenDataRecursive($result);
        }

        if (!empty($result) && $this->appends) {
            $result = $this->appendData($result);
        }

        switch ($this->returnType) {
            case 'object':
                $resultFormat = json_decode(json_encode($this->_safeOutputSanitize($result)));
                break;
            case 'json':
                $resultFormat = json_encode($this->_safeOutputSanitize($result));
                break;
            default:
                $resultFormat = $this->_safeOutputSanitize($result);
        }

        $this->resetQuery();
        return $resultFormat;
    }

    private function resetQuery()
    {
        $this->db = $this->load->database($this->connection, TRUE);
        $this->primaryKey = 'id';
        $this->relations = [];
        $this->eagerLoad = [];
        $this->returnType = 'array';
        $this->_paginateColumn = [];
    }

    # SECURITY HELPER

    /**
     * Enable safe output against XSS injection
     *
     * @return $this
     */
    public function safeOutput()
    {
        $this->_secureOutput = true;
        $this->_secureOutputException = [];
        return $this;
    }

    /**
     * Enable safe output against XSS injection with exception key
     *
     * @param array $exception The key array that will be except from sanitize
     * @return $this
     */
    public function safeOutputWithException($exception = [])
    {
        $this->_secureOutput = true;
        $this->_secureOutputException = $exception;
        return $this;
    }

    /**
     * Sanitize output data if safe output is enabled
     *
     * @param mixed $data Data to sanitize
     * @return mixed
     */
    private function _safeOutputSanitize($data)
    {
        if (!$this->_secureOutput) {
            return $data;
        }

        // Early return if data is null or empty
        if (is_null($data) || $data === '') {
            return $data;
        }

        return $this->sanitize($data);
    }

    /**
     * Recursively sanitize data
     *
     * @param mixed $value Value to sanitize
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function sanitize($value = null)
    {
        // Check if $value is not null or empty
        if (!isset($value) || is_null($value)) {
            return $value;
        }

        // If $value is an array, sanitize its values while checking each key against the exception list
        if (is_array($value)) {
            if (!empty($this->_secureOutputException)) {
                foreach ($value as $key => $item) {
                    // Check if the key exists in the exception list
                    if (in_array($key, $this->_secureOutputException)) {
                        continue; // Skip sanitization for this key
                    }

                    // Recursively sanitize the value for this key
                    $value[$key] = $this->sanitize($item);
                }

                return $value;
            } else {
                return array_map([$this, 'sanitize'], $value);
            }
        }

        // Sanitize input based on data type
        switch (gettype($value)) {
            case 'string':
                if (isset($this->_secureOutputException) && in_array($value, $this->_secureOutputException)) {
                    return $value; // Skip sanitization if the string is in the exception list
                }
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8'); // Apply XSS protection and trim
            case 'integer':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            case 'double':
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'boolean':
                return (bool) $value;
            default:
                // Handle unexpected data types (consider throwing an exception)
                throw new \InvalidArgumentException("Unsupported data type for sanitization: " . gettype($value));
        }
    }


    /**
     * Recursively removes hidden keys from the given data array.
     *
     * This method takes an array ($data) and an array of hidden keys ($hidden).
     * It removes keys listed in the $hidden array from $data.
     * If a value in $data is an array, the method is called recursively.
     *
     * @param array $data The data array from which to remove hidden keys.
     * @return array The modified data array with hidden keys removed.
     */
    protected function removeHiddenDataRecursive($data)
    {
        // Flip the hidden array for faster key lookups
        $hiddenFlipped = array_flip($this->hidden);

        // Remove hidden keys
        $data = array_diff_key($data, $hiddenFlipped);

        // Recursively process nested arrays
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->removeHiddenDataRecursive($value, $this->hidden);
            }
        }

        return $data;
    }

    /**
     * Check if the result is a multidimensional array
     *
     * @param array $result The result to check
     * @return bool True if multidimensional, false otherwise
     */
    private function isMultiCustomCheck($result)
    {
        if (!is_array($result)) {
            return false;
        }

        // If it's an empty array, we'll consider it as non-multi
        if (empty($result)) {
            return false;
        }

        // Check if the first element is an array
        return is_array(reset($result));
    }

    public function showColumnHidden()
    {
        $this->hidden = null;
        return $this;
    }

    public function setColumnHidden($hidden = null)
    {
        $this->hidden = $hidden;
        return $this;
    }

    # APPEND DATA HELPER

    public function setAppends($appends = null)
    {
        $this->appends = $appends;
        return $this;
    }

    private function appendData($resultQuery)
    {
        if (empty($resultQuery) || empty($this->appends) || empty($this->fillable)) {
            return $resultQuery;
        }

        $isMulti = $this->isMultiCustomCheck($resultQuery);
        $appendMethods = $this->getAppendMethods();

        if ($isMulti) {
            foreach ($resultQuery as &$item) {
                $this->appendToSingle($item, $appendMethods);
            }
        } else {
            $this->appendToSingle($resultQuery, $appendMethods);
        }

        return $resultQuery;
    }

    private function getAppendMethods()
    {
        $methods = [];
        foreach ($this->appends as $append) {
            $methodName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $append))) . 'Attribute';
            if (method_exists($this, $methodName)) {
                $methods[$append] = $methodName;
            }
        }
        return $methods;
    }

    private function appendToSingle(&$item, $appendMethods)
    {
        $this->setAttributes($item);

        foreach ($appendMethods as $append => $method) {
            $item[$append] = $this->$method();
        }

        $this->unsetAttributes();
    }

    private function setAttributes($data)
    {
        foreach ($this->fillable as $attribute) {
            $this->$attribute = $data[$attribute] ?? null;
        }
    }

    private function unsetAttributes()
    {
        foreach ($this->fillable as $attribute) {
            unset($this->$attribute);
        }
    }
}


class ParallelWorker
{
    private $pid;
    private $callback;
    private $tmpFile;
    private $isWindows;
    private $timeout;
    private $startTime;

    public function __construct(callable $callback, $tempDir = NULL, $timeout = 3600)
    {
        $this->callback = $callback;
        $this->timeout = $timeout;
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->createTempFile($tempDir);
    }

    private function createTempFile($dir = NULL)
    {
        $folder = FCPATH . "application" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "ParallelWorker";
        $tempDir = empty($dir) ? $folder : $folder . DIRECTORY_SEPARATOR . $dir;

        // Check if the directory exists and is writable
        if (!is_dir($tempDir) || !is_writable($tempDir)) {
            // Attempt to create the directory if it doesn't exist
            if (!mkdir($tempDir, 0644, true)) {
                throw new RuntimeException("Failed to create temporary directory: $tempDir");
            }
        }

        // Create the temporary file
        $this->tmpFile = tempnam($tempDir, 'worker_');
        if ($this->tmpFile === false) {
            throw new RuntimeException("Failed to create temporary file in: $tempDir");
        }

        // Ensure the file is readable and writable
        if (!chmod($this->tmpFile, 0644)) {
            throw new RuntimeException("Failed to set permissions on temporary file: $this->tmpFile");
        }
    }

    public function start()
    {
        $this->startTime = time();
        if ($this->isWindows) {
            $this->windowsStart();
        } else {
            $this->linuxStart();
        }
    }

    private function windowsStart()
    {
        $result = $this->executeCallback();
        $this->writeResult($result);
    }

    private function linuxStart()
    {
        if (!function_exists('pcntl_fork')) {
            $this->windowsStart();
            return;
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new RuntimeException('Could not fork process');
        } elseif ($pid) {
            $this->pid = $pid;
        } else {
            $result = $this->executeCallback();
            $this->writeResult($result);
            exit(0);
        }
    }

    private function executeCallback()
    {
        try {
            return call_user_func($this->callback);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function writeResult($result)
    {
        $serialized = serialize($result);
        if (file_put_contents($this->tmpFile, $serialized) === false) {
            throw new RuntimeException("Failed to write to temporary file: $this->tmpFile");
        }
    }

    public function getResult()
    {
        if ($this->isWindows) {
            return $this->windowsGetResult();
        } else {
            return $this->linuxGetResult();
        }
    }

    private function windowsGetResult()
    {
        $this->waitForCompletion();
        return $this->readAndCleanup();
    }

    private function linuxGetResult()
    {
        if (!function_exists('pcntl_waitpid')) {
            return $this->windowsGetResult();
        }

        $status = 0;
        while (true) {
            $res = pcntl_waitpid($this->pid, $status, WNOHANG);
            if ($res == -1 || $res > 0) {
                break;
            }
            if ($this->hasTimedOut()) {
                posix_kill($this->pid, SIGKILL);
                throw new RuntimeException("Process timed out after {$this->timeout} seconds");
            }
            usleep(100000); // Sleep for 100ms to prevent CPU hogging
        }

        return $this->readAndCleanup();
    }

    private function waitForCompletion()
    {
        while (!file_exists($this->tmpFile) || filesize($this->tmpFile) == 0) {
            if ($this->hasTimedOut()) {
                throw new RuntimeException("Process timed out after {$this->timeout} seconds");
            }
            usleep(100000); // Sleep for 100ms to prevent CPU hogging
        }
    }

    private function hasTimedOut()
    {
        return (time() - $this->startTime) > $this->timeout;
    }

    private function readAndCleanup()
    {
        if (!file_exists($this->tmpFile)) {
            throw new RuntimeException("Temporary file not found: $this->tmpFile");
        }

        $content = file_get_contents($this->tmpFile);
        if ($content === false) {
            throw new RuntimeException("Failed to read from temporary file: $this->tmpFile");
        }

        $result = unserialize($content);

        if (!unlink($this->tmpFile)) {
            error_log("Failed to delete temporary file: $this->tmpFile");
        }

        if (isset($result['error'])) {
            throw new RuntimeException("Worker process error: " . $result['error']);
        }

        return $result;
    }

    public function __destruct()
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }
}