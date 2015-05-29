<?php

class News implements NewsInterface
{
    var $id;
    var $ctime;
    var $mtime;
    var $meettime;
    var $meet_date;
    var $meet_hour;
    var $meet_minute;
    var $user;
    var $removed;
    var $title;
    var $body;
    
    
    function __construct($id = NULL)
    {
        if($id != NULL)
        {
            $this->id = $id;
            $this->load();
        }
    }
    

    public function load()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("SELECT news_id AS id, 
                                          news_user AS author, 
                                          news_ctime AS ctime,
                                          news_mtime AS mtime,
                                          news_meettime AS meettime,
                                          news_removed AS removed,
                                          news_title AS title,
                                          news_body AS body 
                                     FROM {$config['db_news']}
                                    WHERE news_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            $this->ctime = $array['ctime'];
            $this->mtime = $array['mtime'];
            $this->meettime = $array['meettime'];
            if($array['meettime'] != NULL) $this->convertDate();
            $this->user = $array['author'];
            $this->removed = $array['removed'];
            $this->title = $array['title'];
            $this->body = $array['body'];
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBread'));
        }
    }
    
    
    public function save()
    {
        if($this->id == NULL) $this->insert();
        elseif($this->id > 0) $this->update();
        else throw new Exception('Błąd podczas zapisu');
    }
    
    
    private function insert()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo -> prepare("INSERT INTO {$config['db_news']} VALUES (NULL, 0, :name, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), :meettime, :title, :body)");
            $stmt->bindValue(':name', Site::getName(),  PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title,  PDO::PARAM_STR);
            $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
            $stmt->bindValue(':meettime', $this->meettime, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite')."\n".$e);
        }
    }
    
    
    private function update()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("UPDATE {$config['db_news']}
                                             SET news_title = :title,
                                                 news_body = :body,
                                                 news_mtime = UNIX_TIMESTAMP(NOW()),
                                                 news_removed = :removed,
                                                 news_meettime = :meettime
                                            WHERE news_id = :id");
            $stmt -> bindValue(':title', $this->title,  PDO::PARAM_STR);
            $stmt -> bindValue(':body', $this->body, PDO::PARAM_STR);
            $stmt -> bindValue(':removed', $this->removed, PDO::PARAM_STR);
            $stmt -> bindValue(':meettime', $this->meettime, PDO::PARAM_INT);
            $stmt -> bindValue(':id', $this->id, PDO::PARAM_STR);
            $number = $stmt->execute();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
    
    
    public function drop()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo -> prepare("DELETE FROM {$config['db_news']}
                                                WHERE news_id = :id");
            $stmt -> bindValue(':id', $this->id, PDO::PARAM_STR);
            $number = $stmt->execute();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
    }
    
    private function convertDate()
    {
        $this->meet_date = date('Y-m-d', $this->meettime);
        $this->meet_hour = date('H', $this->meettime);
        $this->meet_minute = date('i', $this->meettime);
    }
}


class NewsList implements NewsListInterface
{
    var $data;
    var $list;
    var $removed;
    var $limit;
    
    function __construct($removed = NULL, $limit = 0)
    {
        if($removed != NULL) $this->removed = $removed;
        else $this->removed = 0;
        $this->limit = $limit;
        $this->load();
        $this->timeFormat();
    }
    
    public function load()
    {
        global $pdo, $config;
        
        //$offset = $page*$quantity;
        
        try
        {
            if($this->hasLimit())
            {
                $stmt = $pdo->prepare("SELECT news_id AS id, 
                                                     news_user AS author, 
                                                     news_title AS title,
                                                     news_mtime AS mtime,
                                                     news_ctime AS ctime,
                                                     news_body AS body 
                                                FROM {$config['db_news']}
                                                WHERE news_removed = :removed
                                                ORDER BY news_ctime DESC LIMIT :limit");
                $stmt->bindValue(':removed', $this->removed, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $this->limit, PDO::PARAM_INT);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT news_id AS id, 
                                                     news_user AS author, 
                                                     news_title AS title,
                                                     news_mtime AS mtime,
                                                     news_ctime AS ctime,
                                                     news_body AS body 
                                                FROM {$config['db_news']}
                                                WHERE news_removed = :removed
                                                ORDER BY news_ctime DESC");
                $stmt->bindValue(':removed', $this->removed, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
            debug($array);
            $stmt->closeCursor();

            for($i=0;$i<count($array);$i++)
            {
                $array[$i]['body'] = Site::textFilter($array[$i]['body']);
            }
            $this->list = $array;
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            message('error', "Wystąpił błąd przy odczycie z bazy.");
        }
    }
    
    private function timeFormat()
    {
        for($i = 0; $i<count($this->list); $i++)
        {
            $this->list[$i]['mtime'] = formatDate($this->list[$i]['mtime']);
            $this->list[$i]['ctime'] = formatDate($this->list[$i]['ctime']);
        }
    }
    
    
    public function hasLimit()
    {
        if($this->limit == 0) return 0;
        else return 1;
    }
    
    public function showsRemoved()
    {
        return $this->removed;
    }

}

?>
