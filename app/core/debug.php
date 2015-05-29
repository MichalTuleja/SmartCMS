<div id="debug">
<?php
//debug($messages);
debug($_POST);
//debug($content);

if($debug != NULL)
{
        foreach($debug as $i)
        {
                echo "\n";
                print_r($i);
                echo "\n";
        }
}



?>
</div>
