<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Table
{


    /**
     * @var \PDO
     */
    private $pdo;


    /**
     * Name of table in DB
     * @var string
     */
    protected $table;


    /**
     * Entity to use
     * @var string|null
     */
    protected $entity;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Paginate items
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }


    protected function paginationQuery()
    {
        return "SELECT * FROM {$this->table}";
    }


    /**
     * Retrieve a list keys values of our records
     *
     *
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
     * Retrieve an item by its id
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetch() ?: null;
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
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $stmt->execute($params);
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
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $stmt->execute($params);
    }


    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }


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
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() !== false;
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
}
