<?php 

define('START_PATH',"../../");

include realpath(START_PATH.'config/connections.php'); //contiene la connessione al mysql

$link = openDBConn(); //richiamo la connessione
$link -> set_charset("utf8"); //imposto la codifica a utf8

include realpath(START_PATH.'config/config.php'); //contiene variabili e altro per la configurazione iniziale del cms

include realpath(START_PATH.'config/functions.php'); //contiene tutte le funzioni usate nel cms

include realpath('config/utilities.php'); //contiene le configurazioni diversificate per lingua

$username = checkSet($_POST['admin-username']);
$password = checkSet($_POST['admin-password']);
$ricorda = isset($_POST['admin-ricorda'] ) && !strcmp('on',$_POST['admin-ricorda']) ? (time() + 60*60*24*365) : $time_login; 

if(strcmp('',$username) && strcmp('',$password)){
	
	$query_login = "SELECT * from users where username='".mysqli_real_escape_string($link,$username)."' and password='".mysqli_real_escape_string($link,md5($password))."' ";
	//$query_login = "SELECT * from users where username='$username' and password='".md5($password)."' ";
	union_killer($query_login,1,2);
	
	$result_login =mysqli_query($link,$query_login);
	if($result_login ==false)
	{
		$risposta = array('check'=>'errore query');	
		$rispo=json_encode($risposta);
		die($rispo);
	}

	$num_login = mysqli_num_rows($result_login);
	$info_login = mysqli_fetch_assoc($result_login);
	
	if($num_login==0){
		$risposta = array('check'=>'inesistente');	
		$rispo=json_encode($risposta);
		die($rispo);
	}
	else{
		
		if($info_login['stato'] > 0){
			
			$ip_client = get_client_ip_server();
			
			$query_insert_log = "INSERT INTO `users_log`(`utente`,`data`,`ip`) values($info_login[id],now(),'$ip_client') ";
			$result_insert_log =mysqli_query($link,$query_insert_log);
			if($result_insert_log ==false)
			{
			$risposta = array('check'=>'errorlog');	
			$rispo=json_encode($risposta);
			die($rispo);
			}
			
			$id_login = mysqli_insert_id($link);
			
			
			setcookie('timecookie',time() + $ricorda,time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('username',$username,time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('id_login',$id_login,time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('password',md5($password),time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('useridnow',$info_login['id'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('nome',$info_login['nome'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('livello',$info_login['livello'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('livello_inf',$info_login['livello_inf'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('email',$info_login['email'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('contenuti',$info_login['contenuti'],time() + $ricorda,PATH_COOKIE_ADMIN);
			setcookie('dashid',$info_login['id'],time() + $ricorda,PATH_COOKIE);
			
			if(FILEMANAGER){
				
				setcookie('timecookie',time() + $ricorda,time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('username',$username,time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('id_login',$id_login,time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('password',md5($password),time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('useridnow',$info_login['id'],time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('nome',$info_login['nome'],time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('livello',$info_login['livello'],time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('livello_inf',$info_login['livello_inf'],time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				setcookie('email',$info_login['email'],time() + $ricorda,PATH_COOKIE_FILEMANAGER);
				//setcookie('dashid',$info_login['id'],time() + $ricorda,PATH_COOKIE);
				
			}
			
			$risposta = array('check'=>'ok');	
			$rispo=json_encode($risposta);
			die($rispo);
			
		}else{
			
			$risposta = array('check'=>'sospeso');	
			$rispo=json_encode($risposta);
			die($rispo);
			
		}
		
	}
	
	
}else{
	
	$risposta = array('check'=>'isset');	
	$rispo=json_encode($risposta);
	die($rispo);
	
}

?>
