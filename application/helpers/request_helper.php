<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require '/var/www/vendor/autoload.php';
use Automattic\WooCommerce\Client;
use Aws\Sqs\SqsClient;

if(!function_exists('utcToIst')){
	function utcToIst($time){
	try{
        $date = new DateTime($time);
        $date->setTimezone(new DateTimeZone("Asia/Kolkata"));
	return $date->format('Y-m-d H:i:s');
	}catch(Exception $e){
		return false;
	}

    }
}