<?php
/**
 * Common functions
 * @author Phuong Nguyen
 *
 */
class Commons {
    
	private static $CURRENCY = array( 1 =>'USD', 2=>'VND' );
	private static $STATUS_ORDER_PAYMENT = 	array( 1=> 'SUCCESS',2 => 'PENDING', 3 => 'FAIL' );
	private static $GENDER = array( 0 => "MALE", 1 => 'FEMALE');
	private static $TYPE_QUESTION = array('TEXT', 'QR CODE' );
	
	public static function typeQuestion(){
		return self::$TYPE_QUESTION;
	}
	public static function getGender(){
		return self::$GENDER;
	}
	
	public static function currencyList(){
		return self::$CURRENCY;
	}
	public static function statusOrderPayment(){
		return self::$STATUS_ORDER_PAYMENT;
	}
	
}
