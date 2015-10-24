<?php
class My_Controller_Action extends Zend_Controller_Action {
    var $N = 20;
    protected static $_infoLogin = null;
    protected static $_lang = null;
    public function init() {
        // Login system
        $namespace = Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
        $auth = Zend_Auth::getInstance ();
        $auth->setStorage ( new Zend_Auth_Storage_Session ( $namespace, 'admin' ) );
        if (isset ( $auth->getIdentity ()->is_authorized ) == TRUE) {
            self::$_infoLogin = $auth->getIdentity ();
        }

        $mixData = $this->_getAllParams ();
        $this->_helper->session->setSession ( 'ISADMIN', 1 );
        if (empty ( self::$_infoLogin ) == TRUE) {
            $this->_redirect ( 'adminbk/auth/login' );
        }

        $this->_helper->session->setSession ( 'TIME_LOGING', time () );
        $this->_helper->acl->allow ();
        $this->language = Zend_Registry::get ( 'language' );
        $this->view->language = $this->language->getMessages ();
        self::$_lang = $this->language->getMessages ();
        $this->view->isLogin = 1;
        $this->view->isAdmin = $this->_helper->session->getSession ( 'ISADMIN' );

        $this->view->fullName = self::$_infoLogin->first_name . " " . self::$_infoLogin->last_name;
        $this->view->user_type = self::$_infoLogin->user_type;

        $this->view->headScript ()->appendFile ( '/js/admin/config.js', 'text/javascript' );
        $this->_helper->layout->setLayout ( 'admin_bk' );

        // Store previous controller
        $prevController  = $this->_helper->session->getSession ( 'PREVIOUS_CONTROLLER' );
        $currentController = $this->getRequest()->getControllerName();
        if ($prevController != $currentController) {
            // Visited to another controller or just go to this controller
            // Clear session search
            // Set new previous controller by current controller
            $this->_helper->session->setSession ( 'PREVIOUS_CONTROLLER', $currentController );
            $this->_helper->session->unsetSession ( "LISTSEARCH" );
        }
    }

    public function preDispatch() {

    }

    public function postDispatch() {

    }

    public static function getUserInfo() {
        return self::$_infoLogin;
    }

    /**
     * Write log information
     * $action : history action
     * $object : current user data
     * $table_id : affect row id
     * $type : 1 - have before values ; 0 - none
     * $seq : seq
     * //$table_index : name of column index in the table
     */
    public static function updateHistory($action, $object, $data, $table_id, $type, $seq = "") {
        $table_index = "seq";
        if ((! isset ( $seq )) || ($seq == "")) {
            $seq = "seq = ";
        } else {
            $table_index = $seq;
            $seq = $seq . " = ";
        }

        $where = $seq . "'" . $table_id . "'";

        // check if this history record has beforedata or not
        if ($type == 1) { // history has before data
            // update case
            $select = $object->select ()->where ( $where );
            $result = $object->fetchRow ( $select )->toArray ();
            $beforeValues = json_encode ( $result );
            // merge with posted data
            $data = array_merge ( $result, $data );

        } else if ($type == 2) {
            // delete case
            $select = $object->select ()->where ( $where );
            $result = $object->fetchRow ( $select )->toArray ();

            $beforeValues = json_encode ( $result );
        } else {
            // insert case
            $beforeValues = "[]";
        }

        // current datetime assign
        foreach ( $data as &$value ) {
            if (( string ) $value == "NOW()") {
                $value = date ( "Y.m.d H:i:s" );
            }
        }
        // get updated value
        $afterValues = json_encode ( $data );

        // perform to save history
        $history = new History ();
        $inserted = $history->update_history ( array ("user_id" => Zend_Auth::getInstance ()->getIdentity ()->seq, "action" => $action, "table_name" => $object->info ( 'name' ), "table_id" => $table_id, "table_index" => $table_index, "datetime" => new Zend_Db_Expr ( 'NOW()' ), "beforecontent" => $beforeValues, "aftercontent" => $afterValues, "state" => "0" ) );
        return $inserted;
    }

    /**
     * parse xml data file to array
     *
     * @param $filename string
     *       	 path of xml file
     * @return array
     */
    public static function xmlParser($filename) {
        if (is_file ( $filename )) {
            $settings = array ();
            $xml = simplexml_load_file ( $filename );
            foreach ( $xml->children () as $child ) {
                $type = strtolower ( $child->getName () );
                // hack to convert to array
                $attrs = ( array ) $child->attributes ();
                if (isset ( $attrs ['@attributes'] )) {
                    $attrs = $attrs ['@attributes'];
                }
                $attrs ['type'] = $type;
                if ($type == 'select') {
                    $options = array ();
                    if ($child->options->option) {
                        foreach ( $child->options->option as $opt ) {
                            $options [( string ) $opt ['value']] = ( string ) $opt ['display'];
                        }
                    }
                    $attrs ['options'] = $options;
                }
                $settings [] = $attrs;
            }
            return $settings;
        }
        return array ();
    }

    public static function emailExisted($email) {
        $userObj = new Model_Users ();
        return ($userObj->emailExisted ( $email ) == NULL ? false : true);
    }

    public static function generatePassword($length = 5, $prefix = '', $subfix = '') {
        $password = '';
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for($i = 0; $i < $length; $i ++) {
            $password .= $string [mt_rand ( 0, strlen ( $string ) - 1 )];
        }
        return ($prefix . $password . $subfix);
    }

    public static function generateCaptcha($fontSize = 25, $wordLen = 6, $width = 200, $height = 80) {
        $captcha = new Zend_Captcha_Image ();
        $captcha->setTimeout ( '300' )->setWordLen ( $wordLen )->setFontSize ( $fontSize )->setHeight ( $height )->setWidth ( $width )->setFont ( 'fonts/VeraBd.ttf' )->setImgDir ( 'assets/captcha' );
        $captcha->generate (); // command to generate session + create image
        return $captcha->getId (); // returns the ID given to session &amp; image
        // http://blog.sankhomallik.com/2008/12/17/tutorial-using-zend_captcha_image/
    }

    public static function validateCaptcha($captcha_input, $captcha_id) {
        $captchaId = $captcha_id;
        $captchaInput = $captcha_input;
        $captchaSession = new Zend_Session_Namespace ( 'Zend_Form_Captcha_' . $captchaId );
        $captchaIterator = $captchaSession->getIterator ();

        if (isset ( $captchaIterator ["word"] ) == TRUE) {
            $captchaWord = $captchaIterator ["word"];
            if ($captchaWord) {
                if ($captchaInput == $captchaWord) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendMail($toEmail, $subject, $messages, $fromEmail = '', $fromName = '') {
        $mailManager = My_Controller_Base::getSetting ( 'webmaster_email' );
        $mailMethod = My_Controller_Base::getSetting ( 'mailmethod' );

        $configs = Zend_Registry::get ( 'configuration' );
        $mailConfig = $configs->get ( 'mail' );

        if (! $mailManager) {
            $mailManager = $mailConfig->get ( 'master' );
        }

        if (! $fromEmail) {
            $fromEmail = $mailManager;
        }
        if (! $fromName) {
            $fromName = 'Web Master';
        }

        $mailAuth = $mailConfig->get ( 'auth' );

        switch ($mailMethod) {
            case 'smtp' :
                $mailServer = My_Controller_Base::getSetting ( 'smtphost' );
                $mailServer = ($mailServer ? $mailServer : $configs->get ( 'server' ));
                $mailUser = My_Controller_Base::getSetting ( 'smtpuser' );
                $mailUser = ($mailUser ? $mailUser : $mailAuth->get ( 'username' ));
                $mailPass = My_Controller_Base::getSetting ( 'smtppassword' );
                $mailPass = ($mailMethod ? $mailPass : $mailAuth->get ( 'password' ));
                $mailPort = My_Controller_Base::getSetting ( 'smtpport' );
                $mailPort = ($mailPort ? $mailPort : $mailAuth->get ( 'port' ));

                $tr = new Zend_Mail_Transport_Smtp ( "{$mailServer}", array ('auth' => 'login', 'username' => "{$mailUser}", 'password' => "{$mailPass}", 'port' => $mailPort ) );
                Zend_Mail::setDefaultTransport ( $tr );
                // send mail
                $mail = new Zend_Mail ( 'utf-8' );
                $mail->setFrom ( $fromEmail, $fromName );
                $mail->setBodyHtml ( $messages );
                $mail->addTo ( $toEmail );
                $mail->setSubject ( $subject );
                $mail->send ();
                break;

            default :
                mail ( $toEmail, $subject, $messages );
                break;
        }
    }

    /**
     * upload file
     *
     * @param $name string
     *       	 name of control
     * @param $folder string
     *       	 folder to upload file
     * @return filename as success otherwise null
     */
    public static function uploading($name, $folder) {
        $filename = '';
        $option = ( int ) My_Controller_Base::getSetting ( 'rename_upload', 1 );

        if ((($_FILES [$name] ["type"] == "image/gif") || ($_FILES [$name] ["type"] == "image/jpeg") || ($_FILES [$name] ["type"] == "image/png") || ($_FILES [$name] ["type"] == "image/pjpeg"))) {
            if ($_FILES [$name] ["error"] <= 0) {
                $fname = $_FILES [$name] ["name"];
                $ext = end ( explode ( '.', $fname ) );
                if ($option == 1) {
                    $fname = date ( 'Ymd-Hms' ) . '.' . $ext;
                }
                move_uploaded_file ( $_FILES [$name] ["tmp_name"], ASSETS_PATH . DS . $folder . DS . $fname );
                $filename = $fname;
            }
        }
        return $filename;
    }
}