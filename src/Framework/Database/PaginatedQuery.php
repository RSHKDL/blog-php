<?php
namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{


    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $countQuery;

    /**
     * @var string|null
     */
    private $entity;


    /**
     * @var array
     */
    private $params;


    /**
     * PaginatedQuery constructor.
     * @param \PDO $pdo
     * @param string $query Query to get all results
     * @param string $countQuery Query to count the number of all results
     * @param null|string $entity
     * @param array $params
     */
    public function __construct(
        \PDO $pdo,
        string $query,
        string $countQuery,
        ?string $entity,
        array $params = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }


    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->countQuery);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }


    /**
     * Returns a slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length): array
    {
        $stmt = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        foreach ($this->params as $key => $param) {
            $stmt->bindParam($key, $param);
        }
        $stmt->bindParam('offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam('length', $length, \PDO::PARAM_INT);
        if ($this->entity) {
            $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
