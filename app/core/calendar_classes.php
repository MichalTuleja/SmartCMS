<?php

class Calendar implements CalendarInterface
{
    var $year;
    var $month;
    var $fields;
    var $caption;
    
    function __construct($date = NULL)
    {
        if($date != NULL)
        {
            $date_array = explode('-', $date);
            $this->year = $date_array[0];
            $this->month = $date_array[1];
        }
        else
        {
            $this->year = date('Y', time());
            $this->month = date('m', time());
        }
        $this->load();
        $this->setCaption();
    }
    
    
    function load()
    {
        global $pdo, $config;
            
        try
        {
            $num = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
            $offset = date('w', mktime(00, 00, 00, $this->month, 1, $this->year))-1;
            if($offset == -1) $offset = 6;
            
            $gt = mktime(00, 00, 00, $this->month, 1, $this->year);
            $lt = mktime(23, 59, 59, $this->month, $num, $this->year);
            $stmt = $pdo->prepare("SELECT news_id AS id, 
                                                 news_user AS author, 
                                                 news_title AS title,
                                                 news_mtime AS mtime, 
                                                 news_body AS body,
                                                 news_meettime AS meettime 
                                            FROM {$config['db_news']}
                                            WHERE news_removed = 0
                                              AND news_meettime > :gt
                                              AND news_meettime < :lt");
            $stmt->bindValue(':gt', $gt, PDO::PARAM_STR);
            $stmt->bindValue(':lt', $lt, PDO::PARAM_STR);
            $stmt->execute();
            $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            for($i=1; $i<=43; $i++)
            {
                $timeline[$i]['day'] = NULL;
            }
            
            for($i=1+$offset; $i<=$num+$offset; $i++)
            {
                $timeline[$i]['day'] = $i-$offset;
            }

            foreach($meetings as $row)
            {
                $day = date('d', $row['meettime'])+$offset;
                $timeline[$day]['hour'] = date('H', $row['meettime']);
                $timeline[$day]['minute'] = date('i', $row['meettime']);
                $timeline[$day]['title'] = $row['title'];
                $timeline[$day]['body'] = $row['body'];
            }

            $array['row'][] = array_slice($timeline, 0, 7);
            $array['row'][] = array_slice($timeline, 7, 7);
            $array['row'][] = array_slice($timeline, 14, 7);
            $array['row'][] = array_slice($timeline, 21, 7);
            $array['row'][] = array_slice($timeline, 28, 7);
            $array['row'][] = array_slice($timeline, 35, 7);
            //debug($array1);
            
            debug($array);
            $this->fields = $array;
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            message('error', "Error during database access.");
        }
    }
    
    private function setCaption()
    {
        $array = array(1 => 'January', 'February', 'March', 'April', 'May', 
                            'June', 'July', 'August', 'September', 
                            'October', 'November', 'December');
                            
        $this->caption = $array[(int)$this->month].' '.$this->year;
    }
}

?>
