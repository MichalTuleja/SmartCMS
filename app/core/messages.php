<?php
// Error messages
$errmsg = array(
'DBread'                    => "Wystąpił błąd podczas odczytu bazy danych",
'DBwrite'               => "Wystąpił błąd podczas zapisu do bazy danych",
'picture_error'     => "Wystąpił błąd podczas konwersji obrazu. Upewnij się że podany plik zawiera obraz w formacie JPG lub PNG.",
'no_pictures'       => "Brak zdjęć w wybranej kategorii",
'no_pictures_cat' => "Nie opublikowano jeszcze żadnych zdjęć. Aby to zrobić utwórz album, dodaj zdjęcia i użyj klawisza 'Publikuj'"
);

function errmsg($i)
{
    global $errmsg;

    if(array_key_exists($i, $errmsg)) return $errmsg[$i];
    else return "Wystąpił błąd typu '$i'";
}

$messages = NULL;
$debug = NULL;

function message($type, $body)
{
    global $messages;

    $messages[] = array($type, $body);
}


function logger($message)
{
    global $pdo, $config;

    try
    {
        $stmt = $pdo->prepare("INSERT INTO {$config['db_logs']}
                                    VALUES (NULL, UNIX_TIMESTAMP(NOW()), :message)");
        $stmt->bindValue(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    catch(Exception $e)
    {
        require('fatal_error.php'); die;
    }
}


function debug($body)
{
    global $debug;

    $n = count($debug);
    $debug[$n+1] = $body;
}
?>
