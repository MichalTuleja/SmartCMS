<?php
define('CONFIG_DIR', '../config');

// Envoirment variables
require_once(CONFIG_DIR . '/php_conf.php');
$config = parse_ini_file(CONFIG_DIR . '/global.php', 0);


// Removes slashes
if(version_compare(phpversion(), '5.3', '<'))
{
    function removeSlashes(&$value)
    {
        if(is_array($value)) return array_map('removeSlashes', $value);
        else return stripslashes($value);
    }
    
    set_magic_quotes_runtime(0);
    
    if(get_magic_quotes_gpc())
    {
        $_POST = array_map('removeSlashes', $_POST);
        $_GET = array_map('removeSlashes', $_GET);
        $_COOKIE = array_map('removeSlashes', $_COOKIE);
    }
}

?>
