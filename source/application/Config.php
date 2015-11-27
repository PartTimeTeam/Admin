<?php
//-------------COMMON FOR ALL WEBSITE----------------//
define('LANG_DEFAULT', 'en');
define('SITE_FRONT', 'SITE_FRONT');
define('AUTO_LOGOUT_MILISEC', 2700000);
define('LOGIN_REMEMBER_TIME', 604800);
define('MIN_PASS_DEFAUL', 100000);
define('MAX_PASS_DEFAUL', 999999);
define('ACTION_VIEW', 'view');
define('ACTION_ADD', 'add');
define('ACTION_EDIT', 'edit');
define('ACTION_DELETE', 'delete');
define('LOGIN_INFO', 'LOGIN_INFO');

//-------------COMMON ERROR CODE----------------//
define('CODE_SUCCESS', 1);
define('CODE_NO_ERROR', 0);
define('CODE_HAS_ERROR', -1);
define('CODE_REDIRECT', -300);
define('CODE_SESSION_EXPIRED', -999);
define('CODE_PERMISSION_DENIED', -998);
//------------ALLOWED IMAGE EXTENSION LIST----------//
define('ALLOWED_IMAGE_EXTENSION_LIST', 'jpg,jpeg,png');
define('ALLOWED_EXCEL_EXTENSION_LIST', 'xls,xlsx');
define('MAX_SIZE_IMAGE_ALLOWED', '1MB');
//------------USER----------//
define('USER_STATUS_AVAILABLE', 0);
define('USER_STATUS_DELETED', 1);
define('USER_STATUS_DISABLED', 2);
define('CACHE_ENABLED', 0);
define('CACHE_DISABLED', 1);
//------------TAGSTYPE-----------//
//-------------LOG----------------//
define('LOG_ENABLE', false);
define('LOG_RESULT_FILE', false);
define('LOG_TIME_EXHAUST', 0);
define('LOG_ACTION_VIEW', 'VIEW');
define('LOG_ACTION_GET_INFO', 'GET_INFO');
define('LOG_ACTION_ADD', 'ADD');
define('LOG_ACTION_EDIT', 'EDIT');
define('LOG_ACTION_DELETE', 'DELETE');
define('LOG_ACTION_ADD_IMAGE', 'ADD_IMAGE');
define('LOG_ACTION_ADD_ZIP_FILE', 'ADD_ZIP_FILE');
define('LOG_ACTION_DELETE_IMAGE', 'DELETE_IMAGE');
define('LOG_ACTION_SYNC', 'SYNC');
define('LOG_ACTION_SYNC_ALL', 'SYNC_ALL');

define('LOG_ACTION_ADD_TAGS', 'ADD_TAGS');
define('LOG_ACTION_DELETE_TAGS', 'DELETE_TAGS');
define('LOG_ACTION_VOID', 'VOID');

//CAPTCHAR
define('CAPTCHA_PATH', BASE_PATH . '/data/captcha/');
define('DOMAIN_XRACE', 'http://45.32.248.222:9091/');
define('STATUS_DELETE', 1 );
define('TYPE_IS_IMAGE', 0);
define('TYPE_IS_FILE', 1);
