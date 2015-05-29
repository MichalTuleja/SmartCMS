<?php

class CategoriesList implements CategoriesListInterface
{
    var $public;
    var $list;

    function __construct($public = 1)
    {
        $this->public = $public;
    }
    
    public function fetch()
    {
        global $pdo, $config;
        

        try 
        {
            $array = NULL;
            
            $stmt = $pdo->prepare("SELECT picture_categories_id AS id,
                                                    picture_categories_title AS title,
                                                    picture_categories_descr AS descr,
                                                    picture_categories_picture_id AS thumb_id
                                            FROM {$config['db_albums']}
                                            WHERE picture_categories_public = :public
                                                AND picture_categories_id > 1
                                            ORDER BY picture_categories_mtime DESC");
            $stmt->bindValue(':public', $this->public, PDO::PARAM_STR);
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row['thumb_id'] != NULL){
                      $thumb = new Picture($row['thumb_id']);
                }
                else
                    $thumb = NULL;
                $array[] = array('id' => $row['id'],
                                 'title' => $row['title'],
                                 'descr' => $row['descr'],
                                 'thumb' => $thumb);
            }
            $stmt->closeCursor();

            debug($array);
            $this->list = $array;
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
}


class PicturesList implements PicturesListInterface
{
    var $id;
    var $list;

    function __construct($id)
    {
        $this->id = $id;
    }
    
    function fetch()
    {
        global $pdo, $config;
        
        try 
        {
            $stmt = $pdo->prepare("SELECT picture_id AS id
                                     FROM {$config['db_pictures']}
                                    WHERE picture_category = :category_id");
            $stmt->bindValue(':category_id', $this->id, PDO::PARAM_STR);
            $stmt->execute();

            while($pic = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $this->list[] = new Picture($pic['id']);
            }
            $stmt->closeCursor();
            
            debug($this);
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            //throw new Exception(errmsg('DBread'));
            message('error' ,errmsg('DBread'));
        }
    }
    
    function isRecycleBin()
    {
        if ($this->id == 1) return 1; else return 0;
    }
}

?>
