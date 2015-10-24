<?php
class LoginService extends BaseService{
	public function Login( $input = array() ){
		$errors = '';
		$response = array();
		$response = LoginUtils::getInstance()->userLogin( $input );
		if( $this->checkResponse( $response ) == false ) {
			$errors = $response['message'];
		}
		return $this->serviceResponse( $input, $response, $errors );
	}
}