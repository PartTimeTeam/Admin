<?php
/**
 * Check input data
 * Class Name:  Validate_Check
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 16, 2009
 * @Version V001 Jul 16, 2009 (hoangpm) New Create
 */
class My_Check
{
    // XML full path
    protected $_xml;

    // Return error array
    protected $_error = array();
    protected $_data;
    protected $_orginal_data;

    public function __construct()
    {

    }

    /**
     * Check input, filter data and return error messages
     * Function Name: check
     * Programmer: hoangpm (GCS)
     * Create Date: Jul 17, 2009
     *
     * @param   string $xml The full xml path to check data
     * @param   array $data The data array need to check input
     * @return  array  array of the error messages
     * @Version V001 Jul 17, 2009 (hoangpm) New Create
     */

    public function check($xml, array $data)
    {
        if(is_file($xml) == false)
        {
            throw new Exception('The xml file is not found - '.$xml);
        }

        $this->_xml = $xml;
        $this->_orginal_data = $data;

        $xmlObj = new Zend_Config_Xml( $this->_xml );
        $xmlObj = $xmlObj->toArray();

        foreach($xmlObj as $key => $condition)
        {
            // Getting data to be filtered and validated
            if(isset($data[$key])){
                $value = $data[$key];
            }

            if(isset($condition['filter']['pre']['item'])){
                $preFilters = $condition['filter']['pre']['item'];
            }
            if(isset($condition['filter']['post']['item'])){
                $postFilters = $condition['filter']['post']['item'];
            }
            if(isset($condition['validate']['item'])){
                $rules = $condition['validate']['item'];
            }

            // Incase only 1 class check condition
            if(empty($rules[0]) == true)
            {
                $temp = $rules;
                unset($rules);
                $rules[0] = $temp;
            }

            //Filter input if pre-filter existed
            if(isset($preFilters) && isset($value))
            {
                $value = $this->_filter($value, $preFilters);
            }

            // Check error
            $error = $this->_validate($value, $rules);

            // Filter data before return
            if(isset($data[$key])){
                $value = $data[$key];
            }
            if(isset($postFilters) && isset($value))
            {
                $value = $this->_filter($value, $postFilters);
            }

            if(mb_strlen($error) > 0)
            {
                $this->_error[$key] = $error;
            }
            $this->_data[$key]  = $value;
        }

        return $this->_error;
    }

    /**
     * Check the input data has error
     * Function Name: hasError
     * Programmer: hoangpm (GCS)
     * Create Date: Jul 17, 2009
     *
     * @param   void
     * @return  boolean  Return TRUE if input data has error
     * @Version V001 Jul 17, 2009 (hoangpm) New Create
     */
    public function hasError()
    {
        if(empty($this->_error) == true)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the filtered data array
     * Function Name: getData
     * Programmer: hoangpm (GCS)
     * Create Date: Jul 17, 2009
     *
     * @param  	void
     * @return 	array  array of the filtered input data
     * @Version V001 Jul 17, 2009 (hoangpm) New Create
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get the error array
     * Function Name: getError
     * Programmer: hoangpm (GCS)
     * Create Date: Jul 17, 2009
     *
     * @param   void
     * @return  array  array of the error messages
     * @Version V001 Jul 17, 2009 (hoangpm) New Create
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Get the error and filtered data array
     * Function Name: getError
     * Programmer: hoangpm (GCS)
     * Create Date: Jul 17, 2009
     *
     * @param   void
     * @return  array  array of the error messages
     * @Version V001 Jul 17, 2009 (hoangpm) New Create
     */
    public function getAll()
    {
        $return = array(
                'data'  => $this->_data,
                'error' => $this->_error
        );

        return $return;
    }

    protected function _filter($value, $rules)
    {
        $filterChain = new Zend_Filter;
        foreach($rules as $class)
        {
            if(is_array($class) == true)
            {
                $class = $class['class'];
            }

            $value = $filterChain->filterStatic($value, $class);;
        }
        return $value;
    }

    protected function _validate($value, $rules)
    {
        $validateChain = new Zend_Validate();
        foreach($rules as $rule)
        {
            $class      = $rule['class'];
            $message    = $rule['message'];
            if(isset($rule['skipfor'])){
                $skipfor    = $rule['skipfor'];
            }

            $skip_flg   = false;
            if(isset($skipfor) == true && strlen($skipfor) > 0)
            {
                $skips = explode(",", $skipfor);
                foreach( $skips as $skip_key => $skip_value )
                {
                    if( isset($this->_error[$skip_value] ) == true )
                    {
                        $skip_flg = true;
                        break;
                    }
                }
            }

            if( $skip_flg == true )
            {
                continue;
            }

            unset($rule['class']);
            unset($rule['message']);
            unset($rule['skipfor']);

            switch ($class)
            {
                case 'InArray':
                    $rule['haystack'] = explode(',', $rule['haystack']);
                    break;
                case 'GreaterThan':
                    $min = $rule['min'];
                    $min = $this->_getDateTime($min);
                    if(mb_strlen($min) > 0){
                        $rule['min'] = $min;
                    }
                    break;
                case 'LessThan':
                    $max = $rule['max'];
                    $max = $this->_getDateTime($max);
                    if(mb_strlen($max) > 0){
                        $rule['max'] = $max;
                    }
                    break;
                case 'Between':
                    $min = $rule['min'];
                    $min = $this->_getDateTime($min);
                    if(mb_strlen($min) > 0){
                        $rule['min'] = $min;
                    }
                    $max = $rule['max'];
                    $max = $this->_getDateTime($max);
                    if(mb_strlen($max) > 0){
                        $rule['max'] = $max;
                    }
                    break;
            }

            $check = $validateChain->is($value, $class, $rule);

            if($check == false)
            {
                return $message;
            }
        }
        return null;
    }

    private function _getDateTime($key)
    {
        if(strtolower($key) == 'date'){
            return date('Y-m-d');
        }else if(strtolower($key) == 'now'){
            return date('Y-m-d H:i:s');
        }else if(strtolower($key) == 'time'){
            return date('H:i:s');
        }

        return null;
    }
}