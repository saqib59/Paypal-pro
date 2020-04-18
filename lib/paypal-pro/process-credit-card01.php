<?php

function user_credientials($account_num, $country_code,$expiry_date,$cvv_no){

	// Include config file
	require_once('includes/config.php');
	// Store request params in an array
	 $request_params = array(
		'METHOD'        => 'DoDirectPayment',
		'USER'          => $api_username,
		'PWD'           => $api_password,
		'SIGNATURE'     => $api_signature,
		'VERSION'       => $api_version,
		'PAYMENTACTION' => 'Sale',
		'IPADDRESS'     => $_SERVER['REMOTE_ADDR'],
		'ACCT'          => $account_num,
		'EXPDATE'       => $expiry_date,
		'CVV2'          => $cvv_no,
		'COUNTRYCODE'   => $country_code,
		'AMT'           => '2250',
		'CURRENCYCODE'  => 'USD',
		'DESC'          => 'We are here for learning'

	);


	// Loop through $request_params array to generate the NVP string.
	$nvp_string = '';
	foreach($request_params as $var=>$val){
	$nvp_string .= '&'.$var.'='.urlencode($val);	

	}

    //var_dump($request_params[AMT]);


	// Send NVP string to PayPal and store response
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_URL, $api_endpoint);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);
	$result = curl_exec($curl);
	curl_close($curl);
	// Parse the API response
	return $result_array = NVPToArray($result);
}












