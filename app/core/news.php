<?php

require(APP_PATH . '/core/news_interfaces.php');
require(APP_PATH . '/core/news_classes.php');
require(APP_PATH . '/core/calendar_interfaces.php');
require(APP_PATH . '/core/calendar_classes.php');
require(APP_PATH . '/core/pictures_interfaces.php');
require(APP_PATH . '/core/pictures_images.php');



if(Site::isUser())
{
    if(isset($_POST['news']))
    {
        try
        {
            $news = new News($_POST['id']);
            if($_POST['meet_date'] != NULL)
            { 
                $array = explode('-', $_POST['meet_date']);
                $news->meettime = mktime($_POST['meet_hour'], $_POST['meet_minute'], 00, $array[1], $array[2], $array[0]); //$_POST['meet_date'].' '.$_POST['meet_hour'].':'.$_POST['meet_minute'].':00';
                $news->meet_date = $_POST['meet_date'];
                $news->meet_hour = $_POST['meet_hour'];
                $news->meet_minute = $_POST['meet_minute'];
            }
            $news->title = $_POST['title'];
            $news->body = $_POST['body'];

            if($_POST['title'] == NULL)
            {
                $content->news_form = $news;
                throw new Exception("Brak tytułu");
            }
            if($_POST['body'] == NULL)
            {
                $content->news_form = $news;
                throw new Exception("Brak treści");
            }
            
            $news->save();
            unset($news);
            
            if($_POST['id'] == NULL) message('info', 'Dodano newsa');
            else message('info', 'Zmieniono treść newsa');
            
            $news = new News();
            $content->news_form = $news;
            unset($news);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    elseif(isset($_GET['edit']))
    {
        try
        {
            $news = new News($_GET['edit']);
            $content->news_form = $news;
            unset($news);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    else
    {
        $news = new News();
        $content->news_form = $news;
        unset($news);
    }
    
    if(isset($_GET['remove']))
    {
        try
        {
            $news = new News($_GET['remove']);
            if($news->removed == 0) 
            {
                $news->removed = 1;
                message('info', 'Przeniesiono do kosza');
            }
            else 
            {
                $news->removed = 0;
                message('info', 'Przywrócono z kosza');
            }
            $news->save();
            unset($news);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_GET['drop']))
    {
        try
        {
            $news = new News($_GET['drop']);
            $news->drop();
            unset($news);
            message('info', 'News został usunięty');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

    if(isset($_GET['empty_trash']))
    {
        try
        {
            $list = new NewsList(1, 0);  // $removed = NULL, $limit = 0
            foreach($list->list as $entry)
            {
                $news = new News($entry['id']);
                $news->drop();
                unset($news);
            }
            unset($list);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

}

if(Site::isUser())
{
    if(isset($_GET['recycle']) && $_GET['recycle'] == 1)
    {
        $list = new NewsList(1, 0);  // $removed = NULL, $limit = 0
        $content->news = $list;
        unset($list);
    }
    else
    {
        $list = new NewsList(0, 0);
        $content->news = $list;
        unset($list);
    }
}
else
{
    if(isset($_GET['all']) && $_GET['all'] == 1)
    {
        $list = new NewsList(0, 0);
    }
    else
    {
        $list = new NewsList(0, 10);
    }
    
    $content->news = $list;
    unset($list);
}


$pics = new RandomPictures();
$content->pic = $pics;

$cals[] = new Calendar();
$cals[] = new Calendar(date('Y-m', time() + cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'))*86400));
$content->cals = $cals;


?>
