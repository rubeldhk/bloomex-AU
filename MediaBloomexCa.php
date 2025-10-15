<?php
class MediaBloomexCa
{
    private $ftp_server = '54.244.112.77';
    private $ftp_user = "mediauser";
    private $ftppass = "FbC8SOh0BJnR6fTIln1t";
    private $ftplink = '';
    private $error_log = null;
    // ------------------------------------------------------------------------
    public function __construct()
    {
        $this->ftplink = ftp_connect($this->ftp_server, 21);
        if (!$this->ftplink)
        {
            $this->error_log = 'No connect with '.$this->ftp_server;
        }
        else {
            if (@ftp_login($this->ftplink, trim($this->ftp_user), trim($this->ftppass))) {

            ftp_pasv($this->ftplink, true);

            } else {
                $this->error_log = 'Bad login: '.$this->ftp_user .'@'.$this->ftppass;
            }
        }
    }
    
    // ------------------------------------------------------------------------
    public function upload( $new_name, $path, $file ) // 
    {
        //$new_name .= substr( strrchr( $old_name, '.' ), 1 ); // File extension for new file name
        if( $this->error_log ) return $this->error_log; // Bad connect
        if( !$this->protect_image( $file ) ) return $this->error_log; // Bad file
        $this->isset_path( $path );
        $upload = ftp_put( $this->ftplink, $path.'/'.$new_name, $file, FTP_BINARY); 
        //return $this->error_log = ( $upload ) ? 'File is upload.' : 'Error upload file.';
    }
    
    public function fupload( $new_name, $path, $file ) // 
    {
        //$new_name .= substr( strrchr( $old_name, '.' ), 1 ); // File extension for new file name
        if( $this->error_log ) return $this->error_log; // Bad connect
        //if( !$this->protect_image( $file ) ) return $this->error_log; // Bad file
        
        $this->isset_path( $path );
        
        return file_put_contents('ftp://'.$this->ftp_user.':'.$this->ftppass.'@'.$this->ftp_server.$path.$new_name, $file);
    }
    
    private function isset_path( $isset_path )
    {
        $dir = explode( '/', $isset_path );
        $count = count($dir);
        $path = '/';
        for( $i = 0; $i < $count; $i++ )
        {
            $path .= $dir[$i].'/';
            @ftp_mkdir( $this->ftplink, $path );
        }
    }
    
    public function delete($file)
    {
        @ftp_delete($this->ftplink, $file);
    }
    
    private function protect_image( $image )
    {
        $blacklist = array(".php", ".phtml", ".php3", ".php4");
        foreach ($blacklist as $item) {
            if(preg_match("/$item\$/i", $image))
            {
                $this->error_log = 'Bad FILE EXTENSION!';
                return false;
            }
        }
        return true;
    }
}
?>
