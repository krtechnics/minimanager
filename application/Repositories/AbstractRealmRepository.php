<?php


namespace Application\Repositories;


use PDO;

abstract class AbstractRealmRepository extends AbstractRepository
{
    /**
     * @var int
     */
    protected $realmID;

    public function __construct(PDO $database, int $realmID)
    {
        parent::__construct($database);
        $this->realmID = $realmID;
    }

    public function getRealmName(){
        $query = 'SELECT name FROM `realmlist` WHERE id = :id';
        return $this->queryAndFetchCell($query, ['id' => $this->realmID]);
    }
}
