<?php
/**
 * Filter utilities
 * @author TuanAnh
 *
 */
class UtilFilter {
    /**
     * Format to float
     * @param string $data
     * @return string
     */
    public static function Float($data) {
        if ( isset( $data ) == true && strlen( $data ) > 0 ) {
            $data = str_replace( ",", "", $data );
    	    $data = floatval( $data );
    	    $data = number_format( $data, 2, ".", "" );
        }
		return $data;
    }
    public static function TransformUpper( $data ){
    	return strtoupper($data);
    }
}
