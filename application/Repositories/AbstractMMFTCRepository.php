<?php


namespace Application\Repositories;


use PDO;

abstract class AbstractMMFTCRepository extends AbstractRepository
{
    protected $realmID;

    public function __construct(PDO $database, int $realmID)
    {
        parent::__construct($database);
        $this->realmID = $realmID;
    }
}
