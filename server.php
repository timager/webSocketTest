<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

$connections = [];

$ws_worker = new Worker("websocket://0.0.0.0:8000");

$ws_worker->onMessage = function($connection, $data) use (&$connections) {
    foreach($connections as $user => $connection) {
        /**
         * @var ConnectionInterface $connection
         */
        $connection->send($data);
    }
    echo $data . PHP_EOL;
};

$ws_worker->onConnect = function($connection) use (&$connections)
{
    $connection->onWebSocketConnect = function($connection) use (&$connections)
    {
        $connections[$_GET['user']] = $connection;
    };
};


Worker::runAll();
