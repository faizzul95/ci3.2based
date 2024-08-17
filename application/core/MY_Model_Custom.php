<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Model Class
 *
 * @category  Model
 * @Description  An extended model class for CodeIgniter 3 with advanced querying capabilities, relationship handling, and security features.
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @link      -
 * @version   0.0.1
 */

class MY_Model_Custom extends CI_Model
{
    protected $table;
    protected $primaryKey = 'id';

    protected $query;
    protected $relations = [];
    protected $eagerLoad = [];
    protected $returnType = 'array';
    protected $allowedOperators = ['=', '!=', '<', '>', '<=', '>=', '<>', 'LIKE', 'NOT LIKE'];
    protected $_secureOutput = false;

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
        $this->query = $this->db->from($this->table);
    }

    /**
     * Select columns for the query
     *
     * @param string $columns Columns to select
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->query->select($columns);
        return $this;
    }

    /**
     * Add a WHERE clause to the query
     *
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @param string $operator Comparison operator
     * @return $this
     */
    public function where($column, $value, $operator = '=')
    {
        $this->applyCondition('where', $column, $value, $operator);
        return $this;
    }

    public function orWhere($column, $value, $operator = '=')
    {
        $this->applyCondition('or_where', $column, $value, $operator);
        return $this;
    }

    public function whereDate($column, $value, $operator = '=')
    {
        $this->applyCondition('where', "DATE($column)", $value, $operator);
        return $this;
    }

    public function orWhereDate($column, $value, $operator = '=')
    {
        $this->applyCondition('or_where', "DATE($column)", $value, $operator);
        return $this;
    }

    public function whereDay($column, $value, $operator = '=')
    {
        $this->validateDayMonth($value);
        $this->applyCondition('where', "DAY($column)", $value, $operator);
        return $this;
    }

    public function orWhereDay($column, $value, $operator = '=')
    {
        $this->validateDayMonth($value);
        $this->applyCondition('or_where', "DAY($column)", $value, $operator);
        return $this;
    }

    public function whereYear($column, $value, $operator = '=')
    {
        $this->validateYear($value);
        $this->applyCondition('where', "YEAR($column)", $value, $operator);
        return $this;
    }

    public function orWhereYear($column, $value, $operator = '=')
    {
        $this->validateYear($value);
        $this->applyCondition('or_where', "YEAR($column)", $value, $operator);
        return $this;
    }

    public function whereMonth($column, $value, $operator = '=')
    {
        $this->validateDayMonth($value, true);
        $this->applyCondition('where', "MONTH($column)", $value, $operator);
        return $this;
    }

    public function orWhereMonth($column, $value, $operator = '=')
    {
        $this->validateDayMonth($value, true);
        $this->applyCondition('or_where', "MONTH($column)", $value, $operator);
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->query->where_in($column, $values);
        return $this;
    }

    public function whereNotIn($column, $values)
    {
        $this->query->where_not_in($column, $values);
        return $this;
    }

    public function orWhereIn($column, $values)
    {
        $this->query->or_where_in($column, $values);
        return $this;
    }

    public function orWhereNotIn($column, $values)
    {
        $this->query->or_where_not_in($column, $values);
        return $this;
    }

    public function whereBetween($column, $start, $end)
    {
        $this->query->where("$column BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function whereNotBetween($column, $start, $end)
    {
        $this->query->where("$column NOT BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function orWhereBetween($column, $start, $end)
    {
        $this->query->or_where("$column BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
        return $this;
    }

    public function orWhereNotBetween($column, $start, $end)
    {
        $this->query->or_where("$column NOT BETWEEN {$this->sanitizeValue($start)} AND {$this->sanitizeValue($end)}");
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
        $this->query = $this->db->query($query);
        return $this;
    }

    public function join($table, $condition, $type = 'inner')
    {
        $this->query->join($table, $condition, $type);
        return $this;
    }

    public function rightJoin($table, $condition)
    {
        $this->query->join($table, $condition, 'right');
        return $this;
    }

    public function leftJoin($table, $condition)
    {
        $this->query->join($table, $condition, 'left');
        return $this;
    }

    public function innerJoin($table, $condition)
    {
        $this->query->join($table, $condition, 'inner');
        return $this;
    }

    public function outerJoin($table, $condition)
    {
        $this->query->join($table, $condition, 'outer');
        return $this;
    }

    public function limit($limit)
    {
        $limit = $this->validateInteger($limit, 'Limit');
        $this->query->limit($limit);
        return $this;
    }

    public function offset($offset)
    {
        $offset = $this->validateInteger($offset, 'Offset', false);
        $this->query->offset($offset);
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->query->order_by($column, $direction);
        return $this;
    }

    public function groupBy($column)
    {
        $this->query->group_by($column);
        return $this;
    }

    public function groupByRaw($expression)
    {
        $this->query->group_by($expression, FALSE);
        return $this;
    }

    public function having($column, $value, $operator = '=')
    {
        $this->query->having("$column $operator", $value);
        return $this;
    }

    public function havingRaw($condition)
    {
        $this->query->having($condition, NULL, FALSE);
        return $this;
    }

    public function chunk($size, callable $callback)
    {
        $offset = 0;

        // Set the temporary data to holds the original value
        $_tempTable = $this->table;
        $_tempQuery = $this->query;
        $_tempPK = $this->primaryKey;
        $_tempRelation = $this->relations;
        $_tempEagerLoad = $this->eagerLoad;
        $_tempReturnType = $this->returnType;

        while (true) {

            // Set back to original value for next details
            $this->table = $_tempTable;
            $this->query = $_tempQuery;
            $this->primaryKey = $_tempPK;
            $this->relations = $_tempRelation;
            $this->eagerLoad = $_tempEagerLoad;
            $this->returnType = $_tempReturnType;

            $this->limit($size)->offset($offset);
            $results = $this->get();

            if (empty($results)) {
                break;
            }

            if (call_user_func($callback, $results) === false) {
                break;
            }

            $offset += $size;
        }

        // Unset the variables to free memory
        unset($_tempTable, $_tempQuery, $_tempPK, $_tempRelation, $_tempEagerLoad, $_tempReturnType);

        // Reset internal properties for next query
        $this->resetQuery();

        return $this;
    }

    public function count()
    {
        return $this->query->count_all_results();
    }

    public function toSql()
    {
        return $this->query->get_compiled_select();
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

            $result = $this->query->get()->result_array();
            $result = $this->loadRelations($result);
            $formattedResult = $this->formatResult($result);

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
        $result = $this->query->get()->row_array();
        $result = $this->loadRelations([$result]);
        return $this->formatResult($result[0]);
    }

    public function first()
    {
        $result = $this->query->limit(1)->get()->row_array();
        $result = $this->loadRelations([$result]);
        return $this->formatResult($result[0]);
    }

    public function find($id)
    {
        return $this->where($this->primaryKey, $this->sanitizeValue($id))->first();
    }

    /**
     * Paginate the query results
     *
     * @param int $perPage Items per page
     * @param int|null $page Current page
     * @param int $draw Draw count for DataTables
     * @return array Paginated results
     */
    public function paginate($perPage = 10, $page = null, $searchValue = '', $draw = 1)
    {
        $page = $page ?: ($this->input->get('page') ? $this->input->get('page') : 1);
        $offset = ($page - 1) * $perPage;

        // Apply filter
        $this->paginateFilter($searchValue);

        // Count total rows
        $countQuery = clone $this->query;
        $total = (int) $countQuery->select('COUNT(*) as count')->get()->row()->count;
        unset($countQuery);

        // Fetch only the required page of results
        $this->limit($perPage)->offset($offset);
        $data = $this->get();

        // Calculate pagination details
        $totalPages = ceil($total / $perPage);
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
            'draw' => $draw,
            'recordsTotal' => $total,
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

    protected function paginateFilter($searchValue)
    {
        if (empty($searchValue)) {
            return;
        }

        $columns = $this->db->list_fields($this->table);
        $this->query->group_start();
        foreach ($columns as $column) {
            $this->query->or_like($column, $searchValue);
        }
        $this->query->group_end();
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

    public function belongsToMany($modelName, $pivotTable, $foreignKey, $relatedKey)
    {
        $this->relations[$modelName] = ['type' => 'belongsToMany', 'model' => $modelName, 'pivotTable' => $pivotTable, 'foreignKey' => $foreignKey, 'relatedKey' => $relatedKey];
        return $this;
    }

    # EAGER LOADING SECTION

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

    protected function loadRelations($results)
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

    protected function loadNestedRelation($currentInstance, &$results, $relations, $constraints = null)
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
                            $parentIds = array_unique(count($relations) > 1 ? $this->searchRelatedKeys($results, $relations[0] . '.' . $localKey) : array_column($results, $localKey));
                            $relatedData = $this->chunkedWhereIn($relationInstance, $foreignKey, $parentIds);
                            $this->matchRelations($results, $relatedData, $currentRelation, $localKey, $foreignKey, $relationType, count($relations) > 1 ? $relations[0] : null);
                            break;

                        case 'belongsTo':
                            $ownerKey = $rels['ownerKey'];
                            $foreignIds = array_unique(count($relations) > 1 ? $this->searchRelatedKeys($results, $relations[0] . '.' . $foreignKey) : array_column($results, $foreignKey));
                            $relatedData = $this->chunkedWhereIn($relationInstance, $ownerKey, $foreignIds);
                            $this->matchRelations($results, $relatedData, $currentRelation, $foreignKey, $ownerKey, $relationType, count($relations) > 1 ? $relations[0] : null);
                            break;
                    }
                }
            }
        } else {
            throw new Exception("Method {$currentRelation} does not exist in the model " . get_class($this));
        }
    }

    protected function chunkedWhereIn($model, $column, $values, $chunkSize = 1000)
    {
        $result = [];

        foreach (array_chunk($values, $chunkSize) as $chunk) {
            $result = array_merge($result, $model->whereIn($column, $chunk)->get());
        }

        return $result;
    }

    protected function matchRelations(&$results, $relatedData, $relation, $localKey, $foreignKey, $type, $parentRelation = null)
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
                            $result[$parentRelation][$relation] = $type === 'hasOne' ? $relatedDataMap[1][0] : $relatedDataMap[1];
                        }
                    }
                }
            }
        }
    }

    # HELPER SECTION

    protected function searchRelatedKeys($data, $keyToSearch)
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

    /**
     * Apply a condition to the query
     *
     * @param string $method Query method to use
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @param string $operator Comparison operator
     * @throws InvalidArgumentException
     */
    protected function applyCondition($method, $column, $value, $operator)
    {
        if (!in_array(strtoupper($operator), $this->allowedOperators)) {
            throw new InvalidArgumentException("Invalid operator: $operator");
        }

        $this->query->$method($column, $value, $operator);
    }

    protected function validateDayMonth($value, $month = false)
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

    protected function formatResult($result)
    {
        $resultFormat = null;
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

    protected function resetQuery()
    {
        $this->query = $this->db->from($this->table);
        $this->primaryKey = 'id';
        $this->relations = [];
        $this->eagerLoad = [];
        $this->returnType = 'array';
    }

    # SECURITY HELPER

    /**
     * Enable or disable safe output
     *
     * @param bool $enable Whether to enable safe output
     * @return $this
     */
    public function safeOutput($enable = true)
    {
        $this->_secureOutput = $enable;
        return $this;
    }

    /**
     * Sanitize output data if safe output is enabled
     *
     * @param mixed $data Data to sanitize
     * @return mixed
     */
    protected function _safeOutputSanitize($data)
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
    protected function sanitize($value = null)
    {
        // Check if $value is not null or empty
        if (!isset($value) || is_null($value)) {
            return $value;
        }

        // Sanitize input based on data type
        switch (gettype($value)) {
            case 'string':
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');  // Apply XSS protection and trim
            case 'integer':
            case 'double':
                return $value;
            case 'boolean':
                return (bool) $value;
            case 'array':
                return array_map([$this, 'sanitize'], $value);
            default:
                // Handle unexpected data types (consider throwing an exception)
                throw new \InvalidArgumentException("Unsupported data type for sanitization: " . gettype($value));
        }
    }
}
