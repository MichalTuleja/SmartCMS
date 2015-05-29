<?php

class Article implements ArticleInterface
{
    var $id;
    var $cat_id;
    var $ctime;
    var $mtime;
    var $public;
    var $removed;
    var $user;
    var $title;
    var $descr;
    var $body;
    
    function __construct($id = NULL)
    {
        if($id != NULL)
        {
            $this->id = $id;
            $this->load();
        }
    }
    
    function load()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("SELECT article_id AS id, 
                                                    article_user AS user,
                                                    article_ctime AS ctime,
                                                    article_mtime AS mtime,
                                                    article_public AS public,
                                                    article_removed AS removed,
                                                    article_title AS title,
                                                    article_descr AS descr, 
                                                    article_body AS body 
                                            FROM {$config['db_articles']}
                                            WHERE article_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            //debug($array);
            $this->ctime = $array['ctime'];
            $this->mtime = $array['mtime'];
            $this->public = $array['public'];
            $this->removed = $array['removed'];
            $this->user = $array['user'];
            $this->title = $array['title'];
            $this->descr = $array['descr'];
            $this->body = $array['body'];
        }
        catch(PDOException $e)
        {
            message('error', "Wystąpił błąd podczas odczytu bazy danych");
            debug("Error: $e");
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
            $stmt = $pdo->prepare("INSERT INTO {$config['db_articles']}
                                            VALUES (NULL, 0, 0, 
                                                      :user, NOW(), NOW(), 
                                                      :title, :descr, :body, NULL)");
            $stmt->bindValue(':user', Site::getName(), PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
            $stmt->bindValue(':descr', $this->descr, PDO::PARAM_STR);
            $stmt->execute();
            $this->id = $pdo->lastInsertId();
        }
        catch(PDOException $e)
        {
            message('error', "Wystąpił błąd podczas zapisu do bazy danych");
            debug("Error: $e");
        }
    }
    
    private function update()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("UPDATE {$config['db_articles']}
                                            SET article_user = :user, 
                                                 article_title = :title, 
                                                 article_descr = :descr, 
                                                 article_body = :body, 
                                                 article_mtime = NOW(),
                                                 article_public = :public,
                                                 article_removed =  :removed
                                            WHERE article_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->bindValue(':user', Site::getName(), PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
            $stmt->bindValue(':descr', $this->descr, PDO::PARAM_STR);
            $stmt->bindValue(':public', $this->public, PDO::PARAM_STR);
            $stmt->bindValue(':removed', $this->removed, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            message('error', "Wystąpił błąd podczas zapisu do bazy danych");
            debug("Error: $e");
        }
    }
    
    function drop()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("DELETE FROM {$config['db_articles']} WHERE article_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            message('error', "Wystąpił błąd podczas zapisu do bazy danych");
            debug("Error: $e");
        }
    }
    
    function prepareView()
    {
        $filter = '<embed><object><param><img><b><i><s><u><a><ul><li><iframe><br><p>';
        $this->title = strip_tags($this->title);
        $this->descr = nl2br(strip_tags($this->descr));
        $this->body = strip_tags($this->body,$filter);
    }
    
    function descr_visible()
    {
        if($this->id > 2 || $this->id == NULL) return 1;
        else return 0;
    }

    function permanent()
    {
        if($this->id > 2) return 1;
        else return 0;
    }


}



class ArticlesList implements ArticlesListInterface
{
    var $removed;
    var $public;
    var $list;

    function __construct($public = 1, $removed = 0)
    {
        $this->public = $public;
        $this->removed = $removed;
    }

    function load()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("SELECT article_id AS id, 
                                                    article_user AS author, 
                                                    article_title AS title, 
                                                    article_ctime AS ctime,
                                                    article_mtime AS mtime,
                                                    article_descr AS descr 
                                            FROM {$config['db_articles']}
                                            WHERE article_removed = :removed 
                                                AND article_public = :public 
                                                AND article_id > '2' 
                                            ORDER BY ctime DESC");
            $stmt->bindValue(':removed', $this->removed, PDO::PARAM_STR);
            $stmt->bindValue(':public', $this->public, PDO::PARAM_STR);
            $stmt->execute();
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $this->list = $array;
        }
        catch(PDOException $e)
        {
            message('error', "Wystąpił błąd podczas odczytu bazy danych");
            debug("Error: $e");
        }
    }
}
?>
