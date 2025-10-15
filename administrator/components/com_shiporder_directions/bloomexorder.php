<?php 
class SenderOptions { 
    var $Name = null;
    var $StreetNumber = null;
    var $StreetName = null;
    var $City = null;
    var $Province = null;
    var $Country = null;
    var $PostalCode = null;
    var $CountryCode = null;
    var $AreaCode = null;
    var $Phone = null;
    function SenderOptions($login) {
        switch ($login) {
            case "sydney":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "12-18";
                $this->StreetName = "Victoria St E";
                $this->City = "Lidcombe";
                $this->Province = "NSW";
                $this->Country = "AU";
                $this->PostalCode = "2141";
                $this->CountryCode = "61";
                $this->AreaCode = "61";
                $this->Phone = "";
                break;
            case "melbourne":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "6";
                $this->StreetName = "Ely Ct";
                $this->City = "Keilor East";
                $this->Province = "VIC";
                $this->Country = "AU";
                $this->PostalCode = "3033";
                $this->CountryCode = "61";
                $this->AreaCode = "61";
                $this->Phone = "";
                break;
            case "brisbane":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "1-23";
                $this->StreetName = "Lathe Street";
                $this->City = "Virginia";
                $this->Province = "QLD";
                $this->Country = "AU";
                $this->PostalCode = "4014";
                $this->CountryCode = "61";
                $this->AreaCode = "61";
                $this->Phone = "";
                break;
            case "perth":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "5/202";
                $this->StreetName = "Camboon Road";
                $this->City = "Malaga";
                $this->Province = "WA";
                $this->Country = "AU";
                $this->PostalCode = "6090";
                $this->CountryCode = "61";
                $this->AreaCode = "61";
                $this->Phone = "";
                break;

            default :
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "12-18";
                $this->StreetName = "Victoria St E";
                $this->City = "Lidcombe";
                $this->Province = "NSW";
                $this->Country = "AU";
                $this->PostalCode = "2141";
                $this->CountryCode = "61";
                $this->AreaCode = "61";
                $this->Phone = "";
                break;
        }
    }

}
?>