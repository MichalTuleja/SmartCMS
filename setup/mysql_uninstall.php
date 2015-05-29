<?php
error_reporting(E_ALL);

define('APP_PATH', '../app');
define('DB_PATH', '../database');

require_once(APP_PATH . '/core/env.php');

$default_pass = sha1('qwerty');

$sql = implode(';', array("DROP TABLE `{$config['db_sessions']}`",
                            "DROP TABLE `{$config['db_users']}`",
                            "DROP TABLE `{$config['db_logs']}`",
                            "DROP TABLE `{$config['db_news']}`",
                            "DROP TABLE `{$config['db_articles']}`",
                            "DROP TABLE `{$config['db_pictures']}`",
                            "DROP TABLE `{$config['db_albums']}`"));

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
