<?php


namespace Application\Repositories;


use Application\ViewModels\User;
use PDO;

abstract class AbstractRealmRepository extends AbstractRepository
{
    /**
     * @var int
     */
    protected $realmID;

    /**
     * @var User
     */
    protected $user;

    public function setUser(User $user){
        $this->user = $user;
    }

    public function __construct(PDO $database, int $realmID)
    {
        parent::__construct($database);
        $this->realmID = $realmID;
    }

    public function getRealmName(){
        $query = 'SELECT name FROM `realmlist` WHERE id = :id';
        return $this->queryAndFetchCell($query, ['id' => $this->realmID]);
    }

    public function getGMLevelForAccount(?int $accountID = null){
        $query = 'SELECT gmlevel FROM account_access WHERE id = :userID AND RealmID = :realmID';
        if($accountID === null){
            $accountID = $this->user->getUserID();
        }
        return $this->queryAndFetchCell($query, ['userID' => $accountID, 'realmID' => $this->realmID]);
    }
}
