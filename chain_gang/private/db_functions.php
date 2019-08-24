<?php

function db_connect() {
    $connection = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
    confirm_db_connect($connection);
    return $connection;
}

function confirm_db_connect($connection) {
    if($connection->connect_errno) {
        $message = "DB Connections failed: ";
        $message .= $connection->connect_error;
        $message .= " (" . $connection->connect_errno . ")";
        exit($message);
    }
}

function db_disconnect($connection) {
    if(isset($connection)) {
        $connection->close();
    }
}

?>