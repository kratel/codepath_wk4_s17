<?php
const CIPHER_METHOD = 'AES-256-CBC';

function signing_checksum($string) {
    $salt = "Qe23n4J53n09"; // makes process hard to guess
    return hash('sha1', $string . $salt);
}

function signed_string_is_valid($array) {
    
    // if not 2 parts it is malformed or not signed
    if(count($array) != 2) { return false; }

    $new_checksum = signing_checksum($array[0]);
    return ($new_checksum === $array[1]);
}


function dec_cookie($message) {
	$key = 'a1b2c3d4e5';

	// Needs a key of length 32 (256-bit)
	$key = str_pad($key, 32, '*');

	// Base64 decode before decrypting
	$iv_with_ciphertext = $message;
	  
	// Separate initialization vector and encrypted string
	$iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
	$iv = substr($iv_with_ciphertext, 0, $iv_length);
	$ciphertext = substr($iv_with_ciphertext, $iv_length);

	// Decrypt
	return openssl_decrypt($ciphertext, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
}

if(isset($_COOKIE['scrt'])){
	//decode base64
	$signed = base64_decode($_COOKIE['scrt']);

	//break cookie val into two parts
	$array = explode('--', $signed);

	if(signed_string_is_valid($array)){
		$plaintext = dec_cookie($array[0]);
		echo $plaintext;
	} else {
		echo "Error: No Secret";
	}

} else {
	echo "Error: No Secret";
}

?>