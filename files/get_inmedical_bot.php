<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
require('api-client.php');
$fp = fopen('/var/www/html/disisot.com/log.txt', 'a+');

$resultado = file_get_contents('php://input');
$input = json_decode($resultado,true); 
fwrite($fp, print_r($input,true));
fclose($fp);

if (json_last_error() === JSON_ERROR_NONE) {
$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];
// Set this Verify Token Value on your Facebook App 
if ($verify_token === 'testtoken') {
  echo $challenge;
}
   $input = json_decode($resultado,true); 
    
   $event = $input['entry'][0]['changes'][0]['field'];
// echo $event;
if ($event == 'messages') {
$event = 'message';
}
$token = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
$sender = $input['entry'][0]['changes'][0]['value']['messages'][0]['from'];
$uid = $input['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];
$contact_uid = $input['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
$name = $input['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
$type = $input['entry'][0]['changes'][0]['value']['messages'][0]['type'];
$dtm = $input['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];
$muid = $input['entry'][0]['changes'][0]['value']['messages'][0]['id'];
$from = $input['entry'][0]['changes'][0]['value']['messages'][0]['from'];
if ($type == 'text') {
$type = 'chat';
$message_text = $input['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
}

if ($type == 'interactive') {
$type = 'chat';
$type_button = $input['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type'];
$message_text = $input['entry'][0]['changes'][0]['value']['messages'][0]['interactive'][$type_button]['id'];
}


$_POST['event'] = $event;
$_POST['token'] = $token;
$_POST['uid'] = $uid;
$_POST['contact']['uid'] = $contact_uid;
$_POST['contact']['pic'] = '';
$_POST['contact']['name'] = $name;
$_POST['contact']['type'] = 'user';
$_POST['message']['dtm'] = $dtm;
$_POST['message']['uid'] = $muid;
$_POST['message']['cuid'] = $contact_uid;
$_POST['message']['dir'] = '';
$_POST['message']['type'] = $type;
$_POST['message']['clientUrl'] = '';
$_POST['message']['mediakey'] = '';
$_POST['message']['mimetype'] = '';
$_POST['message']['body']['text'] = $message_text;
$_POST['message']['body']['ack'] = 1;
$_POST['message']['sender'] = $sender;
$_POST['message']['from'] = $from;
$_POST['message']['to'] = $uid;
$_POST['data'] = json_encode($input); 	
   	

}

// print_r($_POST);


fwrite($fp, print_r($_POST,true));
fclose($fp);
foreach ($_POST as $key => $value) {
	  // error_log($key,0);
	    foreach($value as $k => $v)
		      {
			        // error_log($k,0);
				// error_log($v,0);
				// error_log('
// ',0);
				    }

} 

$enlace =  mysqli_connect('localhost','admin','disisot3k30','espo_bussinescenter');
if (!$enlace) {
        // echo 'No pudo conectarse: ' . mysqli_error();
}
mysqli_set_charset($enlace,"utf8mb4");

function parse_ini ( $filepath ) {
    $ini = file( $filepath );
    if ( count( $ini ) == 0 ) { return array(); }
    $sections = array();
    $values = array();
    $globals = array();
    $i = 0;
    foreach( $ini as $line ){
        $line = trim( $line );
        if ( $line == '' || $line{0} == ';' ) { continue; }
        if ( $line{0} == '[' ) {
            $sections[] = substr( $line, 1, -1 );
            $i++;
            continue;
        }
        list( $key, $value ) = explode( '=>', $line, 2 );
        $key = trim( $key );
        $value = trim( $value );
        if ( $i == 0 ) {
            if ( substr( $line, -1, 2 ) == '[]' ) {
                $globals[ $key ][] = $value;
            } else {
                $globals[ $key ] = $value;
            }
        } else {
            if ( substr( $line, -1, 2 ) == '[]' ) {
                $values[ $i - 1 ][ $key ][] = $value;
            } else {
                $values[ $i - 1 ][ $key ] = $value;
            }
        }
    }
    for( $j=0; $j<$i; $j++ ) {
        $result[ $sections[ $j ] ] = $values[ $j ];
    }
    return $globals;
}
function url_part(){
   $http=isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
   $part=rtrim($_SERVER['SCRIPT_NAME'],basename($_SERVER['SCRIPT_NAME']));
   $domain=$_SERVER['SERVER_NAME'];
   return "$http"."$domain"."$part";
}
$data = json_decode($_POST['message']['data'],true);
// $idwhatsapp = $data['id']['id'];

$idwhatsapp = $_POST['message']['uid'];
$stores = parse_ini('data/config.php');	
error_log(url_part(),0);
//$client = new EspoApiClient(url_part());
$client = new EspoApiClient("http://localhost/BusinessCenter/");
$search = array("'",",");
if (str_replace($search,"",trim($stores["'wischatApiKey'"]))!='') {
$client->setApiKey(str_replace($search,"",trim($stores["'wischatApiKey'"])));
}

if ($_POST['message']['type']=='chat') {
// if (str_replace($search,"",trim($stores["'wischatSecretKey'"]))!='') {
// $client->setSecretKey(str_replace($search,"",trim($stores["'wischatSecretKey'"])));
// }
// Arreglar direcciÃ³n de entrada o salida de mensajes
/*	if ($_POST['contact']['uid'] != $_POST['message']['cuid']){
		$_POST['message']['dir'] = 'o';
		}	
	if ($_POST['message']['dir'] == 'o') {
		$direccion = 'O';
	}
	else {
		$direccion = 'i';
	} */
$direccion = 'i';
$lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.$_POST['message']['cuid']);
// error_log($lead, 0);
if($lead['total']>=1){
        $log =  "existe ".$lead['list'][0]['name'];
        error_log($log, 0);
        $id_lead = $lead['list'][0]['id'];
        $menuid = $lead['list'][0]['menuid']; 
        $assignedUserId = $lead['list'][0]['assignedUserId'];
        $leadName = $lead['list'][0]['name'];
} else {
        $phone_lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=phoneNumber&where[1][value]=0'.trim(substr($_POST['contact']['uid'],-9)));
        if($phone_lead['total']>=1){
                echo "Actualizando Numero de Whatsapp";
                $response = $client->request('PATCH', 'Lead/'.$phone_lead['list'][0]['id'], ['whatsapp' => "593".trim(substr($_POST['contact']['uid'],-9))]);
                $menuid = $lead['list'][0]['menuid'];
        } else {
                $leadName = mb_convert_encoding(substr($_POST['contact']['name'],0,50), 'UTF-8', 'UTF-8');
                if (($leadName=='') || (trim($leadName)=='/  /')) {
                        $leadName = 'desconocido '.$_POST['contact']['uid'];
                }
		if ($direccion != 'i') {
		   $leadName = utf8_encode($_POST['message']['cuid']);
		}	
	        $leadName = $leadName . " " . rand();	
                $client->request('POST', 'Lead', [
                'firstName' => $leadName,
                'name' => $leadName,
                'whatsapp' => $_POST['message']['cuid'],
                'phoneNumber' => '0'.trim(substr($_POST['contact']['uid'],-9)),
                'assignedUserId' => '1']);
        }
$lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.$_POST['contact']['uid']);
if($lead['total']>=1){
        echo "existe ".$lead['list'][0]['name'];
        $id_lead = $lead['list'][0]['id'];
        $assignedUserId = $lead['list'][0]['assignedUserId'];
        $leadName = $lead['list'][0]['name'];
        $menuid = $lead['list'][0]['menuid'];
}
}
$idw = $client->request('POST', 'Wischat', [
	'name' => $leadName.' - Lead',	
	'description' => $_POST['message']['body']['text'],
	'direccion' => $direccion,
	'tipo' => $_POST['message']['type'],
	'leadId' => $id_lead,
	'assignedUserId' => $assignedUserId,
	'idwhatsapp' => $idwhatsapp,
	'respuesta' => 'lead '.$_POST['message']['uid'],
	'sender' => $_POST['message']['sender'],
	'from' => $_POST['message']['from'],
	'to' => $_POST['message']['to'],
        'menuid' => $menuid, 
	'contactType' => $_POST['contact']['type']]);

if (($menuid==999)&&($direccion=='i')&&($assignedUserId != "1")) {
$idn = str_replace('.','',substr(uniqid('', true),0,16));
$query = "INSERT INTO `reminder` (`id`, `deleted`, `remind_at`, `start_at`, `type`, `seconds`, `entity_type`, `entity_id`, `user_id`, `is_submitted`) VALUES ('".$idn."','0',now(),now(),'Popup',0,'Wischat','".$idw['id']."','".$assignedUserId."','0')";
// $query = "INSERT INTO `reminder`(`id`, `deleted`, `remind_at`, `start_at`, `type`, `seconds`, `entity_type`, `entity_id`, `user_id`, `is_submitted`) VALUES ('".$idn."','0',now(),now(),'Popup',0,'Wischat','".$idw['id']."','1','0')";
// error_log($query,0);
 $stmt = mysqli_prepare($enlace,$query);
         $rc = mysqli_stmt_execute($stmt);
         if ( false===$rc ) {
                error_log(json_encode(explode(":", htmlspecialchars($stmt->error))),0);
        }
         // echo $query.";
         // ";
         mysqli_stmt_close($stmt);

// $query = "INSERT INTO `subscription` (`deleted`, `entity_id`, `entity_type`, `user_id`) VALUES ('0','".$idw['id']."','Wischat','".$assignedUserId."')";
// error_log($query,0);
 $stmt = mysqli_prepare($enlace,$query);
         $rc = mysqli_stmt_execute($stmt);
         if ( false===$rc ) {
                error_log(json_encode(explode(":", htmlspecialchars($stmt->error))),0);
        }
         // echo $query.";
         // ";
         mysqli_stmt_close($stmt);

}


// $client->request('PATCH', 'GruposWhatsapp/'.$_POST['contact']['name'], ['grupoWhatsapp' => $_POST['message']['cuid']]);
} else {
$wischat = $client->request('GET', 'Wischat?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=idwhatsapp&where[1][value]='.$idwhatsapp);
$client->request('PATCH', 'Wischat/'.$wischat['list'][0]['id'], ['respuesta' => $_POST['message']['body']['ack']]);
}
?>
