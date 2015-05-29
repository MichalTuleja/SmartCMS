<?php
define('APP_PATH', '../app/');
define('DB_PATH', '../database');
define('LIB_PATH', '../libraries');
define('TPL_PATH', '../app/templates');
define('TMP_PATH', '../tmp');

require_once(APP_PATH . '/core/env.php');
require_once(APP_PATH . '/core/messages.php');
require_once(APP_PATH . '/core/dbconnect.php');
require_once(APP_PATH . '/core/auth/secure.php');
require_once(APP_PATH . '/core/common.php');
require_once(LIB_PATH . '/PHPTAL/PHPTAL.php');

$page = new Page();
$content = new PHPTAL(TPL_PATH . '/main.xhtml');

require_once(APP_PATH . "core/{$page->getEngineFile()}");

$content->site = $page;
$content->title = $config['title'];
$content->messages = $messages;
$content->img_dir = $config['img_dir'];

try {
    echo $content->execute();
    }
catch(Exception $e) {
    echo "<div style=\"background-color: #F75D59;
                       white-space: pre-wrap;
                       border:2px solid #C11B17;
                       margin:3px;font-family:Courier\">$e</div>";
                    }
if($config['debug'])
{
    debug($config);
    include(APP_PATH . '/core/debug.php');
}
?>
