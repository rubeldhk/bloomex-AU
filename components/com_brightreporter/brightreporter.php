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

require_once( $mainframe->getPath( 'front_html', $option ) );

  switch ($task) {
      case 'showreport':
        showreport($option, 0 );
        break;
      case 'PrepReport':
        PrepReport( $option );
        break;
      case 'showexcell':
        showreport($option, 1 );
        break;
      case 'about':
        HTML_BrightReporter::about();
        break;          
      default:
        showlist( $option);
        break;
  }

function showlist( $option) {
  global $database, $mainframe;

  $query = "Select * from #__jeporter where block = 0";
  $database->setQuery( $query );
  $rows = $database->loadObjectList();

  $Itemid = mosGetParam( $_REQUEST, 'Itemid', false);
  // get params definitions
  $menu =new  mosMenu( $database );
  $menu->load( $Itemid );
  $params =new  mosParameters( $menu->params );
  
  $cid = $params->get( 'cid' );
 
  if($cid!=''){
    PrepReport( $option, $cid );
  }else{  
    HTML_BrightReporter::displayReports($rows, $option, $Itemid );
  }

}

function PrepReport( $option, $cid=0 ){
  global $database;

  if($cid==0){
    $cid = mosGetParam( $_REQUEST, 'cid', false);
  }
  
	$cids = 'id=' . $cid;
	$jids = 'jeportid=' . $cid;

  $sql = "SELECT * FROM #__jeporter WHERE ( $cids )";
  $database->setQuery( $sql );
  $report = NULL;
  $database->loadObject( $report );

  $cid = $report->id;
  $title = $report->title;
  $jquery = $report->jquery;  

	$query = "SELECT * FROM #__jeporter_fields WHERE ( $jids )";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

  HTML_BrightReporter::PrepReport($cid, $option, $title, $jquery, $rows) ;
}

function showreport($option, $excel=0 ){
		global $database;

    $cid = mosGetParam( $_REQUEST, 'cid', false);

		$sql = "SELECT * FROM #__jeporter WHERE id= ".$cid;
		$database->setQuery( $sql );
		$report = NULL;
		$database->loadObject( $report );

		$cid = $report->id;
		$title = $report->title;
		$jquery = $report->jquery;
    
		$sql = "SELECT * FROM #__jeporter_fields WHERE jeportid= ".$cid;
    $database->setQuery( $sql);
    $rows = $database->loadObjectList();

    $i=0;
    foreach($rows as $row){ 
    
      $jquery = str_replace('${'.$row->fieldname.'}', trim(mosGetParam( $_REQUEST, $row->fieldname, false)), $jquery);

      $i++;
    }

		$database->setQuery($jquery);
		$rows = $database->loadAssocList();
    if($excel==0){
        HTML_BrightReporter::showReport($rows, $title, $option, $cid) ;
    }elseif($excel==1){
        HTML_BrightReporter::showExcell($rows, $title, $option, $cid) ;
    }
}
?>