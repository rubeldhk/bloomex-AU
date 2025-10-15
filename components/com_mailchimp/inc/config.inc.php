<?php
    //API Key - see http://admin.mailchimp.com/account/api
    $apikey = '186b0717cb39956042ae8aa44400c5b3-us4';
    
    // A List Id to run examples against. use lists() to view all
    // Also, login to MC account, go to List, then List Tools, and look for the List ID entry
    $listId = 'b38b3484ab'; //List name = "Reminders"
    
    // A Campaign Id to run examples against. use campaigns() to view all
    $campaignId = 'YOUR MAILCHIMP CAMPAIGN ID - see campaigns() method';

    //some email addresses used in the examples:
  //  $my_email = 'iamx47mail@gmail.com';
    $my_email 		= 'marketing@bloomex.ca';
    $boss_man_email 	= 'marketing@bloomex.ca';

    //just used in xml-rpc examples
    $apiUrl = 'http://api.mailchimp.com/1.3/';
    
?>
