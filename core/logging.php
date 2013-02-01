<?php

define('DEFAULT_LOG_FILE', '/home/roman/web/steaminfo.net/main.log');

function writeUserViewLog($user_id)
{
    try {
        $db = new Database();
    } catch (PDOException $e) {
        writeLogToFile('Database access error! ' . $e);
        return false;
    }

    $remote_address = $_SERVER['REMOTE_ADDR'];

    $statement = $db->prepare('INSERT INTO user_profile_view_log (remote_address, user_id)
                               VALUES(:address, :user_id)');
    $statement->execute(array(
        ':address' => $remote_address,
        ':user_id' => $user_id
    ));
}

function writeGroupViewLog($group_id)
{
    try {
        $db = new Database();
    } catch (PDOException $e) {
        writeLogToFile('Database access error! ' . $e);
        return false;
    }

    $remote_address = $_SERVER['REMOTE_ADDR'];

    $statement = $db->prepare('INSERT INTO group_view_log (remote_address, group_id)
                               VALUES(:address, :group_id)');
    $statement->execute(array(
        ':address' => $remote_address,
        ':group_id' => $group_id
    ));
}

function writeMatchViewLog($match_id)
{
    try {
        $db = new Database();
    } catch (PDOException $e) {
        writeLogToFile('Database access error! ' . $e);
        return false;
    }

    $remote_address = $_SERVER['REMOTE_ADDR'];

    $statement = $db->prepare('INSERT INTO dota_match_view_log (remote_address, match_id)
                               VALUES(:address, :match_id)');
    $statement->execute(array(
        ':address' => $remote_address,
        ':match_id' => $match_id
    ));
}

function writeErrorLog($message)
{
    try {
        $db = new Database();
    } catch (PDOException $e) {
        writeLogToFile('Database access error! ' . $e);
        return false;
    }

    $remote_address = $_SERVER['REMOTE_ADDR'];
    $request_uri = $_SERVER['REQUEST_URI'];

    $statement = $db->prepare('INSERT INTO error_log (remote_address, request_uri, message)
                               VALUES(:address, :uri, :message)');
    $statement->execute(array(
        ':address' => $remote_address,
        ':uri' => $request_uri,
        ':message' => $message
    ));
}


function writeLogToFile($message)
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