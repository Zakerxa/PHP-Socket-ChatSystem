<?php

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;


// composer autoload
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "vendor", "autoload.php"));

$io = new SocketIO(2020);
$names = [];
$ids = [];
$id; //declaring the arrays to store names and socket id's and anvariable id.

$io->on('connection', function ($socket) {

    $socket->addedUser = false;

    $socket->on('api', function ($cornjob) use ($socket) {
        $socket->broadcast->emit('live api', array(
            'data' => $cornjob
        ));
    });

    /// when the client connect 'user joined', this listens and executes
    $socket->on('connect user', function ($token, $username, $pic)  use ($socket) {
        global $names, $ids, $id, $io,$numUsers;
        if ($socket->addedUser)
            return;
        // find client name in names array
        if (in_array($username, $names)) {
        } else {
            // we store the username in the socket session for this client
            $socket->username = $username;
            // add the client's username to the global list
            array_push($names, $username);
            array_push($ids, $socket->id);
            var_dump($username . '  is online');

            // $io->sockets->emit('client', $names, $ids);

            ++$numUsers;
            $socket->broadcast->emit('user joined', array(
                'username' => $socket->username,
                'num' => $numUsers
            ));
            $socket->addedUser = true;
        }
    });

    // when the client emits 'new message', this listens and executes
    $socket->on('new message', function ($data) use ($socket) {
        // we tell the client to execute 'new message'
        $socket->broadcast->emit('new message', array(
            'username' => $socket->username,
            'message' => $data
        ));
    });

    // when the client emits 'typing', we broadcast it to others
    $socket->on('typing', function () use ($socket) {
        $socket->broadcast->emit('typing', array(
            'username' => $socket->username
        ));
    });

    // when the client emits 'stop typing', we broadcast it to others
    $socket->on('stop typing', function () use ($socket) {
        $socket->broadcast->emit('stop typing', array(
            'username' => $socket->username
        ));
    });

    // when the user disconnects.. perform this
    $socket->on('disconnect', function () use ($socket) {
        global $names, $ids, $io,$numUsers;
        $disClient = $socket->username;
        if ($socket->addedUser) {
            --$numUsers;
            if (in_array($disClient, $names)) {    
                // remove the username from global usernames list
                if (!$socket->username) return;
                $id = array_search($socket->id, $ids);
                unset($names[$id]);
                unset($ids[$id]);
                // echo globally that this client has left
                $socket->broadcast->emit('user left', array(
                    'username' => $disClient,
                    'num' => $numUsers
                ));
            }
        }
    });
});

if (!defined('GLOBAL_START')) {

    Worker::runAll();
}
