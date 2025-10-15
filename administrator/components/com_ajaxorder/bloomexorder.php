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
            case "ottawa":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "4095";
                $this->StreetName = "BELGREEN DRIVE";
                $this->City = "OTTAWA";
                $this->Province = "ON";
                $this->Country = "CA";
                $this->PostalCode = "K1G 3N2";
                $this->CountryCode = "1";
                $this->AreaCode = "613";
                $this->Phone = "228-7673";
                break;
            case "toronto":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "902";
                $this->StreetName = "MAGNETIC DRIVE";
                $this->City = "TORONTO";
                $this->Province = "ON";
                $this->Country = "CA";
                $this->PostalCode = "M3J 3C4";
                $this->CountryCode = "1";
                $this->AreaCode = "416";
                $this->Phone = "739-0699";
                break;
            case "vancouver":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "#108-366";
                $this->StreetName = "KENT St S.";
                $this->City = "VANCOUVER";
                $this->Province = "BC";
                $this->Country = "CA";
                $this->PostalCode = "V5X 4N6";
                $this->CountryCode = "1";
                $this->AreaCode = "604";
                $this->Phone = "325-3066";
                break;
            case "montreal":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "5530";
                $this->StreetName = "RUE St PATRICK, SUITE 1120m";
                $this->City = "MONTREAL";
                $this->Province = "QC";
                $this->Country = "CA";
                $this->PostalCode = "H4E 1A8";
                $this->CountryCode = "1";
                $this->AreaCode = "514";
                $this->Phone = "807-6243";
                break;
            case "halifax":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "20";
                $this->StreetName = "WRIGHT AVENUE, UNIT 5";
                $this->City = "HALIFAX";
                $this->Province = "NS";
                $this->Country = "CA";
                $this->PostalCode = "B3B 1G6";
                $this->CountryCode = "1";
                $this->AreaCode = "888";
                $this->Phone = "912-5666";
                break;
            case "winnipeg":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "1119";
                $this->StreetName = "SANFORD St";
                $this->City = "WINNIPEG";
                $this->Province = "MB";
                $this->Country = "CA";
                $this->PostalCode = "R3E 3A1";
                $this->CountryCode = "1";
                $this->AreaCode = "204";
                $this->Phone = "772-3126";
                break;
            case "calgary":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "3850-32";
                $this->StreetName = "STREET NE";
                $this->City = "CALGARY";
                $this->Province = "AB";
                $this->Country = "CA";
                $this->PostalCode = "T1Y 7L9";
                $this->CountryCode = "1";
                $this->AreaCode = "403";
                $this->Phone = "769-1767";
                break;
            case "edmonton":
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "6067-88";
                $this->StreetName = "STREET NE";
                $this->City = "EDMONTON";
                $this->Province = "ALBERTA";
                $this->Country = "CA";
                $this->PostalCode = "T6E 5T4";
                $this->CountryCode = "1";
                $this->AreaCode = "780";
                $this->Phone = "466-8863";
                break;
            default :
                $this->Name = "BLOOMEX";
                $this->StreetNumber = "4095";
                $this->StreetName = "BELGREEN DRIVE";
                $this->City = "OTTAWA";
                $this->Province = "ON";
                $this->Country = "CA";
                $this->PostalCode = "K1G 3N2";
                $this->CountryCode = "1";
                $this->AreaCode = "613";
                $this->Phone = "228-7673";
                break;
        }
    }

}
?>