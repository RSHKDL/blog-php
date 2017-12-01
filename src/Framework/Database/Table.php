<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Table
{


    /**
     * @var null|\PDO
     */
    protected $pdo;


    /**
     * Name of the table in DB
     * @var string
     */
    protected $table;


    /**
     * Entity to use
     * @var string|null
     */
    protected $entity = \stdClass::class;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Retrieve a list keys values of our records
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }


    /**
     * Return an instance of Query
     * Can generate aliases with the first letter of the table
     *
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }


    /**
     * Retrieve all records
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
    }


    /**
     * Retrieve a line in relation to a field
     *
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->makeQuery()
            ->where("$field = :field")
            ->params(["field" => $value])
            ->fetchOrFail();
    }


    /**
     * Retrieve an item by its id
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }


    /**
     * Retrieve the number of records
     *
     * @return int
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }


    /**
     * Update a record in the database
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $query = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
    }


    /**
     * Create a record post
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        // This doesn't work with sqlite
        // $fieldQuery = $this->buildFieldQuery($params);
        // $stmt = $this->pdo->prepare("INSERT INTO posts SET $fieldQuery");
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $query->execute($params);
    }


    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $query->execute([$id]);
    }


    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }


    /**
     * Check if a record exist
     *
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }


    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }


    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }


    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }


    /**
     * Allow the execution of a request then retrieve the 1st result
     *
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }


    /**
     * Retrieve the 1st column in a table
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    private function fetchColumn(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
