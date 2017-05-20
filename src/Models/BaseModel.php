<?php 

namespace App\Models;

abstract class BaseModel
{
    protected $table;
    protected $column;
    protected $db;
    protected $query;
    protected $check; //column you want to check

    public function __construct($db = null)
    {
        $this->db = $db;
        $this->query = null;
    }

    protected function setDb()
    {
        global $container;

        $this->db = $container['db'];
    }

    protected function getBuilder()
    {
        if ($this->db == null) {
            $this->setDb();
        }
        return $this->db->createQueryBuilder();
    }

    public function getAll()
    {
        $qb = $this->getBuilder();
        $this->query = $qb->select($this->column)
                          ->from($this->table);
        return $this;
    }

    /**
     * Paginate Query
     * @param  int $page  page
     * @param  int $limit data per page
     * @return array data paginate
     */
    public function paginate(int $page, int $limit)
    {
        //count total custom query
        $total = count($this->fetchAll());

        //count total pages
        $pages = (int) ceil($total / $limit);

        
        // $number = (int) $page;
        $range = $limit * ($page - 1);

        if (!$this->query) {
            $qb = $this->getBuilder();
            $this->query = $qb->select($this->column)
                              ->from($this->table);
        } else {
            $this->query->setFirstResult($range)->setMaxResults($limit);
        }
        
        $data = $this->fetchAll();

        $result = [
            'total_data'=> $total,
            'perpage'   => $limit,
            'current'   => $page,
            'total_page'=> $pages,
            'data'      => $data,
        ];

        if (empty($result['data'])) {
            return false;
        }

        return $result;
    }

    /**
     * conditional find
     * @param  string/array $column db column name
     * @param  string $operator find operator
     * @param  string $value  value
     * @return this model
     */
    public function find($column, $value = null, $operator = '=')
    {
        $param = ':'.$column;
        $qb = $this->getBuilder();
        $this->query = $qb->select($this->column)
                     ->from($this->table);
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $column = current($value);
                    $param = ':'.$column;
                    $valueParam = end($value);
                    $qb->andWhere($column.$value[1].$param)
                       ->setParameter($param, $valueParam);
                }
            }
        } else {
            $qb->where($column.$operator.$param)
               ->setParameter($param, $value);
        }
        return $this;
    }

    public function fetchAll()
	{
		return $this->query->execute()->fetchAll();
	}

	public function fetch()
	{
		return $this->query->execute()->fetch();
	}

	//find data with delete = 0
	public function withoutDelete()
	{
		$this->query = $this->query->andWhere('deleted = 0');

		return $this;
	}

    //find data with delete = 1
    public function withDelete()
    {
        $this->query = $this->query->andWhere('deleted = 1');

        return $this;
    }

	/**
	 * Create New Data
	 * @param  array  $data column and value
	 * @return int id rows
	 */
	public function create(array $data)
    {
        $column = [];
        $paramData = [];
        foreach ($data as $key => $value) {
            $column[$key] = ':'.$key;
            $paramData[$key] = $value;
        }
        $qb = $this->getBuilder();
        $qb->insert($this->table)
           ->values($column)
           ->setParameters($paramData)
           ->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * conditional update
     * @param  array  $data   column and value you want to edit
     * @param  string $column column where you want to edit
     * @param  string $value  value of column you want to edit
     * @return [type]         [description]
     */
    public function update(array $data, $column, $value)
    {
        $columns = [];
        $paramData = [];

        $qb = $this->getBuilder();
        $qb->update($this->table);
        foreach ($data as $key => $values) {
            $columns[$key] = ':'.$key;
            $paramData[$key] = $values;

            $qb->set($key, $columns[$key]);

        }
        $paramData[$column] = $value;
        $qb->where( $column.'='. ':'.$column)
           ->setParameters($paramData)
           ->execute();
    }

    //set deleted to 1
    public function softDelete($column, $value)
    {
        $param = ':'.$column;

        $qb = $this->getBuilder();
        $qb->update($this->table)
           ->set('deleted', 1)
           ->where($column.'='. $param)
           ->setParameter($param, $value)
           ->execute();
    }

    //delete from db
    public function hardDelete($column, $value)
    {
        $param = ':'.$column;

        $qb = $this->getBuilder();
        $qb->delete($this->table)
           ->where($column.'='. $param)
           ->setParameter($param, $value)
           ->execute();
    }

    //set deleted to 0
    public function restore($column, $value)
    {
        $param = ':'.$column;

        $qb = $this->getBuilder();
        $qb->update($this->table)
           ->set('deleted', 0)
           ->where($column.'='. $param)
           ->setParameter($param, $value)
           ->execute();
    }

    /**
     * Check data and create if doesn't exist
     * @param  array $data data you want to create
     * @return string name column if data exist
     * @return $this->create() if data doesn't exist
     */
    public function checkOrCreate(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($this->check) && in_array($key, $this->check)) {
                $check = $this->find($key, $value)->fetch();
                if ($check)
                {
                    return ucfirst($key);
                }
            }
        }
        return $this->create($data);
    }

    /**
     * Check data, if exist, update. else, create
     * @param  array  $data   data you want to create
     * @param  string $column column you want to check
     * @param  string $value  value of column
     * @return [type]       [description]
     */
    public function updateOrCreate(array $data)
    {
        $columns = [];
        $paramData = [];

        $qb = $this->getBuilder();
        $qb->select($this->column)->from($this->table);
        foreach ($data as $key => $value) {
            $columns[$key] = ':'.$key;
            $paramData[$key] = $value;

            if (is_array($this->check) && in_array($key, $this->check)) {
                $qb->andWhere($key.'='.$columns[$key])
                   ->setParameter($columns[$key], $value);
            }
        }
        $check = $qb->execute()->fetch();

        if ($check) {
            $this->update($data, 'id', $check['id']);
            return $this->find('id', $check['id'])->fetch();
        } else {
            return $this->create($data);
        }

    }

    public function checkOrUpdate(array $data, $column, $value)
    {
        foreach ($data as $key => $values) {
            if (is_array($this->check) && in_array($key, $this->check)) {
                $check = $this->find($key, $values)->fetch();
                if ($check)
                {
                    return ucfirst($key);
                }
            }
        }
        $this->update($data, $column, $value);
        return $this->find($column, $value)->fetch();
    }
}