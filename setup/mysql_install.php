<?php
error_reporting(E_ALL);

define('APP_PATH', '../app');
define('DB_PATH', '../database');
define('SEED_FILE', 'database_mysql.sql');

require_once(APP_PATH . '/core/env.php');

$default_pass = sha1('qwerty');

$sql = file_get_contents(SEED_FILE);

$search = array('{{db_sessions}}', 
                '{{db_users}}', 
                '{{db_logs}}', 
                '{{db_news}}', 
                '{{db_articles}}', 
                '{{db_albums}}',
                '{{db_pictures}}',
                '{{default_pass}}');

$replace = array($config['db_sessions'],
                 $config['db_users'],
                 $config['db_logs'],
                 $config['db_news'],
                 $config['db_articles'],
                 $config['db_albums'],
                 $config['db_pictures'],
                 $default_pass);

$sql = str_replace($search, $replace, $sql);

try
{
    $pdo_init = "mysql:host={$config['host']};"
                . "port={$config['port']};"
                . "dbname={$config['dbname']};"
                . "charset={$config['charset']}";
                
    $pdo_user = $config['user'];
    $pdo_pass = $config['pass'];
                
    $pdo = new PDO($pdo_init, $pdo_user, $pdo_pass);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($sql);
    
    echo 'Done.';
}
catch(Exception $e)
{
    print_r($e->getMessage());
}
