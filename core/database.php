<?php

class Database extends PDO
{

    function __construct()
    {
        try {
            parent::__construct(DB_TYPE . ':dbname=' . DB_NAME . ';host=' . DB_HOST,
                DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            die;
        }
    }

    public function upsert($table_name, $keys, $fields)
    {
        $field_keys = array_keys($fields);
        $keys_keys = array_keys($keys);

        $statements = self::getUpsertStatements($table_name, $keys_keys, $field_keys);

        $params = array();
        foreach ($keys_keys as $key) {
            $params[':' . $key] = $keys[$key];
        }
        foreach ($field_keys as $key) {
            if (is_bool($fields[$key])) { // Boolean fix
                $fields[$key] ? $fields[$key] = 'TRUE' : $fields[$key] = 'FALSE';
            }
            $params[':' . $key] = $fields[$key];
        }

        self::beginTransaction();
        $statements['update']->execute($params);
        $statements['insert']->execute($params);
        self::commit();
    }

    public function getUpsertStatements($table_name, $keys, $fields)
    {
        /* Example:
         * UPDATE table_name SET field_1='a', field_2='b' WHERE key_1=3;
         * INSERT INTO table_name (id, field_1, field_2)
         *  SELECT 3, 'a', 'b'
         *  WHERE NOT EXISTS (SELECT 1 FROM table_name WHERE key_1=3);
         */

        // UPDATE
        $sql_update = 'UPDATE ' . $table_name . ' SET ';
        foreach ($fields as $field) {
            $sql_update .= $field . '=:' . $field . ' ';
            if ($field !== end($fields)) $sql_update .= ',';
        }
        if (!empty($keys)) {
            $sql_update .= 'WHERE ';
            foreach ($keys as $key) {
                $sql_update .= $key . '=:' . $key . ' ';
                if ($key !== end($keys)) $sql_update .= ',';
            }
        }

        // INSERT
        $sql_insert = 'INSERT INTO ' . $table_name . '(';
        foreach ($keys as $key) {
            $sql_insert .= $key . ',';
        }
        foreach ($fields as $field) {
            $sql_insert .= $field . ' ';
            if ($field === end($fields)) $sql_insert .= ')';
            else $sql_insert .= ',';
        }
        $sql_insert .= 'SELECT ';
        foreach ($keys as $key) {
            $sql_insert .= ':' . $key . ', ';
        }
        foreach ($fields as $field) {
            $sql_insert .= ':' . $field . ' ';
            if ($field !== end($fields)) $sql_insert .= ',';
        }
        $sql_insert .= 'WHERE NOT EXISTS (SELECT 1 FROM ' . $table_name . ' WHERE ';
        foreach ($keys as $key) {
            $sql_insert .= $key . '=:' . $key;
            if ($key !== end($keys)) $sql_insert .= ',';
        }
        $sql_insert .= ')';

        return (array(
            'update' => self::prepare($sql_update),
            'insert' => self::prepare($sql_insert)
        ));
    }

}
