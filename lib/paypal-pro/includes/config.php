<?php
// Set sandbox (test mode) to true/false.
$sandbox = true;

// Set PayPal API version and credentials.
$api_version = '85.0';
$api_endpoint = $sandbox ? 	'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';

//$api_username = $sandbox ? 	'sb-vcqyt819036_api1.business.example.com' : 'http://shearree_api1.aol.com/';
$api_username = $sandbox ? 	'paypal_mail_here_sandbox' : 'paypal_mail_here_live';

//$api_password = $sandbox ? 	'S5U56ZS6FFG5E98G' : 'TFB2RA57S859WYQL';

$api_password = $sandbox ? 	'api_pass_sandbox' : 'api_pass_live';

//$api_signature = $sandbox ? 'AyNYx-G4y3DRk6BjNZm5nEp-FCCDAolED61oC3d6zi6FGaBPwyz4Sg1n' : 'AR6jfZ-xy9rVRwbk5sa6I.SAgPkdAVMtrBJkD53Q2o-Bc02vlsmlfQ3p';

$api_signature = $sandbox ? 'API_SIGNATURE_SANDBOX' : 'API_SIGNATURE_LIVE';

// Function to convert NTP string to an array
function NVPToArray($NVPString)
{
	$proArray = array();
	while(strlen($NVPString))
	{
		// name
		$keypos= strpos($NVPString,'=');
		$keyval = substr($NVPString,0,$keypos);
		// value
		$valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
		$valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
		// decoding the respose
		$proArray[$keyval] = urldecode($valval);
		$NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
	}
	return $proArray;
}