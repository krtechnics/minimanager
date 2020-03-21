<?php

namespace Application\Repositories;

use Application\ViewModels\DatabaseCredentials;
use PDO;
use PDOException;
use PDOStatement;

abstract class AbstractRepository
{

    /**
     * @var PDO
     */
    private $database;

    public static function convertArrayToCredentails(array $credentailsArray): DatabaseCredentials
    {
        $credentails = new DatabaseCredentials();
        $credentails->setHostName($credentailsArray['addr']);
        $credentails->setDatabaseName($credentailsArray['name']);
        $credentails->setUserName($credentailsArray['user']);
        $credentails->setPassword($credentailsArray['pass']);
        return $credentails;
    }

    public static function connectMySQL(DatabaseCredentials $credentials): PDO
    {
        $pdoConnectOptions = [
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            $credentials->getHostName(),
            $credentials->getDatabaseName()
        );
        $database = new PDO(
            $dsn,
            $credentials->getUserName(),
            $credentials->getPassword(),
            $pdoConnectOptions
        );
        $database->exec('SET SESSION sql_mode = \'TRADITIONAL\'');
        return $database;
    }

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $sql
     * @param array $parameters
     *
     * @return $objectName[]|array|null
     */
    protected function queryAndFetchAll(string $sql, ?array $parameters = null)
    {
        try {
            $statement = $this->prepareMapExecute(
                $sql,
                $parameters
            );
            return $statement->fetchAll(
                PDO::FETCH_ASSOC
            );
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * @param $sql
     * @param array $parameters
     *
     * @return PDOStatement
     */
    private function prepareMapExecute(string $sql, ?array $parameters = []): PDOStatement
    {
        $statement = $this->database->prepare($sql);
        if ($parameters !== null) {
            $this->mapParameters(
                $statement,
                $parameters
            );
        }

        $statement->execute();
        return $statement;
    }

    /**
     * @param PDOStatement $statement
     * @param array $parameters
     */
    private function mapParameters(PDOStatement $statement, array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $statement->bindValue(
                ':' . $key,
                $value
            );
        }
    }

    /**
     * @param string $sql
     * @param array $parameters
     *
     * @return array
     */
    protected function queryAndFetchColumn(string $sql, ?array $parameters = null): ?array
    {
        try {
            $statement = $this->prepareMapExecute(
                $sql,
                $parameters
            );
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @param string $objectName
     *
     * @return $objectName|null|bool
     */
    protected function queryAndFetch(string $sql, ?array $parameters = null)
    {
        try {
            $statement = $this->prepareMapExecute(
                $sql,
                $parameters
            );
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * @param string $sql
     * @param array $parameters
     *
     * @return mixed
     */
    protected function queryAndFetchCell(string $sql, ?array $parameters = null)
    {
        try {
            $statement = $this->prepareMapExecute(
                $sql,
                $parameters
            );
            return $statement->fetchColumn();
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * @param string $sql
     * @param array $parameters
     *
     * @return bool
     */
    protected function query(string $sql, ?array $parameters = null)
    {
        try {
            $this->prepareMapExecute(
                $sql,
                $parameters
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
