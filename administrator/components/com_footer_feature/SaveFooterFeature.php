<?php
class SaveFooterFeature{
    
    private $batabaseTable = "jos_vm_footer_feature";
    private $names = array('line','title','subtitle','text','images');
    private $catalogFooter = "../images_upload/footer_feature/line_{LINE}/"; 
    private $line = 0;
    
    private $countItems = 3;
    
    function __construct() {
    }
    
    function insert(){
        global $database;
        $dataset = null;
        $images = null;
        foreach ( $this->names as $value ){
            $dataset .= ( is_null( $dataset ) ? "" : "," );
            switch ($value) {
                                case 'line':
                                    $dataset .= "'".$_POST[$value]."'";
                                    break;
                                case "images":
                                    for ($i = 0; $i < $this->countItems; $i++) {
                                        $images .= ((is_null($images)) ? "" : "[--1--]") . $_FILES["userfile_{$this->line}_{$i}"]['name'];
                                    }
                                    break;

                                default:
                                    $dataset .= "'".addslashes($_POST[$value.'_'.$this->line])."'";
                                    break;
                            }
        }
        $dataset .= "'$images'";
        $hrefs = null;
        for( $i = 0; $i < $this->countItems; $i++ ) $hrefs .= ( ( is_null( $hrefs ) ? '' : '[--1--]' ) ) . $_POST['href_'.$this->line.'_'.$i];
        $dataset .= ",'$hrefs'";
        $query = "INSERT INTO $this->batabaseTable (".implode(',',$this->names).",hrefs) VALUES ($dataset)";
        $database->setQuery($query);
        $database->query();
        for ( $i = 0; $i < $this->countItems; $i++ ) {
            $this->image( $i );
        }
    }
    
    function image( $i, $old = null ){
        $name = $this->imageName($i);
        $catalog = str_replace( "{LINE}" , $this->line, $this->catalogFooter);
        $insertName = $_FILES[$name]['name'];
        if( strlen( $insertName ) > 0 ) {
            $i = 0;
            $add = '';
            while (true) {
                if( file_exists( $catalog . $add .  $insertName ) ){
                    $add = "r".$i;
                }
                else{
                    $insertName = $add .  $insertName;
                    break;
                }
                $i++;
            }
            if( !is_null( $old ) ) unlink( $catalog.$old );
            move_uploaded_file( $_FILES[$name]['tmp_name'], $catalog . $insertName );
        }
        return $insertName;
    }
    
    function imageName($i){
        return 'userfile_'.$this->line.'_'.$i;
    }
    
    function save(){
        global $database;
        $this->line = ( isset( $_POST['line'] ) ) ? $_POST['line'] : null;
        if( is_null( $this->line ) ) return null;
        $query = "SELECT * FROM $this->batabaseTable WHERE line='$this->line'";
        $database->setQuery($query);
        $result = $database->loadObjectList();
        if( is_array($result) ){
            if(count($result) < 1 ){
                $this->insert();
            }
            else{
                $dataset = null;
                foreach ($result as $row) {
                    $standImages = explode( '[--1--]', $row->images );
                    foreach ( $this->names as $value ) {
                        //if( strlen( $_POST[$value] ) > 0 ){
                            if( $value == 'images' ){ 
                                for ( $i = 0; $i < $this->countItems; $i++ ) {
                                    $name = $this->imageName($i);
                                    if( strlen( $_FILES[$name]['name'] ) > 0 ){
                                        $newName = $this->image( $i, $standImages[$i] );
                                        $standImages[$i] = $newName;
                                    }
                                }
                            }
                            $dataset .= ( is_null( $dataset ) ? "" : "," );
                            $datasetImages = implode("[--1--]", $standImages);
                            switch ($value) {
                                case 'line':
                                    $dataset .= "$value='".$_POST[$value]."'";
                                    break;
                                case "images":
                                    break;

                                default:
                                    $dataset .= "$value='".addslashes($_POST[$value.'_'.$this->line])."'";
                                    break;
                            }
                            
                        //}
                    }
                }
                $dataset .= "images='$datasetImages'";
                $hrefs = null;
                for( $i = 0; $i < $this->countItems; $i++ ) $hrefs .= ( ( is_null( $hrefs ) ? '' : '[--1--]' ) ) . $_POST['href_'.$this->line.'_'.$i];
                $dataset .= ",hrefs='$hrefs'";
                $query = "UPDATE $this->batabaseTable SET $dataset WHERE line='$this->line'";
                $database->setQuery($query);
                $database->query();
            }
        }
        return "Saving was successful.";
    }
}
$SaveFooterFeature = new SaveFooterFeature();
echo $SaveFooterFeature->save();
?>
