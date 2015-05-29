<?php

require(APP_PATH . '/core/pictures_interfaces.php');
require(APP_PATH . '/core/pictures_images.php');
require(APP_PATH . '/core/pictures_categories.php');
require(APP_PATH . '/core/pictures_lists.php');


if(Site::isUser())
{
    $generate_cat_form = 1;

    if(isset($_POST['cat']))
    {
        try
        {
            debug($_POST);
            $cat = new Category();
            $cat->id = $_POST['id'];
            if($_POST['id'] > 0) $cat->load();

            $cat->title = $_POST['title'];
            $cat->descr = $_POST['descr'];
            $cat->mtime = $_POST['mtime'];
            if($_POST['mtime'] == NULL)
                $cat->mtime = date('Y-m-d');
            else
                $cat->mtime = $_POST['mtime'];

            if($_POST['title'] == NULL)
            {
                $content->cat_form = $cat;
                $generate_cat_form = 0;
                throw new Exception('Podaj nazwę albumu');
            }
            $cat->save();
            message('info', 'Dodano/zmieniono album');
            debug($cat);
            unset($cat);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }


    if(isset($_GET['cat_edit']))
    {
        try
        {
            $cat = new Category();
            $cat->id = $_GET['cat_edit'];
            $cat->load();
            debug($cat);
            $content->cat_form = $cat;
            unset($cat);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

    if(isset($_GET['cat_public']))
    {
        try
        {
            $cat = new Category();
            $cat->id = $_GET['cat_public'];
            $cat->load();
            if($cat->public == 0)   $cat->public = 1;
            else $cat->public = 0;
            $cat->save();
            unset($cat);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

    if(isset($_GET['cat_remove']))
    {
        try
        {
            $cat = new Category();
            $cat->id = $_GET['cat_remove'];
            $cat->remove();
            unset($cat);
            message('info', 'Usunięto album');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

    if(isset($_GET['set_thumb']))
    {
        try
        {
            $cat = new Category($_GET['cat']);
            $cat->thumb = $_GET['set_thumb'];
            $cat->save();
            debug($cat);
            unset($cat);
            message('info', 'Zmieniono miniaturę albumu');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }

    if(isset($_GET['remove_thumb']))
    {
        try
        {
            $cat = new Category($_GET['remove_thumb']);
            $cat->thumb = NULL;
            $cat->save();
            debug($cat);
            unset($cat);
            message('info', 'Usunięto miniaturę albumu');
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
            $list = new PicturesList(1);
            $list->fetch();
            
            foreach($list->list as $list_entry) {
                $pic = new Picture($list_entry->id);
                debug($pic);
                $pic->drop();
                unset($pic);
            }
            
            unset($list);
            message('info', 'Opróżniono kosz');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_POST['pic']))
    {
        try
        {
            debug($_FILES);
            if(is_uploaded_file($_FILES['userfile']['tmp_name']))
            {
                $filename = Picture::generateFilename($_FILES['userfile']['tmp_name']);
                $path = "{$config['img_dir']}/$filename";

                if(move_uploaded_file($_FILES['userfile']['tmp_name'], $path));
                else
                    throw new Exception('Wystąpił błąd podczas dodawania zdjęcia: nie można przenieść pliku');
            }
            else
            {
                throw new Exception('Nie podano ścieżki do pliku ze zdjęciem');
            }
            
            
            $pic = new Picture();
            $pic->id = $_POST['id'];
            $pic->caption = $_POST['caption'];
            $pic->cat_id = $_POST['cat_id'];
            $pic->file = $filename;
            $pic->save(WITHOUT_ASSIGN);
            unset($pic);
            message('info', 'Dodano zdjęcie');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_GET['pic_edit']))
    {
        try
        {
            $pic = new Picture();
            $pic->id = $_GET['pic_edit'];
            $pic->load();
            $content->picture_edit_form = $pic;
            
            $cat = new Category();
            $cat->id = $pic->cat_id;
            $cat->load();
            $array['id'] = $pic->cat_id;
            $array['title'] = $cat->title;
            $content->active_category = $array;
            unset($pic);
            unset($cat);
            unset($array);
            
            $cat = new CategoriesList();
            $cat->fetch();
            debug($cat->list);
            $content->categories_form = $cat;
            unset($cat);
            
            $cat = new CategoriesList(0);
            $cat->fetch();
            $content->categories_npublic_form = $cat;
            unset($cat);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_POST['pic_edit']))
    {
        try
        {
            $pic = new Picture();
            $pic->id = $_POST['id'];
            $pic->load();
            $pic->cat_id = $_POST['cat_id'];
            $pic->caption = $_POST['caption'];
            $pic->save();
            message('info', 'Zmieniono informacje o zdjęciu');
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_GET['pic_remove']))
    {
        try
        {
            $pic = new Picture();
            $pic->id = $_GET['pic_remove'];
            $pic->load();
            $pic->cat_id = 1;
            $pic->save();
            unset($pic);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    if(isset($_GET['pic_drop']))
    {
        try
        {
            $pic = new Picture();
            $pic->id = $_GET['pic_drop'];
            $pic->load();
            $pic->drop();
            unset($pic);
        }
        catch(Exception $e)
        {
            message('error', $e->getMessage());
        }
    }
    
    {
        function insertPictureFromDir($dir)
        {
            global $config;

            $list = array_diff(scandir($dir), array(".",".."));
            $i = 0;
            foreach($list as $file)
            {
                debug($file);
                $type = Picture::checkImageType($dir.'/'.$file);
                debug($type);
                if($type == 'jpg' || $type == 'png' || $type == 'gif')
                {
                    if($i == 0)
                    {
                        $cat = new Category();
                        $cat_title = array_reverse(explode('/', $dir));
                        debug($cat_title);
                        $cat->title = mb_convert_encoding($cat_title[0], "UTF-8", $config['filesystem_encoding']);
                        $cat->save();
                        $id = $cat->id;
                        unset($cat);
                    }

                    $filename = Picture::generateFilename($dir.'/'.$file).'.'.$type;
                    if(rename($dir.'/'.$file, $config['img_dir'].'/'.$filename))
                    {
                        $pic = new Picture();
                        $pic->cat_id = $id;
                        $pic->file = $filename;
                        $pic->save(WITHOUT_ASSIGN);
                        unset($pic);
                        $i++;
                    }
                }
                elseif(is_dir($dir.'/'.$file))
                {
                    $i = $i + insertPictureFromDir($dir.'/'.$file);
                }
                
            }
            return $i;
        }

       try
        {
            $count = insertPictureFromDir($config['upload_dir']);
            if($count > 0)
            {
                message('info', "Dodano $count zdjęć z FTP");
            }
            unset($count);
        }
        catch(Exception $e)
        {
            message('error', 'Błąd podczas dodawania zdjęć przez FTP: '.$e->getMessage());
        }
    } 
}


if(isset($_GET['cat_list']) || (!isset($_GET['cat']) && !isset($_GET['view'])))    // Displays albums
{
    $cat = new CategoriesList();
    $cat->fetch();
    $set[] = clone $cat;
    if($cat->list == NULL) message('warn', errmsg('no_pictures_cat'));
    unset($cat);
    
    if(Site::isUser())
    {
        $cat = new CategoriesList(0);
        $cat->fetch();
        $set[] = clone $cat;
        $content->recycle_bin = 'exists';
        unset($cat);
        
        if(!isset($_GET['cat_edit']) && !isset($_GET['pic_edit']) && $generate_cat_form)
        {
            $cat = new Category();
            $content->cat_form = $cat;
            unset($cat);
        }
    }
    $content->categories = $set;
    unset($set);
}

if(isset($_GET['cat']))                             // Displays album as thumbnails
{
    $thumbs = new PicturesList($_GET['cat']);
    $thumbs->fetch();
    if($thumbs->list == NULL && $_GET['cat'] != 1) message('warn', errmsg('no_pictures'));
    $content->thumbs = $thumbs;
    unset($thumbs);
    
    $cat = new Category();
    $cat->id = $_GET['cat'];
    $cat->load();
    $content->cat = $cat;
    unset($cat);
    
    if(Site::isUser())
    {
        if(!isset($_GET['pic_edit']))
        {
            $pic = new Picture();
            $content->picture_form = $pic;
            unset($pic);
        }
        if($_GET['cat'] == 1) $content->is_recycle = 1;
    }
}

if(isset($_GET['view']))                               // Displays picture
{
    $pic = new Picture();
    $pic->id = $_GET['view'];
    $pic->load();
    $content->picture = $pic;

    $cat = new PicturesList($pic->cat_id);
    $cat->fetch();
    $content->cat = $cat;
    
    for($i=0;$i<count($cat->list);$i++)
    {
        if($pic->id == $cat->list[$i]->id)
        {
            if(array_key_exists($i+1, $cat->list))
            {
                $next = new Picture();
                $next->id = $cat->list[$i+1]->id;
                $next->load();
                $content->next = $next;
            }
            else
            {
                $next = new Picture();
                $next->id = $cat->list[0]->id;
                $next->load();
                $content->next = $next;
            }

            if(array_key_exists($i-1, $cat->list))
            {
                $prev = new Picture();
                $prev->id = $cat->list[$i-1]->id;
                $prev->load();
                $content->prev = $prev;
            }
            else
            {
                $prev = new Picture();
                $prev->id = $cat->list[count($cat->list)-1]->id;
                $prev->load();
                $content->prev = $prev;
            }
         }
    }
    unset($pic);
    unset($cat);
}

?>
