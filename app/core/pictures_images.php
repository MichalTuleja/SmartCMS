<?php
define('WITH_ASSIGN', 1);
define('WITHOUT_ASSIGN', 0);

class Picture implements pictureInterface
{
    var $id;
    var $cat_id;
    var $caption;
    var $ctime;
    var $mtime;
    var $user;
    var $file;
    var $converted_file = array('thumb' => NULL, 'medium' => NULL, 'high'=> NULL);
    var $next;
    var $prev;


    function  __construct($id = NULL) {
        if($id != NULL) {
            $this->id = $id;
            $this->load();
        }
    }
    
    public function load()
    {
        global $pdo, $config;
        
        try 
        {
            $stmt = $pdo->prepare("SELECT picture_id AS id,
                                          picture_category AS cat_id,
                                          picture_user AS user,
                                          picture_ctime AS ctime,
                                          picture_mtime AS mtime,
                                          picture_caption AS caption,
                                          picture_filename AS file
                                     FROM {$config['db_pictures']}
                                    WHERE picture_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->execute();
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            $this->id = $array['id'];
            $this->cat_id = $array['cat_id'];
            $this->user = $array['user'];
            $this->ctime = $array['ctime'];
            $this->mtime = $array['mtime'];
            $this->caption = $array['caption'];
            $this->file = $array['file'];
            unset($array);
            $this->assign_files();
        }
        catch(PDOException $e)
        {
            message('error', errmsg('DBread'));
            debug("Error: $e");
        }
    }


    private function assign_files()
    {
        try
        {
            global $config;
            if(is_file("{$config['img_dir']}/$this->file"))
            {
                $file = pathinfo("{$config['img_dir']}/$this->file");
                debug($file);
                $type = $this->checkImageType("{$config['img_dir']}/$this->file");
                debug($type);
                if(!isset($file['extension']) || $type != $file['extension'])
                {
                    $old_name = "{$config['img_dir']}/$this->file";
                    $new_name = "{$config['img_dir']}/{$file['filename']}.$type";
                    rename($old_name, $new_name);
                    $this->file = "{$file['filename']}.$type";
                    $this->save(WITHOUT_ASSIGN);
                    $file = pathinfo("{$config['img_dir']}/$this->file");
                }


                foreach(array('thumb', 'medium', 'high') as $i)
                {
                    $file1 = $file['filename'].'_'.$config[$i.'_res'].'.'.$file['extension'];
                    if(!is_file($config['img_dir'].'/'.$file1))
                    {
                        $src = $config['img_dir'].'/'.$this->file;
                        $dest = $config['img_dir'].'/'.$file1;
                        $res = $config[$i.'_res'];
                        $this->resizeImage($src, $dest, $res);
                    }
                    $this->converted_file[$i] = $file1;
                }
            }
            else
                throw new Exception($this->file);
        }
        catch(Exception $e)
        {
            message('error', 'Wystąpił błąd podczas ładowania zdjęcia');
            debug("Error: no file {$e->getMessage()}");
        }
    }

    public function save($assign = 1)
    {
        if($this->id > 0) $this->update();
        else $this->insert();
        if($assign == 1) $this->assign_files();
    }
    
    private function insert()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("INSERT INTO {$config['db_pictures']}
                                    VALUES (NULL, :category, :user, NOW(), NOW(), :caption, :filename)");
            $stmt->bindValue(':category', $this->cat_id, PDO::PARAM_STR);
            $stmt->bindValue(':user', Site::getName(), PDO::PARAM_STR);
            $stmt->bindValue(':caption', $this->caption, PDO::PARAM_STR);
            $stmt->bindValue(':filename', $this->file, PDO::PARAM_STR);
            $stmt->execute();
            $this->id = $pdo->lastInsertId();
            $stmt->closeCursor();
        }
        catch(PDOException $e)
        {
            message('error', errmsg('DBwrite'));
            debug("Error: $e");
        }
    }
    
    
    private function update()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("UPDATE {$config['db_pictures']}
                                      SET picture_category = :category, 
                                          picture_caption = :caption,
                                          picture_mtime = NOW(),
                                          picture_filename = :file
                                    WHERE picture_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->bindValue(':category', $this->cat_id, PDO::PARAM_STR);
            $stmt->bindValue(':caption', $this->caption, PDO::PARAM_STR);
            $stmt->bindValue(':file', $this->file, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            message('error', errmsg('DBwrite'));
            debug("Error: $e");
        }
    }
    
    
    public function drop()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("UPDATE {$config['db_albums']}
                                      SET picture_categories_picture_id = NULL 
                                    WHERE picture_categories_picture_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $stmt = $pdo->prepare("DELETE FROM {$config['db_pictures']}
                                    WHERE picture_id = :id");
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }
        catch(PDOException $e)
        {
            debug("Error: $e");
            throw new Exception(errmsg('DBwrite'));
        }
        try
        {
            $this->assign_files();
            foreach($this->converted_file as $name)
            {
                unlink("{$config['img_dir']}/{$name}");
            }
            unlink("{$config['img_dir']}/{$this->file}");
        }
        catch(Exception $e)
        {
            debug("Error: $e");
            throw new Exception('Wystąpił błąd podczas usuwania pliku');
        }
    }

    

    static public function resizeImage($source, $dest, $res)  // TODO: Add GIF support
    {
        if(exif_imagetype($source) == IMAGETYPE_JPEG)
        {
            $img = imagecreatefromjpeg($source);
            $format = 0;
        }
        elseif(exif_imagetype($source) == IMAGETYPE_PNG)
        {
            $img = imagecreatefrompng($source);
            $format = 1;
        }
        elseif(exif_imagetype($source) == IMAGETYPE_PNG)
        {
            $img = imagecreatefromgif($source);
            $format = 2;
        }
        else
        {
            throw new Exception('Niepoprawny format pliku. Obsługiwane formaty to JPG i PNG');
        }

        try
        {
            global $config;
            
            $x = imagesx($img);
            $y = imagesy($img);

            if($x > $y*(4/3))
            {
                $nx = $res;
                $ny = $res * ($y/$x);
            }
            else
            {
                $ny = $res / (4/3);
                $nx = $ny / ($y/$x);
            }

            $new_img = imagecreatetruecolor($nx, $ny);
            imagecopyresampled($new_img, $img, 0, 0, 0, 0, $nx, $ny, $x, $y);

            if($format == 0){
                imagejpeg($new_img, $dest, $config['jpeg_quality']);
            }
            elseif($format == 1)
            {
                imagepng($new_img, $dest);
            }
            else
            {
                imagegif($new_img, $dest);
            }
        }
        catch(Exception $e)
        {
            throw new Exception($errmsg['picture_resize_error']);
        }
    }


    public function insertImage($tmp_file)
    {
        $random_filename = generateRandomString();

        $img_type = $this->checkImageType($original_file);
        $original_file = "{$config['img_dir']}/$random_filename";

        if(rename($tmp_file, $original_file));
        else
            throw new Exception("Wystąpił błąd podczas wstawiania zdjęcia");

        $this->resizeImage($original_file, "{$config['img_dir']}/$random_filename_{$config['thumb_res']}", $config['thumb_res']);
        $this->resizeImage($original_file, "{$config['img_dir']}/$random_filename_{$config['med_res']}", $config['med_res']);
        $this->resizeImage($original_file, "{$config['img_dir']}/$random_filename_{$config['high_res']}", $config['high_res']);

        $filename = $random_filename.$img_type;
        rename($original_file, 'img/original/'.$filename);

        return $filename;

    }

    static public function generateFilename($file)
    {
        return time().'_'.sha1_file($file);
    }

    static function checkImageType($file)
    {
        if(!is_dir($file))
            {
            if(exif_imagetype($file) == IMAGETYPE_JPEG)
            {
                return 'jpg';
            }
            elseif(exif_imagetype($file) == IMAGETYPE_PNG)
            {
                return 'png';
            }
            elseif(exif_imagetype($file) == IMAGETYPE_GIF)
            {
                return 'gif';
            }
            else
            {
                return 0;
            }
        }
    }

}



class RandomPictures implements RandomPicturesInterface
{
    var $number;
    var $list;
    
    function __construct($n = 3)
    {
        $this->number = $n;
        $this->fetch();
    }
    
    
    function fetch()
    {
        global $pdo, $config;
        
        try
        {
            $stmt = $pdo->prepare("SELECT picture_id AS id
                                     FROM {$config['db_pictures']}
                                     WHERE (SELECT picture_categories_public 
                                              FROM {$config['db_albums']}
                                             WHERE picture_category = picture_categories_id) = 1
                                     ORDER BY RAND() 
                                     LIMIT :number");
            $stmt->bindValue(':number', $this->number, PDO::PARAM_INT);
            $stmt->execute();
            while($pic = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $this->list[] = new Picture($pic['id']);
            }
            $stmt->closeCursor();
            debug($this);
        }
        catch(Exception $e)
        {
            debug($e->getMessage());
            message('error', 'Wystąpił błąd podczas generowania losowych zdjęć');
        }
    }
    
    
}

?>
