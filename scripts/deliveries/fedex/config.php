<?php
//require_once($_SERVER['DOCUMENT_ROOT'] . "/fedex/access.php");
if (isset($_REQUEST['wh'])) {
    setcookie('wh', $_REQUEST['wh']);
    $_COOKIE['wh'] = $_REQUEST['wh'];
}

if (!(class_exists('JConfig'))) {

    class JConfig {

        var $dbtype = 'mysql';
        var $host = '';
        var $user = '';
        var $password = '';
        var $db = '';

        function __construct() {
            include($_SERVER['DOCUMENT_ROOT'] . "/configuration.php");
            $this->dbtype = "mysql";
            $this->host = $mosConfig_host;
            $this->user = $mosConfig_user;
            $this->password = $mosConfig_password;
            $this->db = $mosConfig_db;
            $this->ftp_host = $mosConfig_email_sender_ftp_host;
            $this->ftp_login = $mosConfig_email_sender_ftp_login;
            $this->ftp_pass = $mosConfig_email_sender_ftp_pass;
        }

    }

}

/*
// test
// <UserCredentials>
  $FedexConfig['Key'] = 'yp3VxhgYTWDQfcdH';
  $FedexConfig['Password'] = 'refhOjLSuazrlSc3VS4Y180Qt';
  // <ClientDetail>
  $FedexConfig['AccountNumber'] = '510087984';
  $FedexConfig['MeterNumber'] = '100256864';

 prod 
// <UserCredentials>
$FedexConfig['Key'] = 'PUCFn1q8hLoNWsTM';
$FedexConfig['Password'] = 'g4OMpaoMetP0rzQSV3FyxweNv';
// <ClientDetail>
$FedexConfig['AccountNumber'] = '623746542';
$FedexConfig['MeterNumber'] = '108077870';
 */

// <TransactionDetail>
$FedexConfig['CustomerTransactionId'] = '*** Ground Domestic Shipping Request v12 using PHP ***';
if(isset($_COOKIE['wh'])){
    
switch ($_COOKIE['wh']) {
    
    case "WH01":
        $FedexConfig['PersonName'] = "Natalya";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "4095";
        $FedexConfig['StreetLines2'] = "BELGREEN DRIVE";
        $FedexConfig['City'] = "OTTAWA";
        $FedexConfig['StateOrProvinceCode'] = "ON";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "K1G 3N2";
        $FedexConfig['PhoneNumber'] = "613 2287673";
        // <UserCredentials>
        $FedexConfig['Key'] = 'PUCFn1q8hLoNWsTM';
        $FedexConfig['Password'] = 'g4OMpaoMetP0rzQSV3FyxweNv';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '623746542';
        $FedexConfig['MeterNumber'] = '108077870';

        break;
    
    case "WH02":
        $FedexConfig['PersonName'] = "Bobby";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "902";
        $FedexConfig['StreetLines2'] = "MAGNETIC DRIVE";
        $FedexConfig['City'] = "TORONTO";
        $FedexConfig['StateOrProvinceCode'] = "ON";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "M3J 3C4";
        $FedexConfig['PhoneNumber'] = "416 7390699";
        // <UserCredentials>
        $FedexConfig['Key'] = '5YEk9kE7NY08FM0g';
        $FedexConfig['Password'] = 'DnUrMXwWGjchlpeKTzUWd7K1m';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '627320469';
        $FedexConfig['MeterNumber'] = '110294624';

        break;
    
    case "WH06":
        $FedexConfig['PersonName'] = "Thomas";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "108";
        $FedexConfig['StreetLines2'] = "366 KENT St S.";
        $FedexConfig['City'] = "VANCOUVER";
        $FedexConfig['StateOrProvinceCode'] = "BC";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "V5X 4N6";
        $FedexConfig['PhoneNumber'] = "604 3253066";
        // <UserCredentials>
        $FedexConfig['Key'] = 'LlWws2PM6LHFmEJt';
        $FedexConfig['Password'] = 'UHMPC32t8KKV7PpFHhNWEyqD4';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '628377502';
        $FedexConfig['MeterNumber'] = '110303064';
        
        break;
    
    case "WH04":
        $FedexConfig['PersonName'] = "Anca";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "5530";
        $FedexConfig['StreetLines2'] = "RUE St PATRICK, SUITE 1120m";
        $FedexConfig['City'] = "MONTREAL";
        $FedexConfig['StateOrProvinceCode'] = "QC";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "H4E 1A8";
        $FedexConfig['PhoneNumber'] = "514 8076243";
        // <UserCredentials>
        $FedexConfig['Key'] = 'MBVUbUx2yofSap1J';
        $FedexConfig['Password'] = 'u90IKIKxP99ZzC60V1svqjdtj';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '209803690';
        $FedexConfig['MeterNumber'] = '110339387';
        
        break;
    
    case "WH07":
        $FedexConfig['PersonName'] = "Pam";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "20B Rosedale Dr";
        $FedexConfig['StreetLines2'] = "";
        $FedexConfig['City'] = "DARTMOUTH";
        $FedexConfig['StateOrProvinceCode'] = "NS";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "B3A 1L8";
        $FedexConfig['PhoneNumber'] = "888 9125666";
        // <UserCredentials>
        $FedexConfig['Key'] = 'K9qj9IdvmrwqdQHV';
        $FedexConfig['Password'] = '6rkF5yE2jSxRWg1Wro44oik3P';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '627757425';
        $FedexConfig['MeterNumber'] = '110303058';
        
        break;

    case "WH08":
        $FedexConfig['PersonName'] = "Leslie";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "1119";
        $FedexConfig['StreetLines2'] = "SANFORD St";
        $FedexConfig['City'] = "WINNIPEG";
        $FedexConfig['StateOrProvinceCode'] = "MB";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "R3E 3A1";
        $FedexConfig['PhoneNumber'] = "204 7723126";
        // <UserCredentials>
        $FedexConfig['Key'] = 'lHiAkyD8QtzkNuMG';
        $FedexConfig['Password'] = '0NSSDOC7miDgyIwQj4fGpLQ4I';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '627884001';
        $FedexConfig['MeterNumber'] = '110294630';
        
        break;

    case "WH03":
        $FedexConfig['PersonName'] = "Eugina";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "3850";
        $FedexConfig['StreetLines2'] = "32 STREET NE";
        $FedexConfig['City'] = "CALGARY";
        $FedexConfig['StateOrProvinceCode'] = "AB";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "T1Y 7L9";
        $FedexConfig['PhoneNumber'] = "403 7691767";
        // <UserCredentials>
        $FedexConfig['Key'] = 'IpbMSKctVKVTPBpC';
        $FedexConfig['Password'] = 'xpE0g1BDnqRSOBuszuD5dZ8g9';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '627242603';
        $FedexConfig['MeterNumber'] = '110294637';
        
        break;

    case "WH10":
        $FedexConfig['PersonName'] = "Danelle";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "6067";
        $FedexConfig['StreetLines2'] = "88 STREET NW";
        $FedexConfig['City'] = "EDMONTON";
        $FedexConfig['StateOrProvinceCode'] = "AB";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "T6E 5T4";
        $FedexConfig['PhoneNumber'] = "780 4668863";
        // <UserCredentials>
        $FedexConfig['Key'] = '0wxRUV7uiKX5ljyF';
        $FedexConfig['Password'] = 'cPjxHIgAh41ZWcek7n9erBRJl';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '627458746';
        $FedexConfig['MeterNumber'] = '110294641';
        
        break;

    case "WH09":
        //Supported Web Services:	 FedEx Web Services for Shipping
//Authentication Key:	 8yEHpgXtsQShxfGr
//Meter Number:	 114596574 a
//Account Number 912681815 
        
        $FedexConfig['PersonName'] = "Teresa";
        $FedexConfig['CompanyName'] = 'Bloomex';
        $FedexConfig['StreetLines1'] = "Unit J";
        $FedexConfig['StreetLines2'] = "120 Turnbull Crt";
        $FedexConfig['City'] = "Cambridge";
        $FedexConfig['StateOrProvinceCode'] = "ON";
        $FedexConfig['CountryCode'] = "CA";
        $FedexConfig['PostalCode'] = "N1T 1H9";
        $FedexConfig['PhoneNumber'] = "519 6249242";
        // <UserCredentials>
        $FedexConfig['Key'] = '8yEHpgXtsQShxfGr';
        $FedexConfig['Password'] = 'MjFxUdgl8oylWeKWzjJC6FpM6';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '912681815';
        $FedexConfig['MeterNumber'] = '114596574';

        break;
    default :
        // <UserCredentials>
        $FedexConfig['Key'] = 'PUCFn1q8hLoNWsTM';
        $FedexConfig['Password'] = 'g4OMpaoMetP0rzQSV3FyxweNv';
        // <ClientDetail>
        $FedexConfig['AccountNumber'] = '623746542';
        $FedexConfig['MeterNumber'] = '108077870';
    //   echo "<br/>UNKNOWN WAREHOUSE: " . $login . "<br/><input  type='button' name='Close' value=' Close Window ' onclick='window.close();' /></center>";
}

}



// order status
$FedexConfig['fedex_status'] = 'G';
$FedexConfig['status_cancel'] = 'V';


//form widht
$FedexConfig['myForm'] = '480px';

// map size
$FedexConfig['map_width'] = '800px';
$FedexConfig['map_height'] = '600px';

//radius for search
$FedexConfig['radius'] = '15';

//Results Requested
$FedexConfig['return_count'] = 10; //0-all records
