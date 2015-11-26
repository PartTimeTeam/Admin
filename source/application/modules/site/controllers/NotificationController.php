<?php
class Site_NotificationController extends FrontBaseAction {
    public function init() {
        parent::init();
    }

    public function indexAction() {
    	$game = new Game();
    	$gameInfo = $game->getInfo(1);
    	$this->view->gameInfo = $gameInfo;
    	$data = $this->post_data;
    	if( $this->request->isPost()  ){
    		$checkSuccess = true;
    		if( empty($data['message']) == false ){
    			$url = API_URL.'push_notification_all';
    			$dataPost = array('message_request' => $data['message'], 'code' => 1);
    			$dataEncode = Zend_Json_Encoder::encode( $dataPost );
    			try {
    				$info = RESTClient::getInstance()->postJson( $url, $dataEncode, true );
    			} catch( Exception $exc ) {
    				$checkSuccess == false;
    			}
    			if( $checkSuccess == true ){
    				echo '<script> alert("Push notification success");</script>';
    			}
    		} else {
    			echo '<script> alert("Please Enter Message");</script>';
    		}
    		
    	}
    } 
}