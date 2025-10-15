<?php

// This script developed with samples from around the internet, and the core is based on: Eric O'Callaghan's SMS Script.
// This script also uses Teleslip's SMS Service.

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Process the form if it was submitted
if (isset($_POST['do'])) {

	$mailfrom = $params->get( 'mailfrom', $mosConfig_mailfrom );

    // Parse the data submitted from the form
    $number = trim(mosGetParam( $_REQUEST, 'number', '' ));
    $subject = trim(mosGetParam( $_REQUEST, 'subject', '' ));
    $message = trim(mosGetParam( $_REQUEST, 'message', '' ));

    // Check that the phone number is numeric
    if (!is_numeric($number)) {
        $error = "Only Digits 0-9!";

    // Make sure the phone number doesn't start with 911
    } elseif (preg_match("/^(911)(.*)$/i", $message)) {
        $error = "No 911 Numbers!";

    // Check that the phone number is 10 digits
    } elseif (strlen($number) != "10") {
        $error = "10 Digits!";

    // Check that the subject doesn't contain HTML characters
    } elseif (preg_match("/^(.*)(<|>)(.*)$/i", $subject)) {
        $error = "No HTML!";

    // Check that the message doesn't contain HTML characters
    } elseif (preg_match("/^(.*)(<|>)(.*)$/i", $message)) {
        $error = "No HTML!";

    // Check that the subject is between 3 and 20 characters
    } elseif ((strlen($subject) < "3") || (strlen($subject) > "20")) {
        $error = "Subject 3 to 20 Characters!";

    // Check that the message is between 3 and 120 characters
    } elseif ((strlen($message) < "3") || (strlen($message) > "120")) {
        $error = "Message 3 to 120 Characters!";

    // Send the message
    } else {

        // Where are we sending it?
        $to = "" . $number . "@teleflip.com";
		
		// Who are we sending it from?
		$headers = 'From: ' . $mailfrom;

        // Send the text message (via Teleflip's service)
        if (@mail($to, $subject, $message, $headers)) {

            // Give a success notice
            echo "<script language=\"javascript\">alert('Your text message has been sent!');</script>";

        // Give an error that message can't be sent
        } else {
            $error = "Unexpected Error!";
        }
    }
}

// Show any errors encountered
if (isset($error)) {
    echo "<font color=\"red\"><b>" . $error . "</b></font><br><br>\n";
}

?>

<form method="POST" action="<?php echo $PHP_SELF; ?>">
Number:<br />
<input type="text" name="number" maxlength="10" class="inputbox"><br />
(Example: 8001234567; US Only)<br /><br />
Subject:<br />
<input type="text" name="subject" maxlength="20" class="inputbox"><br />
(Max 20 Characters)<br /><br />
Message:<br />
<textarea name="message" rows="10" cols="15" class="inputbox"></textarea><br />
(Between 3 - 120 Characters)<br /><br />
<input type="submit" name="do" value="Send" class="button">
</form>