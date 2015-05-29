<?php
    try
    {
        ob_start();
        require(APP_PATH . '/core/auth/request.php');
        require(APP_PATH . '/core/auth/user.php');
        require(APP_PATH . '/core/auth/session.php');
    
        $request = new httpRequest;
        $session = new session;

        if($session->getUser()->isAnonymous())
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && isset($_POST['haslo']))
            {
                $result = user::checkPassword($_POST['login'], $_POST['haslo']);
 
                if($result instanceof user)
                {
                    $session -> update($result);
                }
                else
                {
                    logger("Nieudana próba zalogowania użytkownika {$_POST['login']}");
                    message('error', 'Nieprawidłowy login lub hasło');
                }       
            }
        }
        else
        {
            if (isset($_GET['action']) && $_GET['action'] == "logoff")
            {
                $session -> update(new user(true));
            }
        }

        ob_end_flush();
    }
    catch(PDOException $exception)
    {
        echo 'Database error: '.$exception->getMessage();
    }
?>
