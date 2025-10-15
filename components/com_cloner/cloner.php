<?php
/**
* JoomlaCloner Site Backup 1.0 for Mambo/Joomla CMS
* @version $Id: cloner.php,v 1.1 2007/01/27 16:54:51 roviolor Exp $
* @package JoomlaCloner
* @GNU/GPL
* Oficial website: http://www.joomaplug.com/
* -------------------------------------------
* Admin Business Layer
* Creator: Liuta Ovidiu
* Email: admin@joomlaplug.com
* Revision: 1.0
* Date: April 2006
*/

@error_reporting('2');
@set_time_limit(3600);
// Restrict direct access
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

#load configuration
$config_file = $mosConfig_absolute_path."/administrator/components/com_cloner/cloner.config.php";
include_once( $config_file );


# load language
if($_CONFIG['select_lang']!="")
 $mosConfig_lang = $_CONFIG['select_lang'];

if (file_exists( $mosConfig_absolute_path."/administrator/components/com_cloner/language/".$mosConfig_lang.".php" )) {
    include_once( $mosConfig_absolute_path."/administrator/components/com_cloner/language/".$mosConfig_lang.".php" );
    @include_once( $mosConfig_absolute_path."/administrator/components/com_cloner/language/english.php" );
} 
else{
	include_once( $mosConfig_absolute_path."/administrator/components/com_cloner/language/english.php" );
}


switch ($task){
    case 'connect':
       start_connect();
       break;
    default:
       transfer_form();
       break;

    }

function start_connect(){
    global $mosConfig_absolute_path, $_CONFIG;


$clone_name = $_REQUEST['clone_file'];

if($clone_name==""){
 echo LM_NOPAKCAGE_ERROR;
 return ;
}
 

$source_file[0] = $_CONFIG[clonerPath]."/".$clone_name;
$destination_file[0] = $_REQUEST[ftp_dir]."/".$clone_name;



$source_file[1] = $mosConfig_absolute_path."/administrator/components/com_cloner/restore/Joomla.Cloner.php";
$destination_file[1] = $_REQUEST[ftp_dir]."/Joomla.Cloner.php";
$source_file[2] = $mosConfig_absolute_path."/administrator/components/com_cloner/restore/pcltar.lib.php";
$destination_file[2] = $_REQUEST[ftp_dir]."/pcltar.lib.php";

$source_file[3] = $mosConfig_absolute_path."/administrator/components/com_cloner/restore/pclzip.lib.php";
$destination_file[3] = $_REQUEST[ftp_dir]."/pclzip.lib.php";
#$source_file[] = $mosConfig_absolute_path."/files/ver.txt";

// set up basic connection
$conn_id = @ftp_connect($_REQUEST[ftp_server]);

// login with username and password
$login_result = @ftp_login($conn_id, $_REQUEST[ftp_user], $_REQUEST[ftp_pass]);

// check connection
if ((!$conn_id) || (!$login_result)) {
       echo LM_MSG_FRONT_4;
       echo LM_MSG_FRONT_5." ".$_REQUEST[ftp_server]." ".LM_MSG_FRONT_6." ".$_REQUEST[ftp_user]."<br />";
       transfer_form();
       return;
   } else {
       #echo "Connected to $_REQUEST[ftp_server], for user $_REQUEST[ftp_user]";
   }


for($i=0;$i<sizeof($source_file);$i++)
{
// upload the file
$upload = @ftp_put($conn_id, $destination_file[$i], $source_file[$i], FTP_BINARY);

// check upload status
if (!$upload) {
       echo LM_MSG_FRONT_2." $destination_file[$i]<br />";transfer_form();return;
   } else {
       echo LM_MSG_FRONT_3." $destination_file[$i]<br />";
   }

}

// close the FTP stream
@ftp_close($conn_id);

$redurl = $_REQUEST[ftp_url]."/Joomla.Cloner.php";
echo LM_FRONT_MSG_OK."
<a href='".$redurl."' target='_blank'>$redurl</a>";

}

function transfer_form(){
   global $database;

?>
    <br /><?php echo LM_FRONT_TOP?><br /> <br />
    <form action='' method='POST'>
    <input type='hidden' name='option' value='com_cloner'>
    <input type='hidden' name='task' value='connect'>
    <table  width='90%' cellspacing='5'>

    <tr><td width='200'><?php echo LM_FRONT_CHOOSE_PACKAGE?> </td>
    <td>
    <select name='clone_file'>
    <?php
    $d_arr = array();
    $f_arr = array();
    list_packages($d_arr, $f_arr);
    if(sizeof($f_arr)>0)
    foreach($f_arr as $key=>$value)
     echo "<option value='".$value."'>$value</option>";
    else
     echo "<option value=''>".LM_MSG_FRONT_1."</option>";
    ?>
    </select>
    </td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_CHOOSE_PACKAGE_SUB?></td></tr>
    
    <tr><td colspan='2'><?php echo LM_FRONT_TOP_FTP_DETAILS?></td></tr>
    <tr><td width='200'><?php echo LM_FRONT_WEBSITE_URL?> </td>
    <td><input type='text' size='30' name='ftp_url' value='<?php echo $_REQUEST[ftp_url]?>'></td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_WEBSITE_URL_SUB?></td></tr>
    <tr><td width='200'><?php echo LM_FRONT_FTP_HOST?> </td>
    <td><input type='text' size='30' name='ftp_server' value='<?php echo $_REQUEST[ftp_server]?>'></td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_FTP_HOST_SUB?></td></tr>
    <tr><td width='200'><?php echo LM_FRONT_FTP_USER?> </td>
    <td><input type='text' size='30' name='ftp_user' value='<?php echo $_REQUEST[ftp_user]?>'></td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_FTP_USER_SUB?></td></tr>
    <tr><td width='200'><?php echo LM_FRONT_FTP_PASS?> </td>
    <td><input type='text' size='30' name='ftp_pass' value='<?php echo $_REQUEST[ftp_pass]?>'></td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_FTP_PASS_SUB?></td></tr>
    <tr><td width='200'><?php echo LM_FRONT_FTP_DIR?> </td>
    <td><input type='text' size='30' name='ftp_dir' value='<?php echo $_REQUEST[ftp_dir]?>'></td></tr>
    <tr><td colspan='2'><?php echo LM_FRONT_FTP_DIR_SUB?></td></tr>
    <tr><td colspan='2'><input type='submit' name='submit' value='Attempt Install...'></td></tr>
    
    </table>
    </form>
    <br /><br />
    <?php echo LM_FRONT_BOTTOM?>

<?php
}

function list_packages(&$d_arr, &$f_arr){
    global $mosConfig_absolute_path, $_CONFIG;
    

   $path = $_CONFIG["baDownloadPath"];

   if( is_dir( $path ) ) {
        if( $handle = opendir( $path ) ) {
            while( false !== ( $file = readdir( $handle ) ) ) {
                # Make sure we don't push parental directories or dotfiles (unix) into the arrays
                if( $file != "." && $file != ".." && $file[0] != "." ) {
                    if( is_dir( $path . "/" . $file ) )
                        # Create array for directories
                        $d_arr[$d++] = $file;
                    else
                        if ((strstr($file, '.zip' ))||(strstr($file, '.tgz' ))) {
                            # Create array for files
                            $f_arr[$f++] = $file;
                        }
                }
            }
         }
      }

}


?>
