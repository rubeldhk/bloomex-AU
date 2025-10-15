<?php
require_once 'QueryBase.php';
class CreateExcel extends QueryBase
{
    function check(){
        global $database;
        if( isset( $_POST['submitvalue'] ) && $_POST['submitvalue'] == 'excel' )
        {
            $q = $this->searchQuery();
            if( !$q ) return "Create a search query.";
            $database->setQuery( $q );
            $result = $database->loadAssocList();
            $this->showExcell($result, "Legacy.");
        }
    }
    function showExcell($rows, $title){

        $data = "" ;
        $sep = "\t"; 
    if(count($rows)>0)      {          
        $data .= "Report: \t ".$title."\t \t Date :\t" . date("d/m/Y"). "\t \t \n \n";
        $fields = (array_keys($rows[0]));
        $columns = count($fields);

        for ($i = 0; $i < $columns; $i++) {
        $data .= $fields[$i].$sep;
        }
        $data .= "\n";

        for($k=0; $k < count( $rows ); $k++) {
              $row = $rows[$k];
              $line = '';

              foreach ($row as $value) {
                $value = str_replace('"', '""', $value);
                $line .= '"' . html_entity_decode( trim($value), ENT_QUOTES, "UTF-8" ) . '"' . "\t";
              }
              $data .= trim($line)."\n";
          }

        $data = str_replace("\r","",$data);

        if (count( $rows ) == 0) {
            $data .= "\n(0) Records Found!\n";
        }

        $data .= "\n \n \n  \t Copyrights 09.12.2013 \t  \t Contact:  \n";
      }
      else
      {
          $data = "\n(0) Records Found!\n";
      }
      
      ///////////////////////
          header("Content-type: application/vnd.ms-excel");
          header("Content-Disposition: attachment; filename=legacy.xls");
          header("Pragma: no-cache");
          header("Expires: 0");
          header("Content-Transfer-Encoding: binary");
          header("Lacation: excel.htm?id=yes");
          $data = iconv("UTF-8", "UTF-16", $data);
          print $data ;
          die();
      }
}
?>
