<?php

//TODO : REWRITE THIS SHIT
class modulesLanguage{
    // languages
    static public $constLang = array( "en" => 0, "fr" => 1 );
    
    // translates
    static public $newDeliveriesOutsideBlock = array( "For deliveries outside of Australia", "Pour livraison à l'extérieur du Canada" );
    static public $CartValue                 = array( "products", "produits" );
    static public $newCart_checkout          = array( "Checkout", "Passe la Cammmande" );
    static public $newCartClick              = array( "Click", "Clic ici" );
    static public $newCartRevealLoad         = array( "Loading", "les informations de chargement" );
    static public $newCartReveal             = array( "Full Cart", "Panier plein" );
    static public $newCartRevealEmpty        = array( "Cart is Empty", "Le panier est vide" );
    static public $newCartRevealTotal        = array( "Total cost of products", "Montant total de produits" );
    static public $getCartPost               = array( "Order Now", "Commander maintenant" );
    static public $newSearchInput            = array( "Search by Keyword, Product SKU or Price", "Rechercher par mot cle, par code de produit ou par prix" );
    /*
    for christmas array( "christmas_cat_banner.jpg", "product_page_banner_fr.png" );
    not for christmas array( "product_page_banner.png", "product_page_banner_fr.png" );
    for valentines array( "valentines_banner.jpg", "product_page_banner_fr.png" );
     */
    
    static public $appendTopBodyBannerImage  = array( "product_page_banner.png", "product_page_banner_fr.png" );
    static public $appendMainPageBannerImage  = array( "Half_Price_Designer_Collection_Bouquets.png", "Moiti_prix_Bouquets_de_Collection-du-Concepteur.png" );
    static public $appendCheckoutBannerImage  = array( "send_more_flowers_now.png", "send_more_flowers_now_fr.png" );
    static public $bloomexlogo  = array( "bloomexlogo.png", "bloomexlogo_fr.png" );
    static public $topLiveChat  = array( "topLiveChat.jpg", "topLiveChat_fr.jpg" );
    static public $cartButtonUpdate  = array( "edit_f2.png", "update_fr.png" );
    static public $flowerDelivery  = array( "Flower Delivery", "Livraison de fleurs" );
    static public $giftbasketDelivery  = array( "Gift Basket Delivery", "Commander cadeau Livraison" );
    static public $sympathyDelivery  = array( "Sympathy Flowers", "Fleurs de sympathie" );
    
    
    // -------------------------------------------------------------------------
    // public function transtate
    static public function get($data){
        
        global $iso_client_lang, $database;
        
        $iso_client_lang = 'en';
        
        if($data == 'appendMainPageBannerImage' || $data == 'appendMainPageBannerImageLink'){
            if($_GET['option'] == 'com_landingpages' && $_GET['type'] == 'basket'){
                $type="basket_".$iso_client_lang;
            }elseif ($_GET['option'] == 'com_landingpages' && $_GET['type'] == 'sympathy') {
                $type="sympathy_".$iso_client_lang;
            }elseif ($_GET['option'] == 'com_landingpages') {
                $type="landing_".$iso_client_lang;
            }else{
                $type="main_".$iso_client_lang;
            }
            $query = "SELECT * FROM jos_vm_edit_banner_href WHERE type='".$type."'";
            $database->setQuery($query);
            $result = $database->loadObjectList();
            if ($data == 'appendMainPageBannerImage'){
                return $result[0]->image;
            }
            if ($data == 'appendMainPageBannerImageLink'){
                return $result[0]->href;
            }
        }
        
        return self::${$data}[self::$constLang[$iso_client_lang]];
     
       /* return self::${$data}[self::$constLang[$iso_client_lang]];
        en only and forever

        return self::${$data}[0];*/
    }
}
?>
