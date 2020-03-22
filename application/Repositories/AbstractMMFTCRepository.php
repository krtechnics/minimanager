<?php


namespace Application\Repositories;


use PDO;

abstract class AbstractMMFTCRepository extends AbstractRepository
{
    protected $realmID;

    private $mapCache = [];
    private $zoneCache = [];


    public function __construct(PDO $database, int $realmID)
    {
        parent::__construct($database);
        $this->realmID = $realmID;
    }

    public function getMapName($id){
        if(isset($this->mapCache[$id])){
            return $this->mapCache[$id];
        }
        $query = 'SELECT name01 FROM dbc_map WHERE id = :id';
        $name = $this->queryAndFetchCell($query, ['id' => $id]);
        $this->mapCache[$id] = $name;
        return $name;
    }

    public function getZoneName($id){
        if(isset($this->zoneCache[$id])){
            return $this->zoneCache[$id];
        }
        $query = 'SELECT name01 FROM dbc_areatable WHERE id = :id';
        $name = $this->queryAndFetchCell($query, ['id' => $id]);
        $this->zoneCache[$id] = $name;
        return $name;
    }
}
