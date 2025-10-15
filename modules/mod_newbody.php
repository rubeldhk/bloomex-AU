<?php

class newBody{

    private $option = null;
    private $page = null;
    private $task = null;
    private $content_id = null;
    
    public function __construct() {
        $this->option       = !empty( $_REQUEST['option'] ) ? trim( $_REQUEST['option'] ) : "";
        $this->page         = !empty( $_REQUEST['page']   ) ? trim( $_REQUEST['page']   ) : "";
        $this->task         = !empty( $_REQUEST['task']   ) ? trim( $_REQUEST['task']   ) : "";
        $this->category_id   = !empty( $_REQUEST['category_id']     ) ? trim( $_REQUEST['category_id']     ) : "";
        $this->content_id = !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : "";
    }
    
    function body(){
        echo $this->quote();
       
        mosLoadModules('topBodyImg', -1);
        if ( mosCountModules("newsflash ") || ($this->option == "com_frontpage") || ($this->option == '')  || ($this->option == "com_page_not_found")  || ($this->option == "com_best_seller") ) {

            mosLoadModules('newsflash', -1);
        } else {
            mosMainBody();

               mosLoadModules('user9', -1);


        }
        global $sef;
        if ( $sef->landing_type<=0 || $sef->landing_type==4){
            mosLoadModules('botBodyFrm', -1);
        }
    }
    
    private function quote(){
        if ($this->option == "com_virtuemart" && $this->page == "shop.browse" && $this->category_id == 259) {
            return "<a href='{$GLOBALS['mosConfig_live_site']}/quote-request/' class=\"customform\">
                        <img alt='click here for quote' border='0' src='{$GLOBALS['mosConfig_live_site']}/templates/bloomex7/images/Click-here-for-Quote.png' width='150' />
                    </a>";
        }
    }

}

$newBody = new newBody();
$newBody->body();
?>