# Procedura di login

## Spiegazione

Ho messo i due file che vengono chiamati per effettuare il login `ajax_login.php` e il file contenente tutte le funzioni `functions.php` (chiaramente soltanto con le funzioni interessate)

Quando vengono inviate username e password lo script che si occupa del login `/admin/asynchronous/ajax_login.php`  controlla se vengono settati i valori dei due campi e nel caso va a controllare se esiste l'utente in tabella `//Parte 1`

La `select` sulla tabella viene fatta escapando i caratteri speciali tramite la funzione di php `mysqli_real_escape_string`  `//Parte 2`
Oltre a questo controllo ho creato una funzione chiamata `union_killer` `//Parte 3` che non fa altro che controllare se all'interno della query non compaiano `union` `xor` e conta quanti caretteri `=` ci sono all'interno li confronta con il numero passato nel parametro `$equals` e se diverso fa morire il tutto `die()`

Se viene passato il controllo utente e quindi viene trovato all'interno della tabella l'utente che soddisfa username e password allora vengono settati i vari cookie per essere loggati al sistema. Una volta settati i cookie in ogni script del sistema  viene controllato tramite la funzione `check_admin()` se l'insieme dei cookie contenenti username password e id utente esiste nella tabella.

## Test injection
Tramite il software SqlMap ho fatto partire un test sul link ....admin/asynchronous/ajax_login.php?admin-username='+or+0=0+#&admin-password=test e ho messo il report nel file `sqlmap.doc`

## Report errori/warning dal server
Ho disattivato il display degli errori e log dal server, in modo da non dare ulteriori informazioni sul server.

## Ulteriori integrazioni che potremmo fare se queste non dovessero bastare
Le password sono tutte cifrate in md5 che attualmente non è il miglior algoritmo di cifratura, quindi potremo eventualmente ricreare tutte le password degli utenti criptandole con altri tipi di algoritmi.

Potrei usare, almeno per quanto riguarda gli script di login, le query parametrizzate utilizzando dei template appositi. 

## Codice

```
//Parte 1
if(strcmp('',$username) && strcmp('',$password)){

//Parte 2
$query_login = "SELECT * from users where username='".mysqli_real_escape_string($link,$username)."' and password='".mysqli_real_escape_string($link,md5($password))."' ";


//Parte 3
union_killer($query_login,1,2);
    
}



function union_killer($query, $attivo, $equals)
{	
	if($attivo>0){
		if(preg_match("/union/i", $query) || preg_match("/xor/i", $query) || (substr_count($query,'=') != $equals)){
			die("operazione non autorizzata</body></html>");	
		}	
	}

}

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

```



## Installation

> The installation instructions are low priority in the readme and should come at the bottom. The first part answers all their objections and now that they want to use it, show them how.
