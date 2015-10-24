<?php
class Site_CommonController extends FrontBaseAction {
	public function init() {
		$this->setNoLoginRequired();
		//call init of parent
		parent::init();
	}
	/**
	 * Change language action
	 */
	public function languageAction(){
		$langCode = trim( $this->getRequest()->getParam( "langCode" ) );
		$this->_helper->session->setSession( "LANG_CODE", $langCode );
		$returnData[ 'Code' ] = CODE_SUCCESS;
		$this->_helper->json( $returnData );
		exit;
	}
}