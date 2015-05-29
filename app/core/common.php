<?php
define("DATETIME", "d.m.Y H:i");
define("DATE", "d.m.Y");
define("TIME", "H:i");


class Site
{
    static public function isUser()
    {
        global $session;
        if($session->getUser()->isUser())   return 1;
        else return 0;
    }

    static public function getId()
    {
        global $session;
        return $session->getUser()->getId();
    }

    static public function getLogin()
    {
        global $session;
        return $session->getUser()->getLogin();
    }

    static public function getName()
    {
        global $session;
        return $session->getUser()->getName();
    }

    static public function generateRandomString($length = 250)
    {
        $code = md5(uniqid(rand(), true));
        if ($length != "") return substr($code, 0, $length);
        else return $code;
    }

    static public function textFilter($text, $filter = NULL)
    {
        if($filter == NULL)
        {
            $filter = '<embed><object><param><img><b><i><s><u><a><ul><li><iframe><br><p>';
            return strip_tags($text, $filter);
        }
        else
        {
            return strip_tags($text, $filter);
        }
    }
}





class Page
{
    private $page = 'news';
    public $file;
    public $header = 1;
    public $background = NULL;

    function __construct()
    {
        if(isset($_GET['page']))
            $this->setPage($_GET['page']);
        else
            $this->setPage($this->page);
        
        if(isset($_GET['show_header']))
            if($_GET['show_header'] == 0 || $_GET['show_header'] == 1)
                $this->header = $_GET['show_header'];
    }
    
    public function getPage() {return $this->page;}
    public function getEngineFile() {return $this->file['engine'];}
    public function getRenderFile() {return $this->file['render'];}
    private function setFile($file) {$this->file = $file;}
    
    private function setPage($page)
    {
        switch($page)
        {
            case 'news':            $this->setFile(array('engine' => 'news.php', 'render' => 'news')); break;
            case 'articles':    $this->setFile(array('engine' => 'articles.php', 'render' => 'articles')); break;
            case 'pictures':    $this->setFile(array('engine' => 'pictures.php', 'render' => 'pictures')); break;
            case 'admin':       $this->setFile(array('engine' => 'admin.php', 'render' => 'admin')); break;
        }
    }


}





function formatDate($timestamp)
{
    return date(DATETIME, $timestamp);
}




//message('info', 'Info box');
//message('warn', 'Warning box');
//message('error', 'Error box');
?>
