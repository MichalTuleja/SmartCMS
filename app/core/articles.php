<?php
require(APP_PATH . '/core/articles_interfaces.php');
require(APP_PATH . '/core/articles_core.php');


if(Site::isUser())
{
    if(isset($_POST['art']))
    {
        $art = new Article($_POST['id']);
        $art->title = $_POST['title'];
        $art->descr = $_POST['descr'];
        $art->body = $_POST['body'];
        $art->save();
        message('info', 'Zapisano');
        $content->article_form = $art;
        $art_view = clone $art;
        $art_view->prepareView();
        $content->article = $art_view;
        unset($art);
    }


    if(isset($_GET['art_edit']))
    {
        $art = new Article($_GET['art_edit']);
        $content->article_form = $art;
        $art_view = clone $art;
        $art_view->prepareView();
        $content->article = $art_view;
        unset($art);
    }


    if(isset($_POST['art_preview']))
    {
        $art = new Article($_POST['id']);
        $art->title = $_POST['title'];
        if(isset($_POST['descr']))
            $art->descr = $_POST['descr'];
        else
            $art->descr = NULL;

        if(isset($_POST['body']))
            $art->body = $_POST['body'];
        else
            $art->body = NULL;
        
        $content->article_form = $art;
        $art_view = clone $art;
        $art_view->prepareView();
        $content->article = $art_view;
        unset($art);
    }


    if(isset($_GET['art_public']))
    {
        $art = new Article($_GET['art_public']);
        if($art->public == 1) $art->public = 0;
        else $art->public = 1;
        $art->save();
        unset($art);
    }


    if(isset($_GET['art_remove']))
    {
        $art = new Article($_GET['art_remove']);
        $art->removed = 1;
        $art->public = 0;
        $art->save();
        debug($art);
        unset($art);
    }


    if(isset($_GET['art_undo_remove']))
    {
        $art = new Article($_GET['art_undo_remove']);
        $art->removed = 0;
        $art->save();
        unset($art);
    }


    if(isset($_GET['art_drop']))
    {
        $art = new Article($_GET['art_drop']);
        $art->drop();
        unset($art);
    }

    if(isset($_GET['empty_trash']))
    {
        $array = new ArticlesList(0,1);
        $array->load();
        foreach($array->list as $entry) {
            $art = new Article($entry['id']);
            $art->drop();
            unset($art);            
        }
        unset($array);
    }
}


if(isset($_GET['list']))
{
/*    function conv_art($array)
    {
      foreach($array->list as $one) {
        $art = new Article($one['id']);
        debug($art);
        $art->prepareView();
        $art->save();
        unset($art);
        }
    } */

    $array = new ArticlesList();
    $array->load();
    
    if($array->list != NULL) {
        $content->list = $array->list;
        }
    else
        message('warn', "Brak opublikownaych artykułów");
    unset($array);
    
    if(Site::isUser())
    {

        $array = new ArticlesList(0,0);
        $array->load();
        $content->list_npublic = $array->list;
        unset($array);
        
        $array = new ArticlesList(0,1);
        $array->load();
        $content->list_removed = $array->list;
        unset($array);
     }
}


if(isset($_GET['art']))
{
    $art = new Article($_GET['art']);
    $art->prepareView();
    $content->article = $art;
    unset($art);
}

?>
