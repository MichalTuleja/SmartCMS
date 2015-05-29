<?php

class Category implements CategoryInterface
{
    var $id;
    var $title;
    var $descr;
    var $ctime;
    var $mtime;
    var $user;
    var $public;
    var $thumb;


    function  __construct($id = NULL)
    {
        if($id != NULL) {
            $this->id = $id;
            $this->load();
        }
        $this->mtime = date('Y-m-d');
    }

    function load()
    {
        global $pdo, $config;
        
        try 
        {
            $stmt = $pdo->prepare("SELECT picture_categories_id AS id,
                                          picture_categories_title AS title,
                                          picture_categories_descr AS descr,
                                          picture_categories_public AS public,
                                          picture_categories_ctime AS ctime,
                                          DATE(picture_categories_mtime) AS mtime,
                                          picture_categories_user AS user,
                                          picture_categories_picture_id AS thumb
                                     FROM {$config['db_albums']}
                                    WHERE picture_categories_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            $this->title = $array['title'];
            $this->descr = $array['descr'];
            $this->public = $array['public'];
            $this->ctime = $array['ctime'];
            $this->mtime = $array['mtime'];
            $this->user = $array['user'];
            $this->thumb = $array['thumb'];
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
    
    function save()
    {
        if($this->id > 0) $this->update();
        else $this->insert();
    }
    
    
    private function insert()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("INSERT INTO {$config['db_albums']}
                                        VALUES (NULL, 0, :user, NOW(), DATE(:mtime), :title, :descr, NULL)");
            $stmt->bindValue(':user', Site::getName(), PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':descr', $this->descr, PDO::PARAM_STR);
            $stmt->bindValue(':mtime', $this->mtime, PDO::PARAM_STR);
            debug($this);
            $stmt->execute();
            $this->id = $pdo->lastInsertId();
            $stmt->closeCursor();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
    
    
    private function update()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("UPDATE {$config['db_albums']}
                                      SET picture_categories_public = :public, 
                                          picture_categories_title = :title, 
                                          picture_categories_descr = :descr,
                                          picture_categories_mtime = DATE(:mtime),
                                          picture_categories_picture_id = :thumb
                                    WHERE picture_categories_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->bindValue(':public', $this->public, PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':descr', $this->descr, PDO::PARAM_STR);
            $stmt->bindValue(':mtime', $this->mtime, PDO::PARAM_STR);
            $stmt->bindValue(':thumb', $this->thumb, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
    

    function remove()
    {
        global $pdo, $config;
        
        try
        {
            if($this->id == 1) throw new Exception($errmsg['DBwrite'].' (id = 1)');
            
            $stmt = $pdo->prepare("UPDATE {$config['db_pictures']}
                                      SET picture_category = '1'
                                    WHERE picture_category = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
            
            $stmt = $pdo->prepare("DELETE FROM {$config['db_albums']}
                                         WHERE picture_categories_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
}

?>
