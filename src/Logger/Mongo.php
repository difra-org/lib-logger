<?php

declare(strict_types=1);

namespace Difra\Logger;

/**
 * Class Mongo
 * @package Difra\Logger
 */
class Mongo extends Common
{
    protected const MONGO_DEFAULT_CONNECTION = 'mongodb://127.0.0.1:27017';
    protected const MONGO_DEFAULT_SCOPE = 'logs';
    protected const MONGO_DEFAULT_COLLECTION = 'main';

    /** @var ?\MongoDB\Client Mongo connection */
    protected ?\MongoDB\Client $connection = null;
    /** @var ?\MongoDB\Collection */
    protected ?\MongoDB\Collection $collection = null;

    /**
     * @inheritdoc
     */
    protected function realWrite(string $message, int $level): void
    {
        $obj = $this->getLogObj($message);
        $this->getMongo()->insertOne($obj);
    }

    /**
     * @return \MongoDB\Collection
     */
    protected function getMongo(): ?\MongoDB\Collection
    {
        if (!empty($this->collection)) {
            return $this->collection;
        }
        $this->connection = new \MongoDB\Client($this->config['connection'] ?? self::MONGO_DEFAULT_CONNECTION);
        $scope = $this->config['scope'] ?? self::MONGO_DEFAULT_SCOPE;
        $collection = $this->config['collection'] ?? self::MONGO_DEFAULT_COLLECTION;
        return $this->collection = $this->connection->$scope->$collection;
    }
}
