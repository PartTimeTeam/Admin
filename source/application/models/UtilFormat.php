<?php
/**
 * Format utilities
 * @author Phuong nguyen
 *
 */
class UtilFormat{

    /**
     * Format datetime
     * @param unknown_type $date
     */
    public static function formatTimeForCreatedDate( $date, $format = "Y/m/d" ) {
        if ( empty( $date ) == false ) {
            return date( $format, strtotime( $date ) );
        } else {
            return "";
        }
    }
    
}
