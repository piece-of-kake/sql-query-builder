<?php

namespace PoK\SQLQueryBuilder;

use PoK\SQLQueryBuilder\Exceptions\Client\CannotConnectException;
use PoK\SQLQueryBuilder\Exceptions\Client\DataTooLongException;
use PoK\SQLQueryBuilder\Exceptions\Client\MissingColumnException;
use PoK\SQLQueryBuilder\Exceptions\Client\MissingTableException;
use PoK\SQLQueryBuilder\Interfaces\CanPaginate;
use PoK\SQLQueryBuilder\Profiler\RecordQueryInterface;
use PDO;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\IsCollectable;
use PoK\SQLQueryBuilder\Interfaces\IsDataType;
use PoK\SQLQueryBuilder\Queries\TableExists;
use PoK\SQLQueryBuilder\Interfaces\LastInsertId;
use PoK\SQLQueryBuilder\Exceptions\Client\DuplicateEntryException;
use PoK\SQLQueryBuilder\Exceptions\Client\UnhandledException;
use PoK\ValueObject\Collection;
use PoK\ValueObject\PaginatedCollection;

class MySQLClient implements SQLClientInterface
{
    private $host;
    private $databaseName;
    private $username;
    private $password = '';
    private $charset = 'utf8mb4';
    private $connection;
    private $profiler;

    public function __construct(
        string $host,
        string $databaseName = null,
        string $username,
        string $password = ''
    )
    {
        $this->host = $host;
        $this->databaseName = $databaseName;
        $this->username = $username;
        $this->password = $password;
    }

    public function setCharset(string $charset)
    {
        $this->charset = $charset;
    }

    public function setProfiler($profiler)
    {
        $this->profiler = $profiler;
    }

    public function execute(CanCompile $query)
    {
        try {
            $dsn = "mysql:host=$this->host;charset=$this->charset;";
            if ($this->databaseName) $dsn .= "dbname=$this->databaseName;";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            throw new CannotConnectException();
        }

        if ($this->profiler instanceof RecordQueryInterface) $this->profiler->recordQuery($query->compile());

        if ($query instanceof IsCollectable && $query instanceof IsDataType) {
            $statement = $this->connection->prepare($query->compile());
            try {
                $statement->execute();
            } catch (\PDOException $exception) {
                $this->handleError($exception);
            }

            $data = $statement->fetchAll($query->getDataType());
            if ($query instanceof CanPaginate && $query->hasPagination()) {
                $countQuery = $query->cloneForTotalCount();
                $totalCount = $this->connection->query($countQuery->compile())->fetchColumn();
                return new PaginatedCollection($data, $query->getPagination(), $totalCount);
            } else {
                return new Collection($data);
            }
        } elseif ($query instanceof TableExists) {
            try {
                return (bool) $this->connection->query($query->compile());
            } catch (\PDOException $exception) {
                try {
                    $this->handleError($exception);
                } catch (MissingTableException $e) {
                    return false;
                }
            }
        } elseif ($query instanceof LastInsertId) {
            $statement = $this->connection->prepare($query->compile());
            try {
                $statement->execute();
            } catch (\PDOException $exception) {
                $this->handleError($exception);
            }
            return $this->connection->lastInsertId();
        } else {
            $statement = $this->connection->prepare($query->compile());
            try {
                $statement->execute();
            } catch (\PDOException $exception) {
                $this->handleError($exception);
            }
            return $statement->rowCount();
        }
    }

    private function handleError(\PDOException $exception)
    {
        switch ($exception->getCode()) {
            case '42S02':
                throw new MissingTableException();
            case '42S22':
                throw new MissingColumnException($exception->getMessage());
            case '22001':
                throw new DataTooLongException($exception->getMessage());
            default:
                throw new UnhandledException();
        }
    }
}
