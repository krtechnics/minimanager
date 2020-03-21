<?php

namespace Application\Modules;

use Application\Repositories\AbstractRepository;
use Application\ViewModels\DatabaseCredentials;
use PDO;

abstract class AbstractModule
{
    /**
     * @var int
     */
    protected $realmID;

    /**
     * @var PDO
     */
    protected $mmftcConnection;

    /**
     * @var PDO
     */
    protected $realmConnection;

    /**
     * @var PDO
     */
    protected $characterConnection;

    /**
     * @var PDO
     */
    protected $worldConnection;

    public function __construct(int $realmID, ?DatabaseCredentials $mmftcCredentails, ?DatabaseCredentials $realmCredentails, ?DatabaseCredentials $characterCredentials, ?DatabaseCredentials $wordCredentails)
    {
        $this->realmID = $realmID;
        if ($mmftcCredentails !== null) {
            $this->mmftcConnection = AbstractRepository::connectMySQL($mmftcCredentails);
        }

        if ($realmCredentails !== null) {
            $this->realmConnection = AbstractRepository::connectMySQL($realmCredentails);
        }

        if ($characterCredentials !== null) {
            $this->characterConnection = AbstractRepository::connectMySQL($characterCredentials);
        }

        if ($wordCredentails !== null) {
            $this->worldConnection = AbstractRepository::connectMySQL($wordCredentails);
        }
    }


}
