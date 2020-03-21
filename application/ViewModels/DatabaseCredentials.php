<?php

namespace Application\ViewModels;
/**
 * Class DatabaseCredentails
 * @package Application\Repositories
 */
final class DatabaseCredentials
{
    private $hostName;
    private $databaseName;
    private $userName;
    private $password;

    /**
     * @return mixed
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * @param mixed $hostName
     */
    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }

    /**
     * @return mixed
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @param mixed $databaseName
     */
    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


}
