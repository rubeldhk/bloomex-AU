<?php
class templateBanners{
    
    // data
    // database table jos_vm_landing_page_title_categoty
    private $result = '';
    private $result_upper='';
    private $result_corner='';
    
    
    //---------------------------------------------------------------------------
    function templateBanners( $connect )  // $lang = 'en' or 'fr'
    {
        global $mosConfig_absolute_path;
        $this->result_upper = '<table cellspacing="0" cellpadding="0" width="100%" border="0">';
        $i = 0;
        $dir = '/images_upload/com_edit_banner/';
         $query = "SELECT href, large FROM jos_vm_edit_banner_href WHERE title in (1,2) order by title";
                                        $results = mysql_query($query, $connect);
        $this->result_upper .= '<tr>';
        $large=-1;
            if( $results ){
                    while( $row = mysql_fetch_assoc($results) )
                    {   
                        if($large<0){
                        $large=$row['large'];
                        }
                        if(($large>0)&&($i==0)){
                       $this->result_upper .= '<td colspan=2 align="center" >';
                       $this->result_upper .= '<a href="'.$row['href'].'">'; 
                       $this->result_upper .= '<img src="'.$dir.'mini_banner_'.($i+1).'.jpg" border="0" />';
                       $this->result_upper .= '</a>';
                       $i++;
                       $this->result_upper .= '</td>';
                        }
                        else{
                            if($large==0){
                       $this->result_upper .= '<td align="center" >';
                       $this->result_upper .= '<a href="'.$row['href'].'">'; 
                       $this->result_upper .= '<img src="'.$dir.'mini_banner_'.($i+1).'.jpg" border="0" />';
                       $this->result_upper .= '</a>';
                       $i++;
                       $this->result_upper .= '</td>';
                            }
                        }
                    }
                }
                $this->result_upper .=  '</tr>';
        $this->result_upper .=  '</table>';

            $this->result_corner = ''; 
        $i = 0;
        $dir = '/images_upload/com_edit_banner/';
         $query = "SELECT href FROM jos_vm_edit_banner_href WHERE title in (3,4)";
                                        $results = mysql_query($query, $connect);
                  if( $results ){
                    while( $row = mysql_fetch_assoc($results) )
                    {     
                       $this->result_corner .= '<a name="corners" style="display:block;" href="'.$row['href'].'">'; 
                       switch($i){
                           case 0:
                       $this->result_corner .= '<img  border="0" src="'.$dir.'corner_banner_'.($i+1).'.jpg" style="bottom: 0pt;position: fixed;left: 0pt;z-index:10" />';
                               break;
                           case 1:
                       $this->result_corner .= '<img  border="0" src="'.$dir.'corner_banner_'.($i+1).'.jpg" style="bottom: 0pt;position: fixed;right: 0pt;z-index:10" />';
                           break;
                           default: break;
                       }
                       $this->result_corner .= '</a>';
                       $i++;
                       
                    }
                }   
    }
    
    
    function get()
    {
        return $this->result;
    }
    
     function get_upper()
    {
        return $this->result_upper;
    }
     function get_corner()
    {
        return $this->result_corner;
    }    
}
?>

