<?php
/**
 * Language model
 * @author TuanAnh
 *
 */
class Languages extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'language_tbl';
    protected $_rowClass 		= 'DbTableRow';
    private static $_language   = null;

    /**
     * Get language by id
     * @param int $id
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function fetchLanguageById($id)
    {
        $db     = $this->getAdapter();
        $where  = $db->quoteInto( "seq = ?", $id, Zend_Db::INT_TYPE );
        $result = $this->fetchRow( $where );
        return $result;
    }

    /**
     * Get language by code
     * @param string $codes
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function fetchLanguageByCodes($codes)
    {
        $db     = $this->getAdapter();
        $where  = $db->quoteInto( "codes = ?", $codes );
        $result = $this->fetchRow( $where );
        return $result;
    }

    /**
     * Get current language id
     * @return Ambigous <string, NULL>
     */
    public static function getLanguageID(){
        $seq = '';
        $lang = self::getLanguage();
        if( $lang ) {
            $seq = $lang['seq'];
        }
        return $seq;
    }

    /**
     * Get current language row
     * @return NULL
     */
    public static function getLanguage(){
        if( self::$_language == null ) {
            $language = new Languages();
            $lang = array();
            $langRow = $language->fetchLanguageByCodes( LANG_CODE );
            if ( empty( $langRow ) == false ) {
                $lang = $langRow->toArray();
            }
            self::$_language = $lang;
        }
        return self::$_language;
    }
}