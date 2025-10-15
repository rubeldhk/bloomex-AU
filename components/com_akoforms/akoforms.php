<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.11 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

# Don't allow direct linking
  defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

# Load some configuation files
  require($mosConfig_absolute_path."/administrator/components/com_akoforms/fields.akoforms.php");
  require($mosConfig_absolute_path."/administrator/components/com_akoforms/class.akoforms.php");
  require($mosConfig_absolute_path."/administrator/components/com_akoforms/config.akoforms.php");
  $akoversion = "V1.11 final";

# Load language if it exists
  if (file_exists($mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php')) {
    include($mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php');
  } else {
    include($mosConfig_absolute_path.'/components/com_akoforms/languages/english.php');
  }

switch ($func) {

############################################################################

  case 'showform':
    # Load form from database
    $category = new mosAkoforms( $database );
    $category->load( $formid );

    echo "<p class='componentheading'>$category->title</p>";

    if ($category->publish_down < date("Y-m-d",time()) && trim( $category->publish_down ) != "0000-00-00 00:00:00") {
      echo $AKFLANG->AKF_FORMEXPIRED;
    } else if ($category->publish_up > date("Y-m-d",time())." 00:00:00" ) {
      echo $AKFLANG->AKF_FORMPENDING;
    } else {
      # Show form
      echo "<p>$category->text</p>";
      # Load fields from database
      $database->setQuery( "SELECT * FROM #__akoforms_fields"
      . "\n WHERE published = '1'"
      . "\n   AND catid = '$category->id'"
      . "\n   AND (publish_up = '0000-00-00 00:00:00' OR publish_up <= NOW())"
      . "\n   AND (publish_down = '0000-00-00 00:00:00' OR publish_down >= NOW())"
      . "\n ORDER BY ordering"
      );
      $fields = $database->loadObjectList();

      # Form Validation
      echo "<script language='javascript' type='text/javascript'>\n";
      echo "  function akovalidate() {\n";
      echo "   var form = document.akoform;\n";
      echo "    if (form.formid.value == \"\"){\n";
      echo "      alert( \"FormID is missing.\" );\n";
      foreach($fields as $field) {
        if ($field->required) {
          echo "    } else if (form.AKF".$field->id.".value == \"\"){\n";
          echo "      alert( \"".$AKFLANG->AKF_PLEASEFILLREQFIELD." ".$field->title."\" );\n";
        }
      }
      echo "    } else {\n";
      echo "      document.akoform.submit();";
      echo "    } } </script>\n";

      echo "<form action='".sefRelToAbs("index.php?option=com_akoforms&func=processform&Itemid=$itemid")."' name='akoform' id='akoform' method='post' enctype='multipart/form-data'>";
      echo "<input type='hidden' value='$category->id' name='formid'>";

      # AkoForms Template Engine Start
      echo $file_akf_layoutstart;
      # AkoForms Template Engine Rows
      foreach($fields as $field) {
        # Execute Field PreProcessor
        if ($formfields[$field->type][field_php]) eval ($formfields[$field->type][field_php]);
        # Compile Field
        $af_output = $formfields[$field->type][field_html];
        $af_output = str_replace( "af_fieldvalue" , $field->value , $af_output );
        $af_output = str_replace( "af_fieldstyle" , $field->style , $af_output );
        $af_output = str_replace( "af_fieldname"  , "AKF".$field->id , $af_output );
        # Compile Row
        $af_outputrow = $file_akf_layoutrow;
        $af_outputrow = str_replace( "###AFTFIELDTITLE###"  , $field->title , $af_outputrow );
        $af_outputrow = $field->required ? str_replace( "###AFTFIELDREQ###"  , "<span class='small'>*</span>" , $af_outputrow ) : str_replace( "###AFTFIELDREQ###"  , "" , $af_outputrow );
        $af_outputrow = str_replace( "###AFTFIELDDESC###"  , $field->text , $af_outputrow );
        $af_outputrow = str_replace( "###AFTFIELD###"  , $af_output , $af_outputrow );
        echo $af_outputrow;
      }
      # AkoForms Template Engine End
      $file_akf_layoutend = str_replace( "###AFTSENDBUTTON###"  , "<input type='button' name='send' value='".$AKFLANG->AKF_BUTTONSEND."' class='button' onclick='akovalidate()' />" , $file_akf_layoutend );
      $file_akf_layoutend = str_replace( "###AFTCLEARBUTTON###"  , "<input type='reset' value='".$AKFLANG->AKF_BUTTONCLEAR."' name='reset' class='button'>" , $file_akf_layoutend );
      echo $file_akf_layoutend;

      echo "</form>";
      echo "<p align='center' class='small'>* ".$AKFLANG->AKF_REQUIREDFIELD."</p>";
    }
    break;

############################################################################

  case 'processform':
    # Load form from database
    $category = new mosAkoforms( $database );
    $category->load( $formid );

    # Fetch input from submitted form
    $akfip   = getenv("REMOTE_ADDR");
    $akfdate = date ( "Y-m-d H:i:s" , time() );
    foreach($_POST as $key=>$elem) {
      if (is_array($elem)) $elem = implode( ',', $elem );
      $akofieldinput[substr($key,3)] = $elem;
    }

    # Load fields from database
    $database->setQuery( "SELECT * FROM #__akoforms_fields"
    . "\n WHERE published = '1'"
    . "\n   AND catid = '$category->id'"
    . "\n   AND (publish_up = '0000-00-00 00:00:00' OR publish_up <= NOW())"
    . "\n   AND (publish_down = '0000-00-00 00:00:00' OR publish_down >= NOW())"
    . "\n ORDER BY ordering"
    );
    $fields = $database->loadObjectList();

    # Save sender to database
    if ($category->savedb) {
      $database->setQuery( "INSERT INTO #__akoforms_sender (`id` , `formid` , `senderip` , `senderdate`) VALUES ('','$category->id' , '$akfip' , '$akfdate' );" );
      $result = $database->query();
    }
    $akoforms_senderid = mysql_insert_id();

    #Initialise Mail Handler
    require_once($mosConfig_absolute_path."/components/com_akoforms/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->Mailer   = "mail";
    $mail->Subject  = $file_akf_mailsubject;
    $mail->CharSet  = $file_akf_mailcharset;
    $mail->FromName = $file_akf_mailsender;
    $mail->From     = $file_akf_mailemail;
    if ($category->emails) $akfmailarray = explode ( "," , $category->emails );

    # Handling
    $line = 0;
    $tabclass = array("sectiontableentry1", "sectiontableentry2");
    $akfmailhtml  = "<table width='100%' cellpadding='4' cellspacing='0' border='0' align='center' class='contentpane'>";
    $akfmailhtml .= "<tr><td width='30%' class='sectiontableheader'>".$AKFLANG->AKF_MAILTABLEFIELD."</td><td width='70%' class='sectiontableheader'>".$AKFLANG->AKF_MAILTABLEDATA."</td></tr>";
    foreach($fields as $field) {
      # Post Processor
      if ($formfields[$field->type][field_post]) {
        $post_processor = str_replace( "af_fieldname"  , "AKF".$field->id , $formfields[$field->type][field_post] );
        eval ($post_processor);
      }

      # Save data for later emailing
      $stfieldinput = stripslashes($akofieldinput[$field->id]);
      $akfmailform .= $field->title.": $stfieldinput\n";
      $stfieldinput = preg_replace("/(\015\012)|(\015)|(\012)/","&nbsp;<br />",$stfieldinput);
      $akfmailhtml .= "<tr><td width='30%' valign='top' class='".$tabclass[$line]."'><b>$field->title</b></td>";
      $akfmailhtml .= "<td width='70%' valign='top' class='".$tabclass[$line]."'>$stfieldinput</td></tr>";

      # Save data to database
      if ($category->savedb) {
        $afsavevalue = mysql_escape_string($akofieldinput[$field->id]);
        $database->setQuery( "INSERT INTO #__akoforms_data (`id` , `senderid` , `fieldid` , `data` )"
        ."\n VALUES ('', '$akoforms_senderid', '$field->id' , '$afsavevalue' );" );
        $result = $database->query();
      }
      $line = 1 - $line;

    }
    $akfmailhtml .= "</table>";

    // HTML body
    $akfemailhtml  = "<style>";
    $akfemailhtml .= ".sectiontableheader {".$file_akf_mailheadcss."}";
    $akfemailhtml .= ".sectiontableentry1 {".$file_akf_mailrow1css."}";
    $akfemailhtml .= ".sectiontableentry2 {".$file_akf_mailrow2css."}";
    $akfemailhtml .= "</style>";
    $akfemailhtml .= "<p>".$AKFLANG->AKF_MAILERHELLO."</p>";
    $akfemailhtml .= "<p>".$AKFLANG->AKF_MAILERHEADER."</p>";
    $akfemailhtml .= $akfmailhtml;
    $akfemailhtml .= "<p>".$AKFLANG->AKF_MAILERFOOTER."</p>";

    // Plain text body (for mail clients that cannot read HTML)
    $akfmailtext  = $AKFLANG->AKF_MAILERHELLO."\n\n";
    $akfmailtext .= $AKFLANG->AKF_MAILERHEADER."\n\n";
    $akfmailtext .= $akfmailform;
    $akfmailtext .= "\n".$AKFLANG->AKF_MAILERFOOTER."\n";

    # Send out Emails
    $mail->Body     = $akfemailhtml;
    $mail->AltBody  = $akfmailtext;
    if ($category->sendmail) {
      if (count($akfmailarray) > 0) {
        foreach($akfmailarray as $akfmail) {
          $mail->ClearAddresses();
          $mail->AddAddress( $akfmail );
          if(!$mail->Send()) echo $AKFLANG->AKF_MAILERERROR." $akfmail<br />";
        }
      }
    }

    # Finishing
    if ($category->target=='1') {
      echo "<SCRIPT>document.location.href='$category->targeturl';</SCRIPT>";
    } else {
      echo "<p class='componentheading'>$category->thanktitle</p>";
      echo "<p>$category->thanktext</p>";
      if ($category->showresult=='1') {
        echo $akfmailhtml;
      }
    }
    break;

############################################################################

  default:
    # Get page title
    $database->setQuery("SELECT name from #__menu WHERE id='$Itemid'");
    $menuname = $database->loadResult();
    echo "<p><span class='componentheading'>$menuname</span></p>";
    # Load forms from database
    $database->setQuery( "SELECT * FROM #__akoforms"
    . "\n WHERE published = '1'"
    . "\n   AND (publish_up = '0000-00-00 00:00:00' OR publish_up <= NOW())"
    . "\n   AND (publish_down = '0000-00-00 00:00:00' OR publish_down >= NOW())"
    . "\n ORDER BY ordering"
    );
    $forms = $database->loadObjectList();
    # Bring them to the screen
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='4'>";
    echo "<tr><td width='100%' height='20' class='sectiontableheader'>$menuname</td></tr>";
    $line=1;
    foreach($forms as $form) {
      $linecolor = ($line % 2) + 1;
      echo "<tr class='sectiontableentry".$linecolor."'><td width='100%' valign='top'>";
      echo "<a href='".sefRelToAbs("index.php?option=com_akoforms&func=showform&formid=$form->id&Itemid=$Itemid'>$form->title")."</a><br />$form->text";
      echo "</td></tr>";
      $line++;
    }
    echo "</table>";
    break;
}


?>