<?php
 
	class user
	{
		private $id;
		private $login;
		private $name;
		private $password;
		private $lastvisit;
		private $construct;

		
		public function __construct($anonymous = true)
		{
			if($anonymous == true)
			{
				$this -> id = 0;
				$this -> login = '';
				$this -> name = '';
				$this -> password = '';
				$this -> lastvisit = time();
			}
			$this -> construct = true;
		} // end __construct();
		
		public function isAnonymous()
		{
			return ($this -> id == 0 ? true : false);
		} // end isAnonymous();

		public function isUser()
		{
			return ($this -> id != 0 ? true : false);
		} // end isUser();


		public function getId()
		{
			return $this -> id;
		} // end getId();
		
		public function getLogin()
		{
			return $this -> login;
		} // end getLogin();

		public function getName()
		{
			return $this -> name;
		} // end getName();
		
		public function getPassword()
		{
			return $this -> password;
		} // end getPassword();
		
		public function getLastvisit()
		{
			return date('d.m.Y, H:i', $this -> lastvisit);
		} // end getLastvisit();
 
		public function __set($name, $value)
		{
			if(!$this -> construct)
			{
				$this -> $name = $value;
			}
		} // end __set();
		
		static public function checkPassword($login, $password)
		{
			global $pdo, $config;
			$stmt = $pdo -> prepare("SELECT user_id AS id,
				user_login AS login, 
				user_name AS name, 
				user_password AS password, 
				user_lastvisit AS lastvisit 
                                FROM {$config['db_users']} WHERE
				 user_login = :login AND user_password = :password");
			$stmt -> bindValue(':login', 	$login,		 		PDO::PARAM_STR);
			$stmt -> bindValue(':password', sha1($password), 	PDO::PARAM_STR);
			$stmt -> execute();
			$stmt -> setFetchMode(PDO::FETCH_CLASS, 'user', array(0 => false));
			if($user = $stmt -> fetch())
			{
				// Jezeli uzytkownik o takim loginie i hasle 
				// istnieje, zwroc jego rekord w postaci obiektu
				$stmt -> closeCursor();
				return $user;			
			}
			else
			{
				$stmt -> closeCursor();
				// Bledy w loginie/hasle zglaszamy zerem
				return 0;
			}
		} // end checkPassword();
	}
?>
