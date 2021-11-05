<?php
namespace Espo\Core\Formula\Functions\StringGroup;

use \Espo\ORM\Entity;
use \Espo\Core\Exceptions\Error;

class SendwhatsappType extends \Espo\Core\Formula\Functions\Base
{
    public function process(\StdClass $item)
    {
// sendwhatsapp(TELEFONO, TEXT, TOKEN, UID, MULTIMEDIA)

        $telefono = $this->evaluate($item->value[0]);
        $mensaje = urlencode(utf8_encode($this->evaluate($item->value[1])));
        $token = $this->evaluate($item->value[2]);
        $uid = $this->evaluate($item->value[3]);

        $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
                CURLOPT_HTTPAUTH       => true,
                CURLAUTH_ANY           => true,
         );

       if (count($item->value) > 4) {
        $attachmentsids = $this->evaluate($item->value[4]);
        $attachmentsnames = $this->evaluate($item->value[5]);
        $adjuntos = '
';

$http=isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
//$part= rtrim($_SERVER['SCRIPT_NAME'],basename($_SERVER['SCRIPT_NAME']));
$domain=$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
$domain=str_replace("/api/v1","",$domain);
$url_path = "$http"."$domain";

foreach ($attachmentsnames as $clave => $valor) {
         $array = explode('.', $valor);
         $extension = end($array);
         $custom_name = substr(uniqid('',true),0,16).'.'.$extension;
symlink(getcwd()."/data/upload/".$clave, getcwd()."/media/".$custom_name);
         $adjuntos = $adjuntos .'
'.$url_path.'/media/'.$custom_name;
         $GLOBALS['log']->alert('Adjuntos : directorio '.getcwd().' '.$custom_name.' => '.$valor);

        }
        $mensaje = $mensaje.urlencode(utf8_encode($adjuntos));
}

        $custom_uid=substr(uniqid('',true),0,16);
        $url="https://wis.chat/w/api/send/chat/?token=".$token."&uid=".$uid."&to=".$telefono."&custom_uid=".$custom_uid."&text=".$mensaje;
        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
            if ($err != '0') {
            return ($err." ".$errmsg);
            } else {
             $GLOBALS['log']->alert('Whatsapp Enviado al telefono :'. $telefono.' '.$id);
            return ($content);
            }
   }
}