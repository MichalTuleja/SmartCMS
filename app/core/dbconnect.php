<?php
try
{
    // MySQL support
    $pdo = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}",
                    $config['user'],$config['pass']);

    // SQlite support
    //$pdo = new PDO("sqlite:../database/{$config['dbfile']}");
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e)
{
    print_r($e);
    require(APP_PATH . '/core/fatal_error.php'); die;
}

?>
