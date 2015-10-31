<?php
/**
 * Common functions
 * @author Phuong Nguyen
 *
 */
class Commons {
    
	private static $CURRENCY = array( 1 =>'USD', 2=>'VND' );
	private static $STATUS_ORDER_PAYMENT = 	array( 1=> 'SUCCESS',2 => 'PENDING', 3 => 'FAIL' );
	public static function currencyList(){
		return self::$CURRENCY;
	}
	public static function statusOrderPayment(){
		return self::$STATUS_ORDER_PAYMENT;
	}
	
}
