<?php

namespace Application\Modules;

use Application\ViewModels\ServerConfiguration;
use Application\ViewModels\User;
use MongoDB\Driver\Server;
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

    /**
     * @var ServerConfiguration
     */
    protected $serverConfiguration;
    /**
     * @var User
     */
    protected $user;

    public function __construct(int $realmID,User $user, ?PDO $mmftcConnection, ?PDO $realmConnection, ?PDO $characterConnection, ?PDO $wordConnection)
    {
        $this->realmID = $realmID;
        $this->user = $user;

        $this->mmftcConnection = $mmftcConnection;
        $this->realmConnection = $realmConnection;
        $this->characterConnection = $characterConnection;
        $this->worldConnection = $wordConnection;
    }

    public function setServerConfiguration(ServerConfiguration $serverConfiguration){
        $this->serverConfiguration = $serverConfiguration;
    }
}
