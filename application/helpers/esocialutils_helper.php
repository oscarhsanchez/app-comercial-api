<?php

function rand_string( $length ) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}

function ean13_check_digit($digits){
//first change digits to a string so that we can access individual numbers
    $digits =(string)$digits;
// 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
// 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;
// 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
// 4. Sum the results of steps 2 and 3.
    $total_sum = $even_sum_three + $odd_sum;
// 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
    $next_ten = (ceil($total_sum/10))*10;
    $check_digit = $next_ten - $total_sum;
    return $digits . $check_digit;
}


function rand_string_number( $length ) {

    $chars = "0123456789";
    return substr(str_shuffle($chars),0,$length);

}

function encryptPass($raw, $salt, $iterations, $algorithm, $encodeHashAsBase64) {
    $salted = empty($salt) ? $raw : $raw.'{'.$salt.'}';
    $digest = hash($algorithm, $salted, true);

    // "stretch" hash
    for ($i = 1; $i < $iterations; $i++) {
        $digest = hash($algorithm, $digest.$salted, true);
    }

    return $encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);
}

function bCryptPassword($raw, $salt, $cost)
{
    if (isPasswordTooLong($raw)) {
        throw new BadCredentialsException('Invalid password.');
    }

    $options = array('cost' => $cost);

    if ($salt) {
        $options['salt'] = $salt;
    }

    return password_hash($raw, PASSWORD_BCRYPT, $options);
}

function isPasswordValid($encoded, $raw, $salt)
{
    return !isPasswordTooLong($raw) && password_verify($raw, $encoded);
}

function isPasswordTooLong($password)
{
    return strlen($password) > MAX_PASSWORD_LENGTH;
}

function getToken() {
    return sha1(rand().date('Y-m-d H:i:s').rand());
}

function getTokenFromString($text) {
    $seed = "jsjkdiirieo23$".$text;
    return sha1($seed);
}

function htmlwrap(&$str, $maxLength=76, $char="\r\n"){
    $count = 0;
    $newStr = '';
    $openTag = false;
    $lenstr = strlen($str);
    for($i=0; $i<$lenstr; $i++){
        $newStr .= $str{$i};
        if($str{$i} == '<'){
            $openTag = true;
            continue;
        }
        if(($openTag) && ($str{$i} == '>')){
            $openTag = false;
            continue;
        }
        if(!$openTag){
            if($str{$i} == ' '){
                if ($count == 0) {
                    $newStr = substr($newStr,0, -1);
                    continue;
                } else {
                    $lastspace = $count + 1;
                }
            }
            $count++;
            if($count==$maxLength){
                if ($str{$i+1} != ' ' && $lastspace && ($lastspace < $count)) {
                    $tmp = ($count - $lastspace)* -1;
                    $newStr = substr($newStr,0, $tmp) . $char . substr($newStr,$tmp);
                    $count = $tmp * -1;
                } else {
                    $newStr .= $char;
                    $count = 0;
                }
                $lastspace = 0;
            }
        } 
    }

    return $newStr;
}

/**
 * Guarda un archivo en amazon
 * 
 * @param file Una cadena sin codificar en base64 del archivo
 * @param token Un token/nombre para usarlo en la codificaci�n del nombre final
 * @return string El nombre codificado del fichero
 */
function uploadToAmazon($file, $token) {
	$CI =& get_instance();

	$encodedName = md5(microtime(1) . '_' . $token);
	$CI->s3->putObjectString($file, "efinanzas", $encodedName, S3::ACL_PUBLIC_READ);

	return $encodedName;
}

function flatArray($bidimensionalArray) {
    $return = array();

    if (is_array($bidimensionalArray)) {
        foreach ($bidimensionalArray as $row) {
            $return[] = $row;
        }
    }

    return $return;
}

/**
 * Funciona que envia un mail a la direccion indicada.
 *
 * @param $subject
 * @param $body
 * @param $to
 */
function sendMail($subject, $body, $to) {
    $CI =& get_instance();
    $config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'smtp.1and1.es',
        'smtp_port' => 25,
        'smtp_user' => 'log@rbconsulting.es', // change it to yours
        'smtp_pass' => 'Log2008', // change it to yours
        'mailtype' => 'html',
        'charset' => 'iso-8859-1',
        'wordwrap' => TRUE
    );

    $CI->load->library('email', $config);

    $CI->email->set_newline("\r\n");
    $CI->email->from('log@rbconsulting.es'); // change it to yours
    $CI->email->to($to);
    $CI->email->subject($subject);
    $CI->email->message($body);
    if($CI->email->send())
    {
        echo 'Email sent.';
    }
    else
    {
        show_error($this->email->print_debugger());
    }

}

/**
 * Funciona que envia un mail a la direccion indicada.
 *
 * @param $subject
 * @param $body
 * @param $to
 */
function sendMailWithFile($subject, $body, $to, $file) {
    $CI =& get_instance();
    $config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'smtp.1and1.es',
        'smtp_port' => 25,
        'smtp_user' => 'log@rbconsulting.es', // change it to yours
        'smtp_pass' => 'Log2008', // change it to yours
        'mailtype' => 'html',
        'charset' => 'iso-8859-1',
        'wordwrap' => TRUE
    );

    $CI->load->library('email', $config);

    $CI->email->set_newline("\r\n");
    $CI->email->from('log@rbconsulting.es'); // change it to yours
    $CI->email->to($to);
    $CI->email->subject($subject);
    $CI->email->message($body);
    $CI->email->attach($file);

    if($CI->email->send())
    {
        return true;
    }
    else
    {
        //show_error($this->email->print_debugger());
        return false;
    }
    $CI->email->clear(true);

}

?>