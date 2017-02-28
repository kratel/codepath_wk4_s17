<?php

const CIPHER_METHOD = 'AES-256-CBC';

function enc_cookie($plaintext) {
	$key = 'a1b2c3d4e5';

	$key = str_pad($key, 32, '*');  //pad the key

	// gen appropriate IV
	$iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
	$iv = openssl_random_pseudo_bytes($iv_length);
	// Encrypt
	$ciphertext = openssl_encrypt($plaintext, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);

	// Return $iv at front of string, need it for decoding
	$message = $iv . $ciphertext;
	  
	// Encode just ensures encrypted characters are viewable/savable
	return $message;
}

function signing_checksum($string) {
    $salt = "Qe23n4J53n09"; // makes process hard to guess
    return hash('sha1', $string . $salt);
}

function sign_string($string) {
    return $string . '--' . signing_checksum($string);
}

$secret = "I have a secret to tell.";
$scrt = enc_cookie($secret); //encrypt the plaintext
$signed = base64_encode(sign_string($scrt)); //sign the ciphertext

setcookie('scrt', $signed);

//see signed cookie and cookie value
if(isset($_COOKIE['scrt'])) {
	print_r($_COOKIE['scrt']);
}

?>