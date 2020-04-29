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

$ws_worker->onMessage = function($connection, $data) use (&$connections){
    $data = json_decode($data, true);
    $user = array_search($connection, $connections);
    $connection2 = $connections[$data['user']];
    $connection2->send(json_encode(["type"=>"message", "author"=>$user, "data" => $data['text']]));
    $connection->send(json_encode(["type"=>"message", "author"=>$user, "data" => $data['text']]));

};

$ws_worker->onConnect = function($connection) use (&$connections)
{
    $connection->onWebSocketConnect = function($connection) use (&$connections)
    {
        $connections[$_GET['user']] = $connection;
        $data = ["type" => "users", "data" => array_keys($connections)];
        foreach($connections as $connection){
            $connection->send(json_encode($data));
        }
    };
};

$ws_worker->onClose = function($connection) use(&$connections)
{
    // удаляем параметр при отключении пользователя
    $user = array_search($connection, $connections);
    unset($connections[$user]);
    $data = ["type" => "users", "data" => array_keys($connections)];
    foreach($connections as $connection){
        $connection->send(json_encode($data));
    }
};

// Run worker
Worker::runAll();
