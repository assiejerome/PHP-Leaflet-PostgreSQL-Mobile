<?php
    ob_start();
    session_start();
    //  *************** For PostgreSQL
        $dsn = "pgsql:host=localhost;dbname=testdb;port=5432";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        $pdo = new PDO($dsn, 'postgres', 'postgres', $opt);
    //  *************** For MySQL
    //    $dsn = "mysql:host=localhost;dbname=testdb;port=3306;charset=utf8";
    //    $opt = [
    //        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    //        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //        PDO::ATTR_EMULATE_PREPARES   => false
    //    ];
    //    $pdo = new PDO($dsn, $user, $pass, $opt);
    
    $root_directory = "webmap401";
    $from_email = "ajer_coolio@yahoo.fr";
    $reply_email = "ajer_coolio@yahoo.fr";
    include "php_functions.php";
?>
