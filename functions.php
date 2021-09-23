<?php

function check_admin()
{	
	global $link;
	
	
	if(!isset($_COOKIE['username']) || !isset($_COOKIE['useridnow']))
		return false;
		
	$query_admin = "select * from users where username='".mysqli_real_escape_string($link,$_COOKIE['username'])."' and password='".mysqli_real_escape_string($link,$_COOKIE['password'])."' and id ='".mysqli_real_escape_string($link,$_COOKIE['useridnow'])."' and livello ='".mysqli_real_escape_string($link,$_COOKIE['livello'])."'";
	
	
	$check_result = mysqli_query($link,$query_admin);
	
	if($check_result==false)
	{
		send_error($_SERVER["SCRIPT_NAME"],$query_admin,mysqli_error($link));
		echo "<div class=\"ui-state-error\">Errore durante la verifica di autenticit&agrave.<br />Una segnalazione automatica &egrave; gi&agrave; stata inviata agli sviluppatori.</div>";
		return false;	
	}	
				
	if(mysqli_num_rows($check_result)!=1)
	{
		mysqli_free_result($check_result);
		return false;	
	}  	
	mysqli_free_result($check_result);
	return true;
}

function union_killer($query, $attivo, $equals)
{	
//protegge la query da injection, meglio cmq usare i prepared statements in futuro
	if($attivo>0){
		if(preg_match("/union/i", $query) || preg_match("/xor/i", $query) || (substr_count($query,'=') != $equals)){
			die("operazione non autorizzata</body></html>");	
		}	
	}

}

?>
