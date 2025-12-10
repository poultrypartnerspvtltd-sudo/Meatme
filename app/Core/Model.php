<?php

namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $attributes = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
    
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }
    
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $result = $this->db->fetch($sql, ['id' => $id]);
        
        if ($result) {
            $this->fill($result);
            return $this->hideAttributes($result);
        }
        
        return null;
    }
    
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        $result = $this->db->fetch($sql, ['value' => $value]);
        
        if ($result) {
            $this->fill($result);
            return $this->hideAttributes($result);
        }
        
        return null;
    }
    
    public function all($orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = $this->db->fetchAll($sql);
        
        return array_map([$this, 'hideAttributes'], $results);
    }
    
    public function where($conditions, $orderBy = null, $limit = null)
    {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereClause);
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = $this->db->fetchAll($sql, $params);
        
        return array_map([$this, 'hideAttributes'], $results);
    }
    
    public function create($data)
    {
        $filteredData = $this->filterFillable($data);
        $filteredData['created_at'] = date('Y-m-d H:i:s');
        $filteredData['updated_at'] = date('Y-m-d H:i:s');
        
        $id = $this->db->insert($this->table, $filteredData);
        
        return $this->find($id);
    }
    
    public function update($id, $data)
    {
        $filteredData = $this->filterFillable($data);
        $filteredData['updated_at'] = date('Y-m-d H:i:s');
        
        $affected = $this->db->update(
            $this->table,
            $filteredData,
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );
        
        if ($affected > 0) {
            return $this->find($id);
        }
        
        return false;
    }
    
    public function delete($id)
    {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );
    }
    
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $result = $this->db->fetch($sql, $params);
        
        return $result['count'] ?? 0;
    }
    
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $this->count($conditions);
        
        // Build query
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $results = $this->db->fetchAll($sql, $params);
        $data = array_map([$this, 'hideAttributes'], $results);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function hideAttributes($data)
    {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }
        
        foreach ($this->hidden as $attribute) {
            unset($data[$attribute]);
        }
        
        return $data;
    }
}
?>
