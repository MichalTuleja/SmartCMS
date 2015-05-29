<?php



if(Site::isUser())
{
    function fetchUserData($id, $data = NULL)
    {
       try
        {
            global $pdo, $config;

            $stmt = $pdo -> prepare("SELECT user_id AS id,
                                            user_login AS login,
                                            user_name AS name,
                                            user_lastvisit AS lastvisit
                                      FROM {$config['db_users']}
                                      WHERE user_id = :id");
            $stmt -> bindValue(':id',   $id,   PDO::PARAM_INT);
            $stmt -> execute();
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt -> closeCursor();
            debug($array);
            if($data == NULL) return $array;
            else return $array[$data];
        }
        catch(Exception $e)
        {
            throw new Exception('Błąd odczytu z bazy danych');
        }
    }

    function resetPassword($id)
    {
       try
        {
            global $pdo, $config;
            
            $pass = Site::generateRandomString(6);
            
            $stmt = $pdo -> prepare("UPDATE {$config['db_users']}
                                        SET user_password = :pass 
                                      WHERE user_id = :id");
            $stmt -> bindValue(':id',   $id,   PDO::PARAM_INT);
            $stmt -> bindValue(':pass',   sha1($pass),   PDO::PARAM_STR);
            $stmt -> execute();
            $stmt -> closeCursor();
            message('info', "Nowe hasło to: $pass");
            logger('Zresetowano hasło dla użytkownika '.fetchUserData($id, 'login'));
        }
        catch(Exception $e)
        {
            throw new Exception('Błąd zapisu do bazy danych');
        }       
    }



    
    if(isset($_GET['reset_password']))
    {
        resetPassword($_GET['reset_password']);
    }   
    
    
    if(isset($_GET['change_password']))
    {
        $pass_tmp['id'] = $session->getUser()->getId();
        $pass_tmp['name'] = $session->getUser()->getName();
        $pass_tmp['login'] = $session->getUser()->getLogin();
        $content->password = $pass_tmp;
        unset($pass_tmp);
    }
    
    if(isset($_POST['change_password']))
    {
        try
        {
            $result = user::checkPassword($_POST['login'], $_POST['pass_old']);
     
            if($result instanceof user)
            {
                if($_POST['pass'] != NULL)
                {
                    if($_POST['pass'] === $_POST['pass_rep'])
                    {
                        try
                        {
                            global $pdo, $config;
                            $stmt = $pdo -> prepare("UPDATE {$config['db_users']}
                                                        SET user_password = :pass 
                                                      WHERE user_id = :id");
                            $stmt -> bindValue(':id',   $_POST['id'],   PDO::PARAM_INT);
                            $stmt -> bindValue(':pass',   sha1($_POST['pass']),   PDO::PARAM_STR);
                            $stmt -> execute();
                            $stmt -> closeCursor();
                            message('info', 'Zmieniono hasło');
                            logger('Zmieniono hasło dla użytkownika '.$session->getUser()->getLogin());
                        }
                        catch(Exception $e)
                        {
                            throw new Exception('Błąd zapisu do bazy danych');
                        }
                    }
                    else
                    {
                        throw new Exception('Pola z nowym hasłem nie zgadzają się');
                    }
                }
                else
                {
                    throw new Exception('Hasło nie może być puste');
                }
            }
            else
            {
                throw new Exception('Nieprawidłowe stare hasło');
            }
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
            logger('Nieudana zmiana hasła dla '.$session->getUser()->getLogin().'. Powód: '.$e->getMessage());
            $pass_tmp['id'] = $session->getUser()->getId();
            $pass_tmp['name'] = $session->getUser()->getName();
            $pass_tmp['login'] = $session->getUser()->getLogin();
            $content->password = $pass_tmp;
            unset($pass_tmp);
        }
    }
    


    if(isset($_GET['change_user_data']))
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("SELECT user_id AS id,
                                          user_login AS login, 
                                          user_name AS name  
                                     FROM {$config['db_users']}
                                     WHERE user_id = :id");
            $stmt->bindValue(':id', $_GET['change_user_data'], PDO::PARAM_INT);
            $stmt->execute();
            $array = $stmt->fetch();
            debug($array);
            $stmt->closeCursor();
            $content->user = $array;
            unset($array);
        }
        catch(Exception $e)
        {
        }
    }

    if(isset($_POST['change_user_data']))
    {
        global $pdo, $config;
        
        try
        {
            try
            {
                if($_POST['login'] == NULL || $_POST['name'] == NULL)
                    throw new Exception();
            }
            catch(Exception $e)
            {
                throw new Exception('Musisz podać login oraz pełną nazwę');
            }
            
            try
            {
                $stmt = $pdo->prepare("UPDATE {$config['db_users']}
                                         SET user_login = :login,
                                             user_name = :name
                                         WHERE user_id = :id");
                $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
                $stmt->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();

                $userdata = fetchUserData($_POST['id']);
                logger("Zmieniono dane użytkownika od id={$userdata['id']} ({$userdata['login']})");

                message('info', 'Zmieniono dane użytkownika');
            }
            catch(Exception $e)
            {
                throw new Exception('Błąd dostępu do bazy danych');
            }
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
            $pass_tmp['id'] = $_POST['id'];
            $pass_tmp['name'] = $_POST['name'];
            $pass_tmp['login'] = $_POST['login'];
            $content->user = $pass_tmp;
            unset($pass_tmp);
        }
    }

    if(isset($_GET['add_user']))
    {
        $content->add_user = 1;
    }

    if(isset($_POST['add_user']))
    {
        global $pdo, $config;

        try
        {
            $pass = Site::generateRandomString(6);
            $stmt = $pdo->prepare("INSERT INTO {$config['db_users']}
                                        VALUES (NULL, :login, :name, :pass, 0)");
            $stmt->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
            $stmt->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
            $stmt->bindValue(':pass', sha1($pass), PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            message('info', "Dodano użytkownika {$_POST['name']} ({$_POST['login']})\nJego hasło to: $pass\nMożesz je zmienić po zalogowaniu, w Admin Panel->Zmień hasło");
            logger("Dodano użytkownika {$_POST['name']} ({$_POST['login']}).");
        }
        catch(Exception $e)
        {
            message('error', 'Błąd dostępu do bazy danych');
        }
    }

    if(isset($_GET['user_remove']))
    {
        global $pdo, $config;
        
        if($_GET['user_remove'] > 1)
        {
            try
            {
                $login = fetchUserData($_GET['user_remove'], 'login');
                $stmt = $pdo->prepare("DELETE FROM {$config['db_users']} WHERE user_id = :id");
                $stmt->bindValue(':id', $_GET['user_remove'], PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                message('info', 'Usunięto użytkownika '.$login);
                logger('Usunięto użytkownika '.$login);
            }
            catch(Exception $e)
            {
                message('error', 'Błąd dostępu do bazy danych');
            }
        }
        else
            message('warn', 'Nie możesz usunąć administratora');
    }

    if(isset($_GET['user_list']))
    {
        global $pdo, $config;
        
        try
            {
            $stmt = $pdo->prepare("SELECT user_id AS id,
                                          user_login AS login, 
                                          user_name AS name, 
                                          user_lastvisit AS lastvisit 
                                     FROM {$config['db_users']}
                                     ORDER BY user_login ASC");
            $stmt->execute();
            $users = $stmt->fetchAll();
            $stmt->closeCursor();
            for($i=0; $i<count($users); $i++)
            {
                if($users[$i]['lastvisit'] == 0)
                    $users[$i]['lastvisit'] = 'Nie logował się';
                else
                    $users[$i]['lastvisit'] = date('Y-m-d H:i', $users[$i]['lastvisit']);
                
            }
            $content->user_list = $users;
            unset($users);
        }
        catch(Exception $e)
        {
            message('error', 'Błąd dostępu do bazy danych');
        }
    }

    if(isset($_GET['view_log']))
    {
        global $pdo, $config;
        
        try
            {
            $stmt = $pdo->prepare("SELECT log_id AS id,
                                          log_time AS time, 
                                          log_message AS message
                                     FROM {$config['db_logs']}
                                     ORDER BY log_time DESC
                                     LIMIT 50");
            $stmt->execute();
            $log = $stmt->fetchAll();
            $stmt->closeCursor();
            debug($log);
            for($i=0; $i<count($log); $i++)
            {
                $log[$i]['time'] = date('Y-m-d H:i:s', $log[$i]['time']);
            }
            $content->log = $log;
            unset($log);
        }
        catch(Exception $e)
        {
            message('error', 'Błąd dostępu do bazy danych');
        }
    }
    
    
}


?>
