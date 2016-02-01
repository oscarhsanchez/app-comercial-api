<?php

require_once(APPPATH."/libraries/JasperReports/JasperRest.php");
require_once(APPPATH."/libraries/JasperReports/JasperException.php");
require_once(APPPATH."/libraries/JasperReports/JasperHelper.php");

class JasperReports {

    public function getLoginResult(){

        /*
         * IMPORTANTE:
         * ES NECESARIO el archivo jasper_rest_cookies. No tiene extension
         * */

        $paramJReports = array(
            "host" => "192.162.12.50:8080",
            "username" => "jasperadmin",
            "password" => "ahy761q85d",
            "cookie_app_path" => dirname(__FILE__) . '/JasperReports' //directorio donde irá a buscar el archivo jasper_rest_cookies
        );


        $jRest = new JasperRest($paramJReports['host'], $paramJReports['cookie_app_path'].'/');
        $result = $jRest->post(JasperHelper::url("/jasperserver/rest/login") . "?j_username=".$paramJReports['username']."&j_password=".$paramJReports['password']);
        return array(
            'result' => $result,
            'jRest' => $jRest
        );
    }

    public function serializeVarsForQueryString($arr=array()){

        $query = "";
        foreach($arr as $var=>$value){

            if (is_array($value)){
                foreach($value as $keyIn=>$valueIn){
                    $query .= $query == '' ? '?' : '&';
                    $query .= $var . '=' . $valueIn;
                }
            }else{
                $query .= $query == '' ? '?' : '&';
                $query .= $var . '=' . $value;
            }
        }
        return $query;
    }


    public function getReportDocument($pJRest, $type, $params, $data){

        if ($pJRest){
            $jRest = $pJRest;
        }else{
            $loginResult = $this->getLoginResult();
            $jRest = $loginResult['jRest'];
            $result = $loginResult['result'];
            if (!$result['header']['http_code'] == 200) die('no logeado');
        }

        $params = $this->prepareAutocompleteFields($params);
        $formParameters = $this->serializeVarsForQueryString($params);

        //echo JasperHelper::url("/jasperserver/rest_v2/reports".$data['reportRoute']."/".$data['reportId'].".".$type.$formParameters);exit;
        $result = $jRest->get(JasperHelper::url("/jasperserver/rest_v2/reports".$data['reportRoute']."/".$data['reportId'].".".$type.$formParameters));

        return $result;
    }

    public function sendReportMailAction($entidad, $documento, $tipoDoc, $params, $mails){

        $doc = null;

        $pk = null;
        $pk_name = null;
        $subject = "";

        $data = array('reportId' => null, 'reportRoute' => null, 'report' => null);

        switch ($tipoDoc) {
            case 'factura':
                $subject = "Factura " . $documento;
                $data['reportId'] = $entidad->jasper_informe_factura_id;
                $data['reportRoute'] = $entidad->jasper_informe_factura_ruta;
                break;
            case 'albaran':
                $subject = "Albarán " . $documento;
                $data['reportId'] = $entidad->jasper_informe_albaran_id;
                $data['reportRoute'] = $entidad->jasper_informe_albaran_ruta;
                break;
            case 'pedido':
                $subject = "Pedido " . $documento;
                $data['reportId'] = $entidad->jasper_informe_pedido_id;
                $data['reportRoute'] = $entidad->jasper_informe_pedido_ruta;
                break;
            case 'presupuesto':
                $subject = "Presupuesto " . $documento;
                $data['reportId'] = $entidad->jasper_informe_presupuesto_id;
                $data['reportRoute'] = $entidad->jasper_informe_presupuesto_ruta;
                break;
            case 'pedidoproveedor':
                $subject = "Pedido a proveedor " . $documento;
                $data['reportId'] = $entidad->jasper_informe_pedidoproveedor_id;
                $data['reportRoute'] = $entidad->jasper_informe_pedidoproveedor_ruta;
                break;
        }

        $mails = explode(",", $mails);

        foreach ($mails as $key => $mail){
            if (empty($mail)) unset($mails[$key]);
        }

        $loginResult = $this->getLoginResult();
        $jRest = $loginResult['jRest'];
        $result = $loginResult['result'];

        $result = $this->getReportDocument($jRest, 'pdf', $params, $data);
        $pdf = $result['body'];
        //Guardamo el archivo para poder enviarlo
        $file = dirname(__FILE__) . '/JasperReports/tmp/' .  str_replace ("/", "_", $documento) . '.pdf';
        //file_put_contents($file, $pdf);
        $fp = fopen($file, 'w');
        fwrite($fp, $pdf);
        fclose($fp);

        foreach ($mails as $email) {
           sendMailWithFile($subject, $entidad->texto_por_defecto_mal, $email, $file);
        }

        unlink($file);

        return true;

    }

    public function prepareAutocompleteFields($post=array()){

        //Esta funcion es para que en los casos de los campos proveedor / cliente / articulo, en los que siempre trae valores multiples, transformemos los valores separados por comas, en un array

        $fields = array("proveedor", "cliente", "articulo");

        foreach($fields as $f){
            $arr = array();
            if (array_key_exists($f, $post)){
                if (is_string($post[$f])) {
                    $arr = explode(",", $post[$f]);
                    foreach ($arr as $key => $a) {
                        if (trim($a) == '') unset($arr[$key]);
                    }
                    $post[$f] = array_values($arr);
                }
            }
        }

        return $post;
    }

}

