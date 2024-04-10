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

// $data = json_decode($_POST['message']['data'],true);
// $idwhatsapp = $data['id']['id'];
$stores = parse_ini('data/config.php');	
error_log(url_part(),0);
$client = new EspoApiClient("http://localhost/neocrm/");
$search = array("'",",");
if (str_replace($search,"",trim($stores["'wischatApiKey'"]))!='') {
$client->setApiKey(str_replace($search,"",trim($stores["'wischatApiKey'"])));
}

print_r($_GET);
$formulario = $client->request('GET', 'Formulario?where[0][type]=equals&where[0][field]=deleted&where[0][value]=0&where[1][type]=equals&where[1][field]=uniqueId&where[1][value]='.$_GET['unique_id']);
print_r($formulario);
if($formulario['total']>=1){
        $log =  "existe ".$formulario['list'][0]['name'];
        $id = $formulario['list'][0]['id'];
	header('Location: http://197.142.28.125/neocrm/#Formulario/view/'.$id);
	exit;
} else {

$id = $client->request('POST', 'Formulario', [
        'name' => $_GET['unique_id'],
        'assignedUserId' => 1,
        'agentNumber' => $_GET['agent_number'],
        'callID' => $_GET['call_id'],
        'callType' => $_GET['call_type'],
        'campaignID' => $_GET['campaign_id'],
        'phone' => $_GET['phone'],
        'uniqueId' => $_GET['unique_id'],
        'leadId' => $_GET['cliente'],
        'remoteChannel' => $_GET['remote_channel']]);
print_r($id);
	header('Location: http://197.142.28.125/neocrm/#Formulario/view/'.$id['id']);
	exit;
}
?>
