<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;

$connections = [];

$ws_worker = new Worker("websocket://0.0.0.0:8000");
$ws_worker->onWorkerStart = function() use (&$connections)
{
//    // создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода нашего сайта
//    $inner_tcp_worker = new Worker("tcp://127.0.0.1:1234");
//    // создаём обработчик сообщений, который будет срабатывать,
//    // когда на локальный tcp-сокет приходит сообщение
//    $inner_tcp_worker->onMessage = function($connection, $data) use (&$users) {
//        $data = json_decode($data);
//        // отправляем сообщение пользователю по userId
//        if (isset($users[$data->user])) {
//            $webconnection = $users[$data->user];
//            $webconnection->send($data->message);
//        }
//    };
//    $inner_tcp_worker->listen();
};

$ws_worker->onMessage = function($connection, $data){
    echo $data;
};

$ws_worker->onConnect = function($connection) use (&$connections)
{
    $connection->onWebSocketConnect = function($connection) use (&$connections)
    {
        $connections[$_GET['user']] = $connection;
    };
};
//
//$ws_worker->onClose = function($connection) use(&$connections)
//{
//    // удаляем параметр при отключении пользователя
//    $user = array_search($connection, $connections);
//    unset($connections[$user]);
//};

// Run worker
Worker::runAll();
