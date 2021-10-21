<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
require('api-client.php');
foreach ($_POST as $key => $value) {
	  error_log($key,0);
	    foreach($value as $k => $v)
		      {
			        error_log($k,0);
				error_log($v,0);
				error_log('
',0);
				    }

} 
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
$idwhatsapp = $data['id']['id'];
$stores = parse_ini('data/config.php');	
error_log(url_part(),0);
$client = new EspoApiClient("http://127.0.0.1/Lyon/");
// $client = new EspoApiClient(url_part());
$search = array("'",",");
if (str_replace($search,"",trim($stores["'wischatApiKey'"]))!='') {
$client->setApiKey(str_replace($search,"",trim($stores["'wischatApiKey'"])));
}

if ($_POST['message']['type']!='ACK') {
// if (str_replace($search,"",trim($stores["'wischatSecretKey'"]))!='') {
// $client->setSecretKey(str_replace($search,"",trim($stores["'wischatSecretKey'"])));
// }
// Arreglar direcciÃ³n de entrada o salida de mensajes
	if ($_POST['contact']['uid'] != $_POST['message']['cuid']){
		$_POST['message']['dir'] = 'o';
		}	
	if ($_POST['message']['dir'] == 'o') {
		$direccion = 'O';
	}
	else {
		$direccion = 'i';
	}
$lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.trim($_POST['message']['cuid']));
if($lead['total']>=1){
        $log =  "existe ".$lead['list'][0]['name'];
        error_log($log, 0);
        $id_lead = $lead['list'][0]['id'];
        $assignedUserId = $lead['list'][0]['assignedUserId'];
        $leadName = $lead['list'][0]['name'];
} else {
$phone_lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=phoneNumber&where[1][value]=0'.trim(substr($_POST['contact']['uid'],-9)));
if($phone_lead['total']>=1){
                $leadName = $_POST['contact']['name'];
                $assignedUserId = $phone_lead['list'][0]['assignedUserId'];
        	$id_lead = $phone_lead['list'][0]['id'];
                echo "Actualizando Numero de Whatsapp";
                $response = $client->request('PATCH', 'Lead/'.$phone_lead['list'][0]['id'], ['whatsapp' => "593".trim(substr($_POST['contact']['uid'],-9)), 'phoneNumber' => '0'.trim(substr($_POST['contact']['uid'],-9))]);

} else {
                $leadName = $_POST['contact']['name'];
                if (($leadName=='') || (trim($leadName)=='/  /')) {
                        $leadName = 'desconocido '.$_POST['contact']['uid'];
                }
		if ($direccion != 'i') {
		   $leadName = $_POST['message']['cuid'];
		}	
                $client->request('POST', 'Lead', [
                'firstName' => $leadName,
                'name' => $leadName,
                'whatsapp' => $_POST['message']['cuid'],
                'phoneNumber' => '0'.trim(substr($_POST['contact']['uid'],-9)),
                'assignedUserId' => '1']);
		// error_log("creacion ".print_r($client,true));
}


$lead = $client->request('GET', 'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.trim($_POST['contact']['uid']));
if($lead['total']>=1){
        echo "existe ".$lead['list'][0]['name'];
        $id_lead = $lead['list'][0]['id'];
        $assignedUserId = $lead['list'][0]['assignedUserId'];
	$leadName = $lead['list'][0]['name'];
        $response = $client->request('PATCH', 'Lead/'.$lead['list'][0]['id'], ['whatsapp' => "593".trim(substr($_POST['contact']['uid'],-9)), 'phoneNumber' => '0'.trim(substr($_POST['contact']['uid'],-9))]);
}
}
$url =  'Lead?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.trim($_POST['contact']['uid']);
$lead = $client->request('GET', trim($url));
$id_lead = $lead['list'][0]['id'];
$assignedUserId = $lead['list'][0]['assignedUserId'];

error_log("id lead: ".$url);

$client->request('POST', 'Wischat', [
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
	'contactType' => $_POST['contact']['type']]);
$client->request('PATCH', 'GruposWhatsapp/'.$_POST['contact']['name'], ['grupoWhatsapp' => $_POST['message']['cuid']]);
} else {
$wischat = $client->request('GET', 'Wischat?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=idwhatsapp&where[1][value]='.$idwhatsapp);
$client->request('PATCH', 'Wischat/'.$wischat['list'][0]['id'], ['respuesta' => $_POST['message']['body']['ack']]);
}
?>
