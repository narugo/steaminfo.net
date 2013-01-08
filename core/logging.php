<?php

require_once PATH_TO_CORE . 'database.php';

define('DEFAULT_LOG_FILE', '/home/roman/web/steaminfo.net/default.log');

function write_log_to_db($message)
{
    try {
        $db = new Database();
    } catch (PDOException $e) {
        write_log_to_file('Database access error!');
        return false;
    }

    $remote_address = $_SERVER['REMOTE_ADDR'];
    $request_uri = $_SERVER['REQUEST_URI'];

    $statement = $db->prepare('INSERT INTO error_logs (remote_address, request_uri, message)
                               VALUES(:address, :uri, :message)');
    $statement->execute(array(
        ':address' => $remote_address,
        ':uri' => $request_uri,
        ':message' => $message
    ));
}

function write_log_to_file($message)
{
    $time = $_SERVER['REQUEST_TIME'];
    $date = date("Y-m-d H:i:s", $time);
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $request_uri = $_SERVER['REQUEST_URI'];

    if ($fd = @fopen(DEFAULT_LOG_FILE, "a")) {
        $result = fputcsv($fd, array($date, $remote_addr, $request_uri, $message));
        fclose($fd);
        if ($result > 0)
            return array(status => true);
        else
            return array(status => false, message => 'Unable to write to ' . DEFAULT_LOG_FILE . '!');
    } else {
        return array(status => false, message => 'Unable to open log ' . DEFAULT_LOG_FILE . '!');
    }
}