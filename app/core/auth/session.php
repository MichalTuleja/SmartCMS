<?php
 
	define('COOKIE_NAME', 'rosnvfonvfcxn'); // losowe
	define('COOKIE_EXPIRE', 3600); // 1 godzina
 
	class session
	{
		private $id;
		private $ip;
		private $browser;
		private $time;
		private $user;
		
		public function __construct()
		{
			global $pdo, $request, $config;
			
			// Kontrola poprawnosci ciastka
			if(!isset($_COOKIE[COOKIE_NAME]))
			{
				$_COOKIE[COOKIE_NAME] = '';
			}
			if(strlen($_COOKIE[COOKIE_NAME]) != 40)
			{			
				$this -> create();
			}
			// Wyslanie zapytania o sesje. Od razu sprawdzamy
			// jej waznosc oraz zgodnosc IP i przegladarki
			$stmt = $pdo -> prepare("SELECT
					session_user, session_ip, 
					session_browser, session_time FROM {$config['db_sessions']}
				WHERE session_id = :sid AND
					session_ip = :sip AND
					session_browser = :sbrowser AND
					session_time > :time
					;");
			$stmt -> bindValue(':sid', 		$_COOKIE[COOKIE_NAME],		PDO::PARAM_STR);
			$stmt -> bindValue(':sip', 		$request -> getIp(),			PDO::PARAM_STR);
			$stmt -> bindValue(':sbrowser', 	$request -> getBrowser(),	PDO::PARAM_STR);
			$stmt -> bindValue(':time', 		time() - COOKIE_EXPIRE,		PDO::PARAM_INT);
			$stmt -> execute();
			if($session = $stmt -> fetch(PDO::FETCH_ASSOC))
			{
				$stmt -> closeCursor();
				$this -> id = $_COOKIE[COOKIE_NAME];
				$this -> ip = $session['session_ip'];
				$this -> browser = $session['session_browser'];
				$this -> time = $session['session_time'];
				setcookie(COOKIE_NAME, $this -> id, 
					time() + COOKIE_EXPIRE);
				$stmt = $pdo -> prepare("UPDATE {$config['db_sessions']} SET
					session_time = :time WHERE session_id = :sid");
				$stmt -> bindValue(':sid', $_COOKIE[COOKIE_NAME],
					PDO::PARAM_STR);
				$stmt -> bindValue(':time', time(), PDO::PARAM_INT);
				$stmt -> execute();
				if($session['session_user'] == 0)
				{
					// sesja anonimowa
					$this -> user = new user(true);
				}
				else
				{
					// sesja zalogowanego
					$stmt = $pdo -> prepare("SELECT user_id AS id,
						user_login AS login, 
						user_name AS name,
						user_password AS password, 
						user_lastvisit AS lastvisit
						FROM {$config['db_users']} WHERE user_id=:uid;");
					$stmt -> bindValue(':uid',
						$session['session_user'], PDO::PARAM_INT);
					
					$stmt -> execute();
					$stmt -> setFetchMode(PDO::FETCH_CLASS, 'user',
						array(0 => false));
					if($this -> user = $stmt -> fetch())
					{
						$stmt -> closeCursor();					
					}
					else
					{
						$stmt -> closeCursor();
						$this -> create();
					}				
				}
			}
			else
			{
				$stmt -> closeCursor();
				$this -> create();
			}
		} // end __construct(); 

		private function create()
		{
			global $pdo, $request, $config;
			$this -> garbageCollector();
			
			// utworz nowa anonimowa sesje. Wczesniej usun stare z bazy
			$this -> id = sha1(uniqid(time().$request->getIp()));
			setcookie(COOKIE_NAME, $this -> id, time() + COOKIE_EXPIRE);
			$stmt = $pdo -> prepare("INSERT INTO {$config['db_sessions']} (session_id,
				session_user, session_time, session_browser,
				session_ip) VALUES(
				:session_id, 0, :session_time, 
				:session_browser, :session_ip			
			);");
			$stmt -> bindValue(':session_id', 		 $this -> id, 			PDO::PARAM_STR);
			$stmt -> bindValue(':session_ip', 		 $request -> getIp(), 	PDO::PARAM_STR);
			$stmt -> bindValue(':session_browser', $request -> getBrowser(), PDO::PARAM_STR);
			$stmt -> bindValue(':session_time', 	 time(), 				PDO::PARAM_INT);
			$stmt -> execute();
			$this -> user = new user(true);
		} // end create(); 
		
		public function update(user $user)
		{
			global $pdo, $request, $config;
			
			if($user -> isAnonymous())
			{
				if($this -> user -> isAnonymous())
				{
					throw new Exception('Próba przerejestrowania
						anonimowego użytkownika!');
				}
				// Aktualizacja ostatnich odwiedzin, jesli 
				// wylogowujemy usera. 
				$stmt = $pdo->prepare("UPDATE {$config['db_users']} SET
					user_lastvisit = :lastvisit 
					WHERE user_id = :uid;");
				$stmt -> bindValue(':lastvisit', time(), 							 PDO::PARAM_INT);
				$stmt -> bindValue(':uid', 			 $this->user->getId(), PDO::PARAM_INT);
				$stmt -> execute();
			}
			// Zmiana ID sesji oraz przypisanie do niej usera
			$newId = sha1(uniqid(time().$request->getIp()));
			setcookie(COOKIE_NAME, $newId, time() + COOKIE_EXPIRE);
			$stmt = $pdo -> prepare("UPDATE {$config['db_sessions']} SET
				session_id = :new_id, session_user = :user 
				WHERE session_id = :sid;");
			$stmt -> bindValue(':new_id', $newId, PDO::PARAM_STR);
			$stmt -> bindValue(':sid', $this -> id, PDO::PARAM_STR);
			$stmt -> bindValue(':user', $user -> getId(),
				PDO::PARAM_INT);
			$stmt -> execute();
 
			$this -> id = $newId;
			$this -> user = $user;
		} // end update(); 

		private function garbageCollector()
		{
			global $pdo, $config;
			
			// Usun stare sesje i przenies do uzytkownikow
			// czas ostatniej aktywnosci jako ostatnia wizyte
			$pdo -> exec("UPDATE {$config['db_users']}, {$config['db_sessions']}
				SET {$config['db_users']}.user_lastvisit = {$config['db_sessions']}.session_time
				WHERE {$config['db_users']}.user_id={$config['db_sessions']}.session_user AND
				{$config['db_sessions']}.session_time < ".(time() - COOKIE_EXPIRE));
			$pdo -> exec("DELETE FROM {$config['db_sessions']} WHERE
				session_time < ".(time() - COOKIE_EXPIRE));
		} // end garbageCollector();
 
		public function getUser()
		{
			return $this -> user;
		} // end getUser();
	}
?>
