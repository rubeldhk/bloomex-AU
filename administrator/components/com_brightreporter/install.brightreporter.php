<?php
/****************************************************************************************************
* Package : Brightcode Reporter
* Author : Theo van der Sluijs
* Llink : http://www.brightcode.eu
* Copyright (C) : 2007 Brightcode.eu
* Email : info@brightcode.eu
* Date : October 2007
* Package Code License :  Commercial License / http://www.brightcode.eu
* Joomla! API Code License : http://www.gnu.org/copyleft/gpl.html GNU/GPL 
* JavaScript Code & CSS : Commercial License / http://www.brightcode.eu
****************************************************************************************************
 * Copyrights (c) 2007
 * All rights reserved. Brightcode.eu
 *
 * This program is Commercial software.
 * Unauthorized reproduction is not allowed.
 * Read the complete license model on our site before using this product
 * http://www.brightcode.eu
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.
 *
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *****************************************************************************************************/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


function com_install() {
  global $database, $mainframe;
  
#install tables ect.

    $sql = "CREATE TABLE IF NOT EXISTS  `#__jeporter` ( `id` int NOT NULL AUTO_INCREMENT , `title` varchar (200) , `jquery` mediumtext , `createdon` datetime , `block` int , `memo` varchar (250), PRIMARY KEY (`id`))";
  	$database->setQuery( $sql );
    if (!$database->query()) {
      echo $database->stderr();
      return false;
    }

    # check if field exists
    $prefix = $database->_table_prefix;
    $tables = array( $prefix.'jeporter');
    $tablefields = $database->getTableFields( $tables );
    $memofound = 0;

//print_r ($tablefields);
    
    function multiarray_keys($ar) {
               
        foreach($ar as $k => $v) {
            $keys[] = $k;
            if (is_array($ar[$k]))
                $keys = array_merge($keys, multiarray_keys($ar[$k]));
        }
        return $keys;
    }
    $search_array = multiarray_keys( $tablefields );

    foreach ($search_array as $value)
    {
      if ($value == 'memo') {
        $memofound=1;
      }
    }

    if ($memofound==0) {
      $sql = "ALTER TABLE #__jeporter ADD COLUMN `memo` varchar (250)  NULL  AFTER `block`";
      $database->setQuery( $sql );
      if (!$database->query()) {
        echo $database->stderr();
        return false;
      }
    }  

    
    $sql = "CREATE TABLE IF NOT EXISTS  `#__jeporter_fields` ( `id` int (11) NOT NULL AUTO_INCREMENT , `jeportid` int (11) NOT NULL , `fieldkindid` int (11) NOT NULL , `fieldname` varchar (200) NOT NULL, `fieldcode` varchar (250)  NULL , PRIMARY KEY (`id`))";
  	$database->setQuery( $sql );
    if (!$database->query()) {
      echo $database->stderr();
      return false;
    }
    
    ${LastDate}="$"."{LastDate}";
    $sql = "INSERT IGNORE INTO #__jeporter (`id`, `title`, `jquery`, `createdon`, `block`, `memo`) VALUES ('1','Users last visit','SELECT `name`, `lastvisitdate` from #__users WHERE DATE(lastvisitdate) =  \'${LastDate}\' ORDER BY `lastvisitDate`','2007-08-29 09:46:44','0', 'This shows the last visist of all users')";
  	$database->setQuery( $sql );
    if (!$database->query()) {
      echo $database->stderr();
      return false;
    }
    
    $sql = "INSERT IGNORE INTO #__jeporter_fields (`id`, `jeportid`, `fieldkindid`, `fieldname`, `fieldcode`) VALUES ('1','1','1','LastDate','')";
  	$database->setQuery( $sql );
    if (!$database->query()) {
      echo $database->stderr();
      return false;
    }
    
    $sql = "INSERT IGNORE INTO `#__jeporter` ( `id`,`title`,`jquery`,`createdon`, `block`) VALUES (2,'Search Terms ','SELECT * from #__core_log_searches',NOW(), 0)";
  	$database->setQuery( $sql );
    if (!$database->query()) {
      echo $database->stderr();
      return false;
    }
    
  $mosConfig_absolute_path = $mainframe->getCfg('absolute_path');

  include("components/com_brightreporter/about.brightreporter.php");

}
?>
