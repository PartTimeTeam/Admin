<?php
/**
 * Controller Action Helper setup common functions for file and directory
 * Class Name:  My_Controller_Action_Helper_File
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 8, 2009
 * @Version V001 Jul 8, 2009 (hoangpm) New Create
 */

class My_Controller_Action_Helper_File extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Create directory
     * Function Name: createDir
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $path full path of the folder to be created
     * @return  Boolean  var TRUE/FALSE
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     * @Version V002 Feb 03, 2009 (boihn) Update
     **/

    public function createDir( $path )
    {
        /*
         * Check path input
        */
        $path = trim( $path );
        if(strlen( $path ) == 0){
            return false;
        }
        /*
         * Perform check OS
        */
        $Path = "/";
        if(PHP_OS == 'WINNT'){
            $Path = "";
            $path = str_replace("\\", "/", $path);
        }
        /*
         * Spit the path in to saparate folder name
        */
        $Array = split("/", $path);
        if(is_array($Array) == FALSE){
            return false;
        }
        /*
         * Perform check and create each folder in the path
        */
        foreach($Array as $value){
            $value = trim($value);
            if(strlen($value) > 0){
                $Path .= $value."/";
                if(@is_dir($Path) == FALSE){
                    $result = @mkdir($Path , 0777);
                    if($result == FALSE){
                        return false;
                    }
                    /*
                     * Change mode for the folder
                    */
                    @chmod( $Path, 0777 );
                }
            }
        }
        return true;
    }

    /**
     * Remove directory
     * Function Name: removeDirLinux
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string $path full path of the folder to be removed
     * @return  Array  var
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/

    public function removeDirLinux( $path, $secure_flg = TRUE )
    {
        $path = trim( $path );

        if( $secure_flg == TRUE )
        {
            $check = self::checkSecure( $path );
            if( $check == FALSE )
            {
                return false;
            }
        }

        if( strlen( $path ) == 0 || is_dir( $path ) == FALSE )
        {
            return false;
        }

        //Globals::getPathConfig()->toArray();
        // TODO: Check and do not perform delete for root web path

        $cmd = "rm -rf ";
        $Arg = array();
        $Arg[] = $path;
        return self::exeCommand( $cmd, $Arg );

    }

    /**
     * Remove file
     * Function Name: removeFile
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $file_name full path of the file name to be deleted
     * @return  type  var description
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function removeFile( $file_name, $secure_flg = TRUE )
    {
        $file_name = trim( $file_name );

        if( $secure_flg == TRUE )
        {
            $check = self::checkSecure( $file_name );
            if( $check == FALSE )
            {
                return false;
            }
        }

        if( strlen( $file_name ) == 0 || is_file( $file_name ) == FALSE )
        {
            return false;
        }

        return @unlink ( $file_name );
    }

    /**
     * Check valid file
     * Function Name: isFile
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $file_name full path of the file to be checked
     * @return  type  var description
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function isFile( $file_name )
    {
        $file_name = trim( $file_name );
        if( strlen( $file_name ) == 0 )
        {
            return false;
        }

        return is_file( $file_name );
    }

    /**
     * Rename the file or folder to the new name
     * Function Name: renameFile
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $file_name full path of the file to be renamed
     * @param2      string  $new_name full path of the new file name
     * @return  Boolean  var TRUE/FALSE
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function rename( $file_name, $new_name )
    {
        $file_name = trim( $file_name );
        $new_name = trim( $new_name );

        if( strlen( $file_name ) == 0 || strlen( $new_name ) == 0
                || $file_name == $new_name )
        {
            return false;
        }

        return rename( $file_name, $new_name );
    }

    /**
     * Copy all data from one folder to another folder
     * Function Name: copyDirLinux
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $from_path full path of the folder source
     * @param2      string  $to_path full path of the folder destination
     * @return  Array  var
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function copyDirLinux( $from_path, $to_path )
    {
        $from_path = trim( $from_path );
        $to_path = trim( $to_path );

        if( strlen( $from_path ) == 0 || strlen( $to_path ) == 0
                || $from_path == $to_path )
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Check source path and desination path
        //-------------------------------------------------------------------//
        $check_from_path = $from_path;
        $end_str = strlen( $from_path );
        if( substr( $from_path, -3, 3 ) == "*.*" )
        {
            $end_str -= 3;
        }
        elseif( substr( $from_path, -1, 1 ) == "*"
                || substr( $from_path, -1, 1) == "." )
        {
            $end_str -= 1;
        }

        $check_from_path = substr( $from_path, 0, $end_str );

        if( is_dir( $check_from_path ) == FALSE || is_dir( $to_path ) == FALSE
                || is_writable( $to_path ) == FALSE
                || is_writable( $check_from_path ) == FALSE )
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Copy directoty
        //-------------------------------------------------------------------//
        @chmod( $to_path, 0777 );
        $cmd = "cp -rf ";
        $Arg = array();
        $Arg[] = $from_path;
        $Arg[] = $to_path;
        $result = self::exeCommand( $cmd, $Arg );

        //-------------------------------------------------------------------//
        // Change mod 0777 for all folder recursively
        //-------------------------------------------------------------------//
        self::exeCommand( "chmod -R 0777", $to_path );

        return $result;
    }
    /**
     * Copy directoy ( all file and subdirectories under it )
     * from one location to another location.
     * Function Name: copyDir
     * Programmer: phongtc (GCS)
     * Create Date: Jan 2, 2009
     *
     * @param1      string  $from_path source folder
     * @param2      string  $to_path destination folder
     * @throws  Zend_Cache_Exception
     * @return  boolean  TRUE/FALSE
     * @Version V001 Jan 2, 2009 (phongtc) New Create
     **/
    public function copyDir( $from_path, $to_path )
    {
        //-------------------------------------------------------------------//
        // Check input directoies
        //-------------------------------------------------------------------//
        $from_path = trim( $from_path );
        $to_path = trim( $to_path );

        if( strlen( $from_path ) == 0 || strlen( $to_path ) == 0
                || $from_path == $to_path )
        {
            return false;
        }

        if( substr( $from_path, -1, 1 ) == "*" )
        {
            $from_path = substr( $from_path, 0, strlen( $from_path ) - 1 );
        }

        if( substr( $from_path, -1, 1 ) == "/" )
        {
            $from_path = substr( $from_path, 0, strlen( $from_path ) - 1 );
        }

        if( substr( $to_path, -1, 1 ) == "/" )
        {
            $to_path = substr( $to_path, 0, strlen( $to_path ) - 1 );
        }

        //-------------------------------------------------------------------//
        // Check writable of directoies
        //-------------------------------------------------------------------//
        if( is_dir( $from_path ) == FALSE || is_dir( $to_path ) == FALSE
                || is_writable( $from_path ) == FALSE
                || is_writable( $to_path ) == FALSE )
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Read file and subfolders of source folder
        //-------------------------------------------------------------------//
        return self::_copyDir( $from_path, $to_path );
    }

    /**
     * Remove all file and subdirectories under it
     * Function Name: removeDir
     * Programmer: phongtc (GCS)
     * Create Date: Jan 2, 2009
     *
     * @param1      String  $path full path of the deleted folder
     * @throws  Zend_Cache_Exception
     * @return  Boolean  TRUE/FALSE
     * @Version V001 Jan 2, 2009 (phongtc) New Create
     **/
    public function removeDir( $path, $secure_flg = TRUE )
    {
        //-------------------------------------------------------------------//
        // Check input directoies
        //-------------------------------------------------------------------//
        $path = trim( $path );

        if( strlen( $path ) == 0 )
        {
            return false;
        }
        //-------------------------------------------------------------------//
        // Remove * character at the end of the string
        //-------------------------------------------------------------------//
        if( substr( $path, -1, 1 ) == "*" )
        {
            $path = substr( $path, 0, strlen( $path ) - 1 );
        }

        if( $secure_flg == TRUE )
        {
            $check = self::checkSecure( $path );
            if( $check == FALSE )
            {
                return false;
            }
        }

        if( substr( $path, -1, 1 ) == "/" )
        {
            $path = substr( $path, 0, strlen( $path ) - 1 );
        }

        //-------------------------------------------------------------------//
        // Check writable of directoies
        //-------------------------------------------------------------------//
        if( is_dir( $path ) == FALSE || is_writable( $path ) == FALSE )
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Read file and subfolders of source folder
        //-------------------------------------------------------------------//
        return self::_removeDir( $path );
    }

    /**
     * Copy directoy ( all file and subdirectories under it )
     * from one location to another location.
     * Function Name: _copyDir
     * Programmer: phongtc (GCS)
     * Create Date: Jan 2, 2009
     *
     * @param1      string  $from_path source folder
     * @param2      string  $to_path destination folder
     * @throws  Zend_Cache_Exception
     * @return  boolean  TRUE/FALSE
     * @Version V001 Jan 2, 2009 (phongtc) New Create
     **/
    private function _copyDir( $from_path, $to_path )
    {

        //-------------------------------------------------------------------//
        // Read file and subfolders of source folder
        //-------------------------------------------------------------------//
        $curdir = @opendir( $from_path );

        if( $curdir == FALSE )
        {
            return false;
        }

        while( ( $file = @readdir( $curdir ) ) != FALSE )
        {
            if( $file != '.' && $file != '..' )
            {
                $srcfile = $from_path . '/' . $file;
                $dstfile = $to_path . '/' . $file;

                //-----------------------------------------------------------//
                // Copy file to destination folder
                //-----------------------------------------------------------//
                if( is_file( $srcfile ) == TRUE )
                {
                    $result = Files::copyFile( $srcfile, $dstfile );

                    if(  $result == FALSE )
                    {
                        return false;
                    }
                }
                //-----------------------------------------------------------//
                // Copy sub folders
                //-----------------------------------------------------------//
                elseif( is_dir( $srcfile ) == TRUE )
                {
                    Files::createDir( $dstfile );
                    self::_copyDir( $srcfile, $dstfile );
                }
            }
        }

        closedir( $curdir );
        return true;
    }

    /**
     * Remove all file and subdirectories under it
     * Function Name: _removeDir
     * Programmer: phongtc (GCS)
     * Create Date: Jan 2, 2009
     *
     * @param1      String  $path full path of the deleted folder
     * @throws  Zend_Cache_Exception
     * @return  Boolean  TRUE/FALSE
     * @Version V001 Jan 2, 2009 (phongtc) New Create
     **/
    private function _removeDir( $path )
    {
        //-------------------------------------------------------------------//
        // Read file and subfolders of source folder
        //-------------------------------------------------------------------//

        $curdir = @opendir( $path );

        if( $curdir == FALSE )
        {
            return false;
        }

        while( ( $file = @readdir( $curdir ) ) != FALSE )
        {
            if( $file != '.' && $file != '..' )
            {
                $srcfile = $path . '/' . $file;
                //-----------------------------------------------------------//
                // Copy file to destination folder
                //-----------------------------------------------------------//
                if( is_file( $srcfile ) == TRUE )
                {
                    $result = Files::removeFile( $srcfile, FALSE );
                    if(  $result == FALSE )
                    {
                        return false;
                    }
                }
                //-----------------------------------------------------------//
                // Copy sub folders
                //-----------------------------------------------------------//
                elseif( is_dir( $srcfile ) == TRUE )
                {
                    self::_removeDir( $srcfile );
                }
            }
        }
        closedir( $curdir );
        @rmdir( $path );
        return true;
    }

    /**
     * In secure mode, it allows to delete user/start data folders only
     * Function Name: checkSecure
     * Programmer: phongtc (GCS)
     * Create Date: Jan 2, 2009
     *
     * @param1      String  $path full path of the deleted folder
     * @throws  Zend_Cache_Exception
     * @return  Boolean  TRUE/FALSE
     * @Version V001 Jan 2, 2009 (phongtc) New Create
     **/

    private function checkSecure( $path )
    {
        $path = rtrim( $path, "/" );
        $user_data_fd = rtrim( Commons::getUserDataFolder(), "/" );
        $standard_data_fd = rtrim( PATH_STANDARD_DATA, "/" );
        $agency_data_fd = rtrim( PATH_AGENCY_DATA, "/" );

        //-------------------------------------------------------------------//
        // Not allow delete standard or user data folders
        //-------------------------------------------------------------------//
        if( $path == $standard_data_fd || $path == $user_data_fd
                || $path == $agency_data_fd)
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Check standard and user data folder
        //-------------------------------------------------------------------//
        $check1 = substr( $path, 0, strlen( $standard_data_fd ) );
        $check2 = substr( $path, 0, strlen( $user_data_fd ) );
        $check3 = substr( $path, 0, strlen( $agency_data_fd ) );
        if( $check1 != $standard_data_fd && $check2 != $user_data_fd
                && $check3 != $agency_data_fd )
        {
            return false;
        }

        return true;
    }

    /**
     * Copy file from one folder to another folder
     * Function Name: exeCommand
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $cmd system command
     * @param2      type  $Arg folder name or folder list
     * @return  Array  var Result of the command
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function exeCommand( $cmd, $Arg )
    {
        //-------------------------------------------------------------------//
        // Check the command and input parameter
        //-------------------------------------------------------------------//
        $Arg_Array = $Arg;
        $cmd = trim( $cmd );
        if( is_array( $Arg ) == FALSE )
        {
            $Arg_Array = array( );
            if( strlen( $Arg ) > 0 )
            {
                $Arg_Array[] = $Arg;
            }
        }

        if( strlen( $cmd ) == 0 )
        {
            return false;
        }

        //-------------------------------------------------------------------//
        // Get input parameter and append to the command
        //-------------------------------------------------------------------//

        if( empty( $Arg_Array ) == FALSE )
        {
            $Arg_Array = array_unique( $Arg_Array );

            $exe_cmd = $cmd;
            foreach( $Arg_Array as $value )
            {
                $exe_cmd .= " ".$value;
            }
        }
        //-------------------------------------------------------------------//
        // Execute the command
        //-------------------------------------------------------------------//
        $Output_Array = array();
        @exec( $exe_cmd, $Output_Array );
        return $Output_Array;
    }

    /**
     * Copy file from one folder to another folder
     * Function Name: copyDir
     * Programmer: phongtc (GCS)
     * Create Date: Dec 17, 2008
     *
     * @param1      string  $from_file full path of the file source
     * @param2      string  $to_file full path of the file description
     * @return  Boolean  var TRUE/FALSE
     * @Version V001 Dec 17, 2008 (phongtc) New Create
     **/
    public function copyFile( $from_file, $to_file )
    {
        $from_file = trim( $from_file );
        $to_file = trim( $to_file );

        if( strlen( $from_file ) == 0 || strlen( $to_file ) == 0
                || $from_file == $to_file )
        {
            return false;
        }

        $result = @copy( $from_file, $to_file );

        if( $result == TRUE )
        {
            @chmod( $to_file, 0777 );
        }

        return $result;
    }

    public function zipYakPod($path)
    {
        @exec ("cd " .$path. "; zip -r ".$path."/YakPod.zip YakPod");
    }
}