<?php

/**
 * Base controller for site module
 * @author Phuong nguyen
 *
 */
class FrontBaseAction extends Zend_Controller_Action{

    protected $logger;
    protected $post_data;
    protected $request;
    protected $require_login = true;
    protected $ip_address;
    protected $retData;
    protected $headScript = array();
    protected $autorefresh = null;
    protected $language_id = '';
    protected $login_info = null;
    protected $roles = array();
    protected $controller = '';
    protected $action = '';
    protected $xmlFile = '';
    protected $lastAddedId = '';
    protected $lastAddedEntity = '';
    
    /**
     * Init
     * @see Zend_Controller_Action::init()
     */
    public function init() {
    	header('Content-Type: text/html; charset=utf-8');
        $this->view->useBlackBackground = false;
        $this->view->isMainPage = false;
        $this->logger = Zend_Registry::get( 'logger' );
        $this->language_id = Languages::getLanguageID();
        $post = $this->_getAllParams();
        $post = $this->_helper->Common->myTrim( $post );
        $this->post_data = $post;
        $this->request = $this->getRequest();
        $this->login_info = UtilAuth::getLoginInfo();
        //check require login
        if( $this->require_login == true ) {
            // Not logged in
            if( empty( $this->login_info ) == TRUE ) {
                if( $this->isAjax( false ) == true ) {
                    $this->ajaxResponse( CODE_SESSION_EXPIRED );
                } else {
                    $this->_redirect( '/'. Constains::LOGIN );
                }
            } else {
                $mdlUser = new Users();
                $userInfo = $mdlUser->fetchUserById( $this->login_info->user_id ) ;
                if ( empty( $userInfo ) == TRUE ) {
                    if( $this->isAjax( false ) == true ) {
                        $this->ajaxResponse( CODE_SESSION_EXPIRED );
                    } else {
                         $this->_redirect( '/'. Constains::LOGIN );
                    }
                }
            }
        }
        $this->view->login_flag = 0;
        if( empty( $this->login_info ) == FALSE ) {
            $this->view->login_flag = 1;
            $this->_helper->session->setSession( 'TIME_LOGING', time() );
        }
        $this->view->login_info = $this->login_info;
        //get lang code
        $session = new My_Controller_Action_Helper_Session();
        $langCode = $session->getSession( "LANG_CODE" );
        if( empty( $langCode ) ) {
            $langCode = LANG_DEFAULT;
            $session->setSession( "LANG_CODE", LANG_DEFAULT );
        }
        // Return lang code into view
        $this->view->lang_code = $langCode;
        // Get class suffix for language
        // If language is English, return null
        // If language is not English, get suffix for css class
        $this->view->langClass = '';
        if( $langCode != 'en' ) {
            $this->view->langClass = '-'.$langCode;
        }
        // Get image class suffix for language
        // For all language,  get suffix for css class
        $this->view->langImgClass = '_'.$langCode;
        $this->action = $this->request->getActionName();
        $this->controller = $this->request->getControllerName();
       // $autoLogoutMilisec = AUTO_LOGOUT_MILISEC;
        //check permisson
//         if( empty( $this->login_info ) == FALSE ) {
//             $act = "";
//            // $autoLogoutMilisec = $this->login_info->timeout * 60 * 1000;
//             if( empty( $this->post_data["act"] ) == false ) {
//                 $hasPrivilege = UtilAuth::hasPrivilege( $this->controller, $this->post_data["act"] );
//                 if( $hasPrivilege == false ) {
//                     if( $this->isAjax( false ) == true ) {
//                         $this->ajaxResponse( CODE_PERMISSION_DENIED );
//                     } else {
//                         $this->_redirect( "/" );
//                     }
//                 }
//             }
//         }
        /*
         * For show/hide tab
         */
        
        // Get client IP address
        $this->ip_address = $_SERVER ['REMOTE_ADDR'];
        // Init data for ajax response
        $this->retData["Code"] = CODE_HAS_ERROR;
        $this->retData["Message"] = "";
        $this->retData["Data"] = array();
        $this->retData["retUrl"] = '';
        $this->retData["Request"] = array( "PostData" => $this->post_data );
        //Auto refresh js
        $this->autorefresh = new My_View_Helper_AutoRefreshRewriter();
        // Add common script
        $this->appendHeadScript( array(
            $this->autorefresh->autoRefreshRewriter( '/scripts/site/constants.js' ),
        	$this->autorefresh->autoRefreshRewriter( '/scripts/site/pages-common.js' ),
         ));
        // Get auto logout config
        //fetch roles
        //check permissions
//         $this->view->hasViewPermission = UtilAuth::hasPrivilege( $this->controller, ACTION_VIEW );
//         $this->view->hasAddPermission = UtilAuth::hasPrivilege( $this->controller, ACTION_ADD );
//         $this->view->hasEditPermission = UtilAuth::hasPrivilege( $this->controller, ACTION_EDIT );
//         $this->view->hasDeletePermission = UtilAuth::hasPrivilege( $this->controller, ACTION_DELETE );
        $this->view->headTitle( UtilTranslator::translate( $this->controller ) );

        //Set up Cache User
    }

    public function setXML( $file ) {
        $this->xmlFile = $file;
    }

    public function getXML() {
        return $this->xmlFile;
    }

    /**
     * Check ajax request
     * @param boolean $isRedirect
     * @return boolean
     */
    public function isAjax( $isRedirect = true ) {
        $result = false;
        if( $this->request->isXmlHttpRequest() ) {
            $result = true;
        } else {
            if( $isRedirect == true ) {
                $this->_redirect( '/' );
            } else {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Set no login required
     */
    public function setNoLoginRequired() {
        $this->require_login = false;
    }

    /**
     * Translate
     * @param string $key
     * @param array $params
     */
    public function translate( $key, $params = array() ) {
        return UtilTranslator::translate( $key, $params );
    }

    /**
     * Return ajax response
     * @param int $code
     * @param string $message
     * @param array $data
     * @param string $url
     */
    public function ajaxResponse( $code, $message = '', $data = array(), $url = '' ) {
        $this->retData['Code'] = $code;
        $this->retData['Message'] = $message;
        $this->retData['Data'] = $data;
        $this->retData['Url'] = $url;
        $this->_helper->json( $this->retData );
        exit;
    }

    /**
     * Append file to head script
     * @param string $script
     */
    public function appendHeadScript( $script ) {
        if( is_array( $script ) ) {
            foreach( $script as $value ) {
                $this->headScript[$value] = "'".$value."'";
            }
        } else {
            $this->headScript[$script] = "'".$script."'";
        }
        $this->view->autoHeadScripts = $this->headScript;
    }

    /**
     * Load translation
     * @param string $fileName
     */
    public function loadLanguage( $fileName ) {
        return UtilTranslator::loadTranslator( $fileName );
    }

    /**
     * Get login info
     * @return NULL
     */
    public function getRoles() {
        if( empty( $this->roles ) == true ) {
            $mdlRole = new Role();
            $roles = $mdlRole->fetchRoles();
            $this->roles = $roles;
        }
        return $this->roles;
    }

    /**
     * Load template content
     * @param string $template
     */
    public function loadTemplate( $template ) {
        $content = $this->view->render( $template );
        $this->ajaxResponse( CODE_SUCCESS, '', $content );
    }

    /**
     * Load translation
     * @param mix $fileName
     */
    public function loadJs( $fileName ) {
        if( is_array( $fileName ) == true ) {
            foreach( $fileName as $value ) {
                $this->appendHeadScript( $this->autorefresh->autoRefreshRewriter( '/scripts/site/'.$value.'.js' ), 'text/javascript' );
            }
        } else {
            $this->appendHeadScript( $this->autorefresh->autoRefreshRewriter( '/scripts/site/'.$fileName.'.js' ), 'text/javascript' );
        }
    }

    /**
     * List page by ajax
     */
    public function getList( $data ) {
        $draw = "";
        if( empty( $data["PostData"]["draw"] ) == false ) {
            $draw = $data["PostData"]["draw"];
        }
        $count = 0;
        if( empty( $data["Response"] ) == false && empty( $data["Response"]["Count"] ) == false ) {
            $count = $data["Response"]["Count"];
        }
        $list = array();
        if( empty( $data["Response"] ) == false && empty( $data["Response"]["List"] ) == false ) {
            $list = $data["Response"]["List"];
        }

        /*
         * Check permission
         */
        $edit_permission = false;
        if( $this->view->hasEditPermission ) {
            $edit_permission = true;
        }

        $delete_permission = false;
        if( $this->view->hasDeletePermission ) {
            $delete_permission = true;
        }
        
        $keyExt = '';
        if( $this->view->controller == Constants::PROD_SERVICE_CTRL ) {
            $keyExt = 'edit_s_detail_permission';
            $valExt = false;
            if( UtilAuth::hasPrivilege( Constants::PROD_SERVICE_DETAIL_CTRL, ACTION_ADD ) ) {
                $valExt = true;
            }
        } elseif( $this->view->controller == Constants::TAGS_CTRL ) {
            $keyExt = 'edit_tv_detail_permission';
            $valExt = false;
            if( UtilAuth::hasPrivilege( Constants::TAGS_VALUE_CTRL, ACTION_ADD ) ) {
                $valExt = true;
            }
        } elseif( $this->view->controller == Constants::PROD_CTRL ) {
            $keyExt = 'view_service';
            $valExt = false;
            if( UtilAuth::hasPrivilege( Constants::PROD_SERVICE_CTRL, ACTION_VIEW ) ) {
                $valExt = true;
            }
        } elseif( $this->view->controller == Constants::LOCATION_PREFIX_CTRL ) {
            $keyExt = 'view_permission';
            $valExt = false;
            if( UtilAuth::hasPrivilege( Constants::LOCATION_PREFIX_CTRL, ACTION_VIEW ) ) {
                $valExt = true;
            }
        } elseif( $this->view->controller == Constants::ACCOUNT_CTRL ) {
        	$keyExt = 'view_permission';
        	$valExt = false;
        	if( UtilAuth::hasPrivilege( Constants::ACCOUNT_CTRL, ACTION_VIEW ) ) {
        		$valExt = true;
        	}
        } elseif( $this->view->controller == Constants::EXCLUDE_BILLER_CTRL ) {
        	$keyExt = 'view_permission';
        	$valExt = false;
        	if( UtilAuth::hasPrivilege( Constants::EXCLUDE_BILLER_CTRL, ACTION_VIEW ) ) {
        		$valExt = true;
        	}
    	} elseif( $this->view->controller == Constants::ORDER_CTRL ) {
    	    $keyExt = 'view_permission';
    	    $valExt = false;
    	    if( UtilAuth::hasPrivilege( Constants::ORDER_CTRL, ACTION_VIEW ) ) {
    	        $valExt = true;
    	    }
        }

        if( empty( $list ) == false && is_array( $list ) ) {
            foreach( $list as $key => $val ) {
                $list[$key]["edit_permission"] = $edit_permission;
                $list[$key]["delete_permission"] = $delete_permission;
                if(empty($keyExt) == false) {
                    $list[$key][$keyExt] = $valExt;
                }
            }
        }
        //return data
        $return = array();
        $return['draw'] = $draw;
        $return['recordsTotal'] = $count;
        $return['recordsFiltered'] = $count;
        $return['data'] = $list;

        return $return;
    }

    /**
     * Check view permission
     * @param string $url
     */
    public function hasViewPermission( $url = "/" ) {
        if( $this->view->hasViewPermission == false ) {
            if( $this->isAjax( false ) == true ) {
                $this->ajaxResponse( CODE_PERMISSION_DENIED );
            } else {
                $this->_redirect( $url );
            }
        }
    }

    /**
     * @Description: Load Custom Fields
     * @param:
     * @return:
     */
    public function loadCustomFieldAction() {
        // Get Customs fields
        $customerFieldsDefault = array();
        if( empty( $this->post_data["Type"] ) == false ) {
            $input['type'] = $this->post_data["Type"];
            $customerFieldsDefault = CommonService::getCustomFieldList( $input );
        }
		
        $this->view->customerFieldsDefault = $customerFieldsDefault;
        $this->view->customFields = @$this->post_data["CustomFields"];
        $this->loadTemplate( "/partials/_custom-fields.phtml" );
    }
    
    /**
     * Add more custom field
     */
    public function addCustomFieldAction() {
        $this->view->id = uniqid();
        $this->view->value = "";
        $this->view->prefix = @$this->post_data["prefix"];
        $this->view->controlData = json_decode( @$this->post_data["controlData"], true );
        $this->loadTemplate( "/partials/_custom_fields_string_multiple.phtml" );
    }
    public function addNewCustomFieldAction() {
    	$this->view->index = @$this->post_data["index"];
    	$this->view->value = array();
    	$this->view->addNewFlag = true;
    	$this->view->prefix = @$this->post_data["prefix"];
    	$this->view->controlData = json_decode( @$this->post_data["controlData"], true );
    	$this->loadTemplate( "/partials/_custom_fields_mapping_string_multiple.phtml" );
    }
    /**
     * @Description: Get list referral action and turn it into form key=>value
     * @return array $listReferralAction
     * @author: Hoang Tran
     */
    public function listReferralAction() {
        $listReferralAction = array();
        $list = BaseService::getReferralActionList();

        if( empty( $list ) == false ) {
            foreach( $list as $value ) {
                $listReferralAction[$value['Id']] = $value['Name'];
            }
        }

        return $listReferralAction;
    }

    /**
     * @Description: 
     * @param array $data
     * @return array $path
     * @author: Hoang Tran
     */
    public function getPath( $data = array() ) {
        $path = array();
        //Proceeding path
        foreach( $data as $key => $value ) {
            if( empty( $value ) == true ) {
                $path[$key] = 0;
            } else {
                $path[$key] = $value;
            }
        }
        $path = '/'.implode( '/', $path );

        return $path;
    }

    /**
     * @Description: Load Product Type
     * @param:
     * @return:
     */
    public function loadProductTypeAction() {
        $this->view->list = CommonService::getInstance()->getProductTypeList();
        $this->view->type = @$this->post_data['type'];
        $this->loadTemplate( "/partials/_product_type.phtml" );
    }

    public function listCurrencyAction(){
        $this->isAjax();
        $productGroupId = $this->post_data['ProductGroupId'];
        $selectedCurrency = @$this->post_data['SelectedCurrency'];
        $resp = "";
        $productGroupService = new ProductGroupService();
        $productGroupInfo = $productGroupService->getInfo( $productGroupId );
        // get country from product group user select
        $countryCode = @$productGroupInfo['CountryCode'];
        $currency = new CountryCurrencyMapService();
        $data = array('country'=>$countryCode);
        $list = array();
        $listCurrency = $currency->getlistCountryCurrencyByCountry($data);
        if( empty( $listCurrency['List'] ) == false ) {
            $list = $listCurrency['List'];
            $resp = $this->view->partial( "/partials/_currency.phtml", array( "code" => $selectedCurrency, "list" => $list ) );
        } else {
            $resp = '<option value="'.DEFAULT_CURRENCY.'">'.DEFAULT_CURRENCY.' </option>';
        }
        $this->ajaxResponse( CODE_SUCCESS, '', $resp );
        exit;
    }
    
    public function getId() {
        $id = 0;
        if( empty($this->post_data["id"] ) == false ) {
            $id = intval( $this->post_data["id"] );
        }
        return $id;
    }
}
