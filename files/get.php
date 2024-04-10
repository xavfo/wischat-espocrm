<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
require('api-client.php');
/*
DEBUG
foreach ($_POST as $key => $value) {
      error_log($key,0);
        foreach($value as $k => $v)
              {
                error_log($k,0);
                error_log($v,0);
                error_log('
',0);
    }
} */
function parse_ini ( $filepath ) {
    $ini = file( $filepath );
    if ( count( $ini ) == 0 ) { return array(); }
    $sections = array();
    $values = array();
    $globals = array();
    $i = 0;
    foreach( $ini as $line ){
        $line = trim( $line );
        if ( $line == '' || $line[0] == ';' ) { continue; }
        if ( $line[0] == '[' ) {
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
//$data = json_decode($_POST['message']['data'],true);
//$idwhatsapp = $data['id']['id'];
$idwhatsapp =$_POST['message_muid'];
$stores = parse_ini('data/config.php');
error_log(url_part(),0);
// $client = new EspoApiClient("http://127.0.0.1/Lyon/");
$client = new EspoApiClient(url_part());
$search = array("'",",");
if (str_replace($search,"",trim($stores["'wischatApiKey'"]))!='') {
$client->setApiKey(str_replace($search,"",trim($stores["'wischatApiKey'"])));
}

//Captura de errores
error_log(" qmedic ".print_r($_POST,true),0);

if ($_POST['message_type']!='ACK') {
	$direccion = ($_POST['message_dir'] == 'in') ? 'i' : 'O';

//$lead = $client->request('GET', 'Contact?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.trim($_POST['message']['cuid']));
$lead = $client->request('GET', 'Contact?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=whatsapp&where[1][value]='.trim(substr($_POST['message_from'],0,12)));
// Validar si existe Lead con éste número
if($lead['total']>=1){
        $log =  "existe ".$lead['list'][0]['name'];
        //error_log($log, 0);
        $id_lead = $lead['list'][0]['id'];
        $assignedUserId = $lead['list'][0]['assignedUserId'];
        $leadName = $lead['list'][0]['name'];
} else {
    //Buscar si alguien tiene el mismo número de teléfono
    //$phone_lead = $client->request('GET', 'Contact?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=phoneNumber&where[1][value]=0'.trim(substr($_POST['contact']['uid'],-9)));
    $phone_lead = $client->request('GET', 'Contact?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=phoneNumber&where[1][value]='.trim(substr($_POST['message_from'],0,12)));
// Actualizar el número de WhatsApp en el campo
    if($phone_lead['total']>=1){
                $leadName = substr($_POST['message_from'],0,12);
                //$leadName = $_POST['contact']['name'];
                $assignedUserId = $phone_lead['list'][0]['assignedUserId'];
                $id_lead = $phone_lead['list'][0]['id'];
                echo "Actualizando Numero de Whatsapp";
                //$response = $client->request('PATCH', 'Contact/'.$phone_lead['list'][0]['id'], ['whatsapp' => trim($_POST['message']['cuid'])]);
                $response = $client->request('PATCH', 'Contact/'.$phone_lead['list'][0]['id'], ['whatsapp' => trim(substr($_POST['message_from'],0,12))]);

} else {
        $leadName = $_POST['message_from'];
        //$leadName = $_POST['contact']['name'];
        if (($leadName=='') || (trim($leadName)=='/  /')) {
                //$leadName = 'desconocido '.$_POST['contact']['uid'];
                $leadName = 'desconocido '.substr($_POST['contact_from'],0,12);

        }
            //Si no tenemos nombre
            if ($direccion != 'i') {
                   $leadName = substr($_POST['message_uid'],0,12);
                   //$leadName = $_POST['message']['cuid'];
        }
            $nuevo->request('POST', 'Contact', [
            'firstName' => $leadName,
            'name' => $leadName,
            'whatsapp' => substr($_POST['message_from'],0,12),
            'phoneNumber' => trim(substr($_POST['message_from'],0,12)),
            'assignedUserId' => '1']);
        error_log("creacion ".print_r($nuevo,true),0);
        $id_lead = $nuevo['id'];
        $leadName = $nuevo['name'];
        $assignedUserId = '1';

	}

}


// Asociar Lead con WA
$client->request('POST', 'Wischat', [
    'name' => $leadName.' - Paciente',
    'description' => $_POST['message_body_text'],
    'direccion' => $direccion,
    'tipo' => $_POST['message_type'],
    'pacienteId' => $id_lead,
    'assignedUserId' => $assignedUserId,
    'idwhatsapp' => $idwhatsapp,
    'respuesta' => 'lead '.$_POST['message_from'],
    'sender' => $_POST['message_sender'],
    'from' => $_POST['message_from'],
    'to' => $_POST['message_to'],
    'contactType' => $_POST['contact_type']]);
    //$client->request('PATCH', 'GruposWhatsapp/'.$_POST['contact']['name'], ['grupoWhatsapp' => $_POST['message']['cuid']]);
} else {
    $wischat = $client->request('GET', 'Wischat?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=idwhatsapp&where[1][value]='.$idwhatsapp);
    //$client->request('PATCH', 'Wischat/'.$wischat['list'][0]['id'], ['respuesta' => $_POST['message']['body']['ack']]);
}
?>
