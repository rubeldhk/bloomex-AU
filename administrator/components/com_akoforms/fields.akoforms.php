<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

/**
* WARNING: DO NOT CHANGE ANYTHING INSIDE THIS FILE, IT'S ESSENTIAL FOR SMOOTH OPERATING!!!
* IF YOU LIKE TO ADD CUSTOM FIELDS, DO IT IN THE fields_custom.akoforms.php FILE!!!
* IF YOU NEED HELP OR CUSTOMIZATIONS, MAIL webmaster[at]konze.de!!!
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$formfields[98] = array (
  "field_title" => "Checkbox (Multiple)",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) \$tmp_value = \$tmp_value.\"<input type='checkbox' name='af_fieldname[]' value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"' style='af_fieldstyle' /> \$tmp_elem<br />\"; \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "af_fieldvalue"
);

$formfields[100] = array (
  "field_title" => "Checkbox (Single)",
  "field_html"  => "<input type='checkbox' name='af_fieldname' style='af_fieldstyle' /> af_fieldvalue"
);

$formfields[104] = array (
  "field_title" => "Dropdown",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) \$tmp_value = \$tmp_value.\"<option value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'>\$tmp_elem</option>\"; \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>af_fieldvalue</select>"
);

$formfields[105] = array (
  "field_title" => "File Upload (Attachment)",
  "field_html"  => "<input type='file' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />",
  "field_post"  => "if (isset(\$_FILES['af_fieldname']) and ! \$_FILES['af_fieldname']['error']) \$mail->AddAttachment(\$_FILES['af_fieldname']['tmp_name'],\$_FILES['af_fieldname']['name']); \$akofieldinput[\$field->id]=\$_FILES['af_fieldname']['name'];"
);

$formfields[106] = array (
  "field_title" => "File Upload (Store)",
  "field_html"  => "<input type='file' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />",
  "field_post"  => "if (isset(\$_FILES['af_fieldname']) and ! \$_FILES['af_fieldname']['error']) move_uploaded_file(\$_FILES['af_fieldname']['tmp_name'], \$mosConfig_absolute_path.\"/uploadfiles/\".\$_FILES['af_fieldname']['name']); \$akofieldinput[\$field->id]=\$_FILES['af_fieldname']['name'];"
);

$formfields[108] = array (
  "field_title" => "Hidden Field",
  "field_html"  => "<input type='hidden' name='af_fieldname' style='af_fieldstyle' value='af_fieldvalue' />"
);

$formfields[112] = array (
  "field_title" => "Inputbox",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />"
);

$formfields[116] = array (
  "field_title" => "Passwordbox",
  "field_html"  => "<input type='password' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />"
);

$formfields[120] = array (
  "field_title" => "Radiobutton",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_key => \$tmp_elem ) { \$tmp_value = \$tmp_value.\"<input type='radio' name='af_fieldname' value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'\"; if (\$tmp_key==0) \$tmp_value = \$tmp_value.\" checked\"; \$tmp_value = \$tmp_value.\"> \$tmp_elem<br />\"; } \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "af_fieldvalue"
);

$formfields[124] = array (
  "field_title" => "Selectbox (Multiple)",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) \$tmp_value = \$tmp_value.\"<option value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'>\$tmp_elem</option>\"; \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "<select size='5' name='af_fieldname[]' class='inputbox' style='af_fieldstyle' multiple>af_fieldvalue</select>"
);

$formfields[128] = array (
  "field_title" => "Selectbox (Single)",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) \$tmp_value = \$tmp_value.\"<option value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'>\$tmp_elem</option>\"; \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "<select size='5' name='af_fieldname' class='inputbox' style='af_fieldstyle'>af_fieldvalue</select>"
);

$formfields[132] = array (
  "field_title" => "Textbox",
  "field_html"  => "<textarea name='af_fieldname' class='inputbox' style='af_fieldstyle'>af_fieldvalue</textarea>"
);

/**
* AkoForms special fields
**/

$formfields[200] = array (
  "field_title" => "Date Popup: Standard (Y-M-D)",
  "field_html"  => "<input type=\"text\" class=\"inputbox\" name=\"af_fieldname\" id=\"af_fieldname\" style=\"af_fieldstyle\" value=\"af_fieldvalue\">
  <script type=\"text/javascript\" src=\"includes/js/mambojavascript.js\"></script>
  <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"includes/js/calendar/calendar-mos.css\" title=\"green\" />
  <script type=\"text/javascript\" src=\"includes/js/calendar/calendar.js\"></script>
  <script type=\"text/javascript\" src=\"includes/js/calendar/lang/calendar-en.js\"></script>
  <input name=\"reset\" type=\"reset\" class=\"button\" onClick=\"return showCalendar('af_fieldname');\" value=\"...\">"
);

$formfields[204] = array (
  "field_title" => "Date Popup: European (D.M.Y)",
  "field_html"  => "<input type=\"text\" class=\"inputbox\" name=\"af_fieldname\" id=\"af_fieldname\" style=\"af_fieldstyle\" value=\"af_fieldvalue\">
  <script type=\"text/javascript\" src=\"includes/js/mambojavascript.js\"></script>
  <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"includes/js/calendar/calendar-mos.css\" title=\"green\" />
  <script type=\"text/javascript\" src=\"includes/js/calendar/calendar.js\"></script>
  <script type=\"text/javascript\" src=\"includes/js/calendar/lang/calendar-en.js\"></script>
  <script type=\"text/javascript\">Calendar._TT[\"DEF_DATE_FORMAT\"] = \"dd.mm.y\";</script>
  <input name=\"reset\" type=\"reset\" class=\"button\" onClick=\"return showCalendar('af_fieldname');\" value=\"...\">"
);

$formfields[208] = array (
  "field_title" => "Date Popup: American (M/D/Y)",
  "field_html"  => "<input type=\"text\" class=\"inputbox\" name=\"af_fieldname\" id=\"af_fieldname\" style=\"af_fieldstyle\" value=\"af_fieldvalue\">
  <script type=\"text/javascript\" src=\"includes/js/mambojavascript.js\"></script>
  <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"includes/js/calendar/calendar-mos.css\" title=\"green\" />
  <script type=\"text/javascript\" src=\"includes/js/calendar/calendar.js\"></script>
  <script type=\"text/javascript\" src=\"includes/js/calendar/lang/calendar-en.js\"></script>
  <script type=\"text/javascript\">Calendar._TT[\"DEF_DATE_FORMAT\"] = \"mm/dd/y\";</script>
  <input name=\"reset\" type=\"reset\" class=\"button\" onClick=\"return showCalendar('af_fieldname');\" value=\"...\">"
);

$formfields[250] = array (
  "field_title" => "Email: Sender Name Field",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$mail->FromName=\$akofieldinput[\$field->id];"
);

$formfields[254] = array (
  "field_title" => "Email: Sender Email Field",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$mail->From=\$akofieldinput[\$field->id];"
);

$formfields[258] = array (
  "field_title" => "Email: Subject Field",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='af_fieldvalue' />",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$mail->Subject=\$akofieldinput[\$field->id];"
);

$formfields[262] = array (
  "field_title" => "Email: Subject Dropdown",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) \$tmp_value = \$tmp_value.\"<option value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'>\$tmp_elem</option>\"; \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>af_fieldvalue</select>",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$mail->Subject=\$akofieldinput[\$field->id];"
);

$formfields[266] = array (
  "field_title" => "Email: Subject Radiobutton",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_key => \$tmp_elem ) { \$tmp_value = \$tmp_value.\"<input type='radio' name='af_fieldname' value='\".htmlspecialchars(\$tmp_elem, ENT_QUOTES).\"'\"; if (\$tmp_key==0) \$tmp_value = \$tmp_value.\" checked\"; \$tmp_value = \$tmp_value.\"> \$tmp_elem<br />\"; } \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "af_fieldvalue",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$mail->Subject=\$akofieldinput[\$field->id];"
);

$formfields[270] = array (
  "field_title" => "Email: Receiver Checkbox Multiple",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) { \$tmp_splitelem = explode ('|', \$tmp_elem); \$tmp_value = \$tmp_value.\"<input type='checkbox' name='af_fieldname[]' value='\".\$tmp_splitelem[0].\"' style='af_fieldstyle' /> \$tmp_splitelem[1]<br />\"; } \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "af_fieldvalue",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$akfmailarray = array_merge(\$akfmailarray, explode(',',\$akofieldinput[\$field->id]));"
);

$formfields[274] = array (
  "field_title" => "Email: Receiver Dropdown",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_elem ) { \$tmp_splitelem = explode ('|', \$tmp_elem); \$tmp_value = \$tmp_value.\"<option value='\".\$tmp_splitelem[0].\"'>\$tmp_splitelem[1]</option>\"; } \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>af_fieldvalue</select>",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$akfmailarray[]=\$akofieldinput[\$field->id];"
);

$formfields[278] = array (
  "field_title" => "Email: Receiver Radiobutton",
  "field_php"   => "\$tmp_array = explode ('\n', \$field->value); foreach(\$tmp_array as \$tmp_key => \$tmp_elem ) { \$tmp_splitelem = explode ('|', \$tmp_elem); \$tmp_value = \$tmp_value.\"<input type='radio' name='af_fieldname' value='\".\$tmp_splitelem[0].\"'\"; if (\$tmp_key==0) \$tmp_value = \$tmp_value.\" checked\"; \$tmp_value = \$tmp_value.\"> \$tmp_splitelem[1]<br />\"; } \$field->value = \$tmp_value; \$tmp_value='';",
  "field_html"  => "af_fieldvalue",
  "field_post"  => "if (\$akofieldinput[\$field->id]) \$akfmailarray[]=\$akofieldinput[\$field->id];"
);


/**
* Mambo special fields
**/

$formfields[300] = array (
  "field_title" => "Mambo Username",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='$my->username' />"
);

$formfields[304] = array (
  "field_title" => "Mambo Usertype",
  "field_html"  => "<input type='text' name='af_fieldname' class='inputbox' style='af_fieldstyle' value='$my->usertype' />"
);

/**
* Predefined fields
**/

$formfields[400] = array (
  "field_title" => "World Timezones",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Choose Timezone</option>
  <option value='-12'>(GMT -12) Eniwetok, Kwajalein</option>
  <option value='-11'>(GMT -11) Midway Island, Samoa</option>
  <option value='-10'>(GMT -10) Hawaii</option>
  <option value='-9'>(GMT -9) Alaska</option>
  <option value='-8'>(GMT -8) Pacific Time (US & Canada)</option>
  <option value='-7'>(GMT -7) Mountain Time (US & Canada)</option>
  <option value='-6'>(GMT -6) Central Time (US & Canada)</option>
  <option value='-5'>(GMT -5) Eastern Time (US & Canada)</option>
  <option value='-4'>(GMT -4) Atlantic Time (Canada)</option>
  <option value='-3.5'>(GMT -3.5)Newfoundland</option>
  <option value='-3'>(GMT -3) Buenos Aires, Georgetown</option>
  <option value='-2'>(GMT -2) Mid-Atlantic, AscensionIs</option>
  <option value='-1'>(GMT -1) Azores, Cape Verde Islands</option>
  <option value='0'>(GMT) Casablanca, Dublin, London, Lisbon</option>
  <option value='1'>(GMT +1) Amsterdam, Berlin, Madrid, Paris</option>
  <option value='2'>(GMT +2) Cairo, Helsinki, South Africa</option>
  <option value='3'>(GMT +3) Baghdad, Riyadh, Moscow, Nairobi</option>
  <option value='3.5'>(GMT +3.5) Tehran</option>
  <option value='4'>(GMT +4) Abu Dhabi, Baku, Muscat, Tbilisi</option>
  <option value='4.5'>(GMT +4.5) Kabul</option>
  <option value='5'>(GMT +5) Islamabad, Karachi, Tashkent</option>
  <option value='5.5'>(GMT +5.5) Bombay, Calcutta, New Delhi</option>
  <option value='6'>(GMT +6) Almaty, Colombo, Novosibirsk</option>
  <option value='6.5'>(GMT +6.5) Rangoon</option>
  <option value='7'>(GMT +7) Bangkok, Hanoi, Jakarta</option>
  <option value='8'>(GMT +8) Beijing, Hong Kong, Singapore</option>
  <option value='9'>(GMT +9) Osaka, Sapporo, Seoul, Tokyo</option>
  <option value='9.5'>(GMT +9.5) Adelaide, Darwin</option>
  <option value='10'>(GMT +10) Guam, Melbourne, Sydney</option>
  <option value='11'>(GMT +11) Magadan, New Caledonia</option>
  <option value='12'>(GMT +12) Auckland, Fiji, Marshall Island</option>
  </select>"
);

$formfields[404] = array (
  "field_title" => "24 Hours Time",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Choose Time</option>
  <option value='00:00'>00:00</option>
  <option value='01:00'>01:00</option>
  <option value='02:00'>02:00</option>
  <option value='03:00'>03:00</option>
  <option value='04:00'>04:00</option>
  <option value='05:00'>05:00</option>
  <option value='06:00'>06:00</option>
  <option value='07:00'>07:00</option>
  <option value='08:00'>08:00</option>
  <option value='09:00'>09:00</option>
  <option value='10:00'>10:00</option>
  <option value='11:00'>11:00</option>
  <option value='12:00'>12:00</option>
  <option value='13:00'>13:00</option>
  <option value='14:00'>14:00</option>
  <option value='15:00'>15:00</option>
  <option value='16:00'>16:00</option>
  <option value='17:00'>17:00</option>
  <option value='18:00'>18:00</option>
  <option value='19:00'>19:00</option>
  <option value='20:00'>20:00</option>
  <option value='21:00'>21:00</option>
  <option value='22:00'>22:00</option>
  <option value='23:00'>23:00</option>
  </select>"
);

$formfields[408] = array (
  "field_title" => "12 Hours Time",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Choose Time</option>
  <option value='12:00 AM'>12:00 AM</option>
  <option value='01:00 AM'>01:00 AM</option>
  <option value='02:00 AM'>02:00 AM</option>
  <option value='03:00 AM'>03:00 AM</option>
  <option value='04:00 AM'>04:00 AM</option>
  <option value='05:00 AM'>05:00 AM</option>
  <option value='06:00 AM'>06:00 AM</option>
  <option value='07:00 AM'>07:00 AM</option>
  <option value='08:00 AM'>08:00 AM</option>
  <option value='09:00 AM'>09:00 AM</option>
  <option value='10:00 AM'>10:00 AM</option>
  <option value='11:00 AM'>11:00 AM</option>
  <option value='12:00 PM'>12:00 PM</option>
  <option value='01:00 PM'>01:00 PM</option>
  <option value='02:00 PM'>02:00 PM</option>
  <option value='03:00 PM'>03:00 PM</option>
  <option value='04:00 PM'>04:00 PM</option>
  <option value='05:00 PM'>05:00 PM</option>
  <option value='06:00 PM'>06:00 PM</option>
  <option value='07:00 PM'>07:00 PM</option>
  <option value='08:00 PM'>08:00 PM</option>
  <option value='09:00 PM'>09:00 PM</option>
  <option value='10:00 PM'>10:00 PM</option>
  <option value='11:00 PM'>11:00 PM</option>
  </select>"
);

$formfields[420] = array (
  "field_title" => "World Currencies",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Choose Currency</option>
  <option value='AFA'>Afghanistan Afghani</option>
  <option value='ALL'>Albanian Lek</option>
  <option value='DZD'>Algerian Dinar</option>
  <option value='ADF'>Andorran Franc</option>
  <option value='ADP'>Andorran Peseta</option>
  <option value='AON'>Angolan New Kwanza</option>
  <option value='ARS'>Argentine Peso</option>
  <option value='AWG'>Aruban Florin</option>
  <option value='AUD'>Australian Dollar</option>
  <option value='BSD'>Bahamian Dollar</option>
  <option value='BHD'>Bahraini Dinar</option>
  <option value='BDT'>Bangladeshi Taka</option>
  <option value='BBD'>Barbados Dollar</option>
  <option value='BZD'>Belize Dollar</option>
  <option value='BMD'>Bermudian Dollar</option>
  <option value='BTN'>Bhutan Ngultrum</option>
  <option value='BOB'>Bolivian Boliviano</option>
  <option value='BWP'>Botswana Pula</option>
  <option value='BRL'>Brazilian Real</option>
  <option value='GBP'>British Pound</option>
  <option value='BND'>Brunei Dollar</option>
  <option value='BGL'>Bulgarian Lev</option>
  <option value='BIF'>Burundi Franc</option>
  <option value='KHR'>Cambodian Riel</option>
  <option value='CAD'>Canadian Dollar</option>
  <option value='CVE'>Cape Verde Escudo</option>
  <option value='KYD'>Cayman Islands Dollar</option>
  <option value='CLP'>Chilean Peso</option>
  <option value='CNY'>Chinese Yuan Renminbi</option>
  <option value='COP'>Colombian Peso</option>
  <option value='KMF'>Comoros Franc</option>
  <option value='CRC'>Costa Rican Colon</option>
  <option value='HRK'>Croatian Kuna</option>
  <option value='CUP'>Cuban Peso</option>
  <option value='CYP'>Cyprus Pound</option>
  <option value='CZK'>Czech Koruna</option>
  <option value='DKK'>Danish Krone</option>
  <option value='DJF'>Djibouti Franc</option>
  <option value='DOP'>Dominican R. Peso</option>
  <option value='XCD'>East Caribbean Dollar</option>
  <option value='ECS'>Ecuador Sucre</option>
  <option value='EGP'>Egyptian Pound</option>
  <option value='SVC'>El Salvador ColonC</option>
  <option value='EEK'>Estonian Kroon</option>
  <option value='ETB'>Ethiopian Birr</option>
  <option value='EUR'>European Euro</option>
  <option value='FKP'>Falkland Islands Pound</option>
  <option value='FJD'>Fiji Dollar</option>
  <option value='GMD'>Gambian Dalasi</option>
  <option value='GHC'>Ghanaian Cedi</option>
  <option value='GIP'>Gibraltar Pound</option>
  <option value='GRD'>Greek Drachma</option>
  <option value='GTQ'>Guatemalan Quetzal</option>
  <option value='GYD'>Guyanese Dollar</option>
  <option value='HTG'>Haitian Gourde</option>
  <option value='HNL'>Honduran Lempira</option>
  <option value='HKD'>Hong Kong Dollar</option>
  <option value='HUF'>Hungarian Forint</option>
  <option value='ISK'>Iceland Krona</option>
  <option value='INR'>Indian Rupee</option>
  <option value='IDR'>Indonesian Rupiah</option>
  <option value='IRR'>Iranian Rial</option>
  <option value='IQD'>Iraqi Dinar</option>
  <option value='ILS'>Israeli New Shekel</option>
  <option value='JMD'>Jamaican Dollar</option>
  <option value='JPY'>Japanese Yen</option>
  <option value='JOD'>Jordanian Dinar</option>
  <option value='KZT'>Kazakhstan Tenge</option>
  <option value='KES'>Kenyan Shilling</option>
  <option value='KWD'>Kuwaiti Dinar</option>
  <option value='LAK'>Lao Kip</option>
  <option value='LVL'>Latvian Lats</option>
  <option value='LBP'>Lebanese Pound</option>
  <option value='LSL'>Lesotho Loti</option>
  <option value='LRD'>Liberian Dollar</option>
  <option value='LYD'>Libyan Dinar</option>
  <option value='LTL'>Lithuanian Litas</option>
  <option value='MOP'>Macau Pataca</option>
  <option value='MGF'>Malagasy Franc</option>
  <option value='MWK'>Malawi Kwacha</option>
  <option value='MYR'>Malaysian Ringgit</option>
  <option value='MVR'>Maldive Rufiyaa</option>
  <option value='MTL'>Maltese Lira</option>
  <option value='MRO'>Mauritanian Ouguiya</option>
  <option value='MUR'>Mauritius Rupee</option>
  <option value='MXN'>Mexican Peso</option>
  <option value='MNT'>Mongolian Tugrik</option>
  <option value='MAD'>Moroccan Dirham</option>
  <option value='MZM'>Mozambique Metical</option>
  <option value='MMK'>Myanmar Kyat</option>
  <option value='NAD'>Namibia Dollar</option>
  <option value='NPR'>Nepalese Rupee</option>
  <option value='NZD'>New Zealand Dollar</option>
  <option value='NIO'>Nicaraguan Cordoba Oro</option>
  <option value='NGN'>Nigerian Naira</option>
  <option value='KPW'>North Korean Won</option>
  <option value='NOK'>Norwegian Kroner</option>
  <option value='OMR'>Omani Rial</option>
  <option value='PKR'>Pakistan Rupee</option>
  <option value='PAB'>Panamanian Balboa</option>
  <option value='PGK'>Papua New Guinea Kina</option>
  <option value='PYG'>Paraguay Guarani</option>
  <option value='PEN'>Peruvian Nuevo Sol</option>
  <option value='PHP'>Philippine Peso</option>
  <option value='PLN'>Polish Zloty</option>
  <option value='QAR'>Qatari Rial</option>
  <option value='ROL'>Romanian Lei</option>
  <option value='RUB'>Russian Rouble</option>
  <option value='WST'>Samoan Tala</option>
  <option value='STD'>Sao Tome/Principe Dobra</option>
  <option value='SAR'>Saudi Riyal</option>
  <option value='SCR'>Seychelles Rupee</option>
  <option value='SLL'>Sierra Leone Leone</option>
  <option value='SGD'>Singapore Dollar</option>
  <option value='SKK'>Slovak Koruna</option>
  <option value='SIT'>Slovenian Tolar</option>
  <option value='SBD'>Solomon Islands Dollar</option>
  <option value='SOS'>Somali Shilling</option>
  <option value='ZAR'>South African Rand</option>
  <option value='KRW'>South-Korean Won</option>
  <option value='LKR'>Sri Lanka Rupee</option>
  <option value='SHP'>St. Helena Pound</option>
  <option value='SDD'>Sudanese Dinar</option>
  <option value='SDP'>Sudanese Pound</option>
  <option value='SRG'>Suriname Guilder</option>
  <option value='SZL'>Swaziland Lilangeni</option>
  <option value='SEK'>Swedish Krona</option>
  <option value='CHF'>Swiss Franc</option>
  <option value='SYP'>Syrian Pound</option>
  <option value='TWD'>Taiwan Dollar</option>
  <option value='TZS'>Tanzanian Shilling</option>
  <option value='THB'>Thai Baht</option>
  <option value='TOP'>Tonga Pa'anga</option>
  <option value='TTD'>Trinidad/Tobago Dollar</option>
  <option value='TND'>Tunisian Dinar</option>
  <option value='TRL'>Turkish Lira</option>
  <option value='USD'>US Dollar</option>
  <option value='UGS'>Uganda Shilling</option>
  <option value='UAH'>Ukraine Hryvnia</option>
  <option value='UYP'>Uruguayan Peso</option>
  <option value='AED'>Utd. Arab Emir. Dirham</option>
  <option value='VUV'>Vanuatu Vatu</option>
  <option value='VEB'>Venezuelan Bolivar</option>
  <option value='VND'>Vietnamese Dong</option>
  <option value='YER'>Yemeni Rial</option>
  <option value='YUN'>Yugoslav Dinar</option>
  <option value='ZMK'>Zambian Kwacha</option>
  <option value='ZWD'>Zimbabwe Dollar</option>
  </select>"
);

$formfields[430] = array (
  "field_title" => "World Countries",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option Value='' Selected=true>Select Country</option>
  <option Value='AF'>Afghanistan</option>
  <option Value='AL'>Albania</option>
  <option Value='DZ'>Algeria</option>
  <option Value='AS'>American Samoa</option>
  <option Value='AD'>Andorra</option>
  <option Value='AO'>Angola</option>
  <option Value='AI'>Anguilla</option>
  <option Value='AQ'>Antarctica</option>
  <option Value='AG'>Antigua And Barbuda</option>
  <option Value='AR'>Argentina</option>
  <option Value='AM'>Armenia</option>
  <option Value='AW'>Aruba</option>
  <option Value='AU'>Australia</option>
  <option Value='AT'>Austria</option>
  <option Value='AZ'>Azerbaijan</option>
  <option Value='BS'>Bahamas</option>
  <option Value='BH'>Bahrain</option>
  <option Value='BD'>Bangladesh</option>
  <option Value='BB'>Barbados</option>
  <option Value='BY'>Belarus</option>
  <option Value='BE'>Belgium</option>
  <option Value='BZ'>Belize</option>
  <option Value='BJ'>Benin</option>
  <option Value='BM'>Bermuda</option>
  <option Value='BT'>Bhutan</option>
  <option Value='BO'>Bolivia</option>
  <option Value='BA'>Bosnia And Herzegowina</option>
  <option Value='BW'>Botswana</option>
  <option Value='BV'>Bouvet Island</option>
  <option Value='BR'>Brazil</option>
  <option Value='IO'>British Indian Ocean Territory</option>
  <option Value='BN'>Brunei Darussalam</option>
  <option Value='BG'>Bulgaria</option>
  <option Value='BF'>Burkina Faso</option>
  <option Value='BI'>Burundi</option>
  <option Value='KH'>Cambodia</option>
  <option Value='CM'>Cameroon</option>
  <option Value='CA'>Canada</option>
  <option Value='CV'>Cape Verde</option>
  <option Value='KY'>Cayman Islands</option>
  <option Value='CF'>Central African Republic</option>
  <option Value='TD'>Chad</option>
  <option Value='CL'>Chile</option>
  <option Value='CN'>China</option>
  <option Value='CX'>Christmas Island</option>
  <option Value='CC'>Cocos (Keeling) Islands</option>
  <option Value='CO'>Colombia</option>
  <option Value='KM'>Comoros</option>
  <option Value='CG'>Congo</option>
  <option Value='CK'>Cook Islands</option>
  <option Value='CR'>Costa Rica</option>
  <option Value='CI'>Cote D'Ivoire</option>
  <option Value='HR'>Croatia (Local Name: Hrvatska)</option>
  <option Value='CU'>Cuba</option>
  <option Value='CY'>Cyprus</option>
  <option Value='CZ'>Czech Republic</option>
  <option Value='DK'>Denmark</option>
  <option Value='DJ'>Djibouti</option>
  <option Value='DM'>Dominica</option>
  <option Value='DO'>Dominican Republic</option>
  <option Value='TP'>East Timor</option>
  <option Value='EC'>Ecuador</option>
  <option Value='EG'>Egypt</option>
  <option Value='SV'>El Salvador</option>
  <option Value='GQ'>Equatorial Guinea</option>
  <option Value='ER'>Eritrea</option>
  <option Value='EE'>Estonia</option>
  <option Value='ET'>Ethiopia</option>
  <option Value='FK'>Falkland Islands (Malvinas)</option>
  <option Value='FO'>Faroe Islands</option>
  <option Value='FJ'>Fiji</option>
  <option Value='FI'>Finland</option>
  <option Value='FR'>France</option>
  <option Value='GF'>French Guiana</option>
  <option Value='PF'>French Polynesia</option>
  <option Value='TF'>French Southern Territories</option>
  <option Value='GA'>Gabon</option>
  <option Value='GM'>Gambia</option>
  <option Value='GE'>Georgia</option>
  <option Value='DE'>Germany</option>
  <option Value='GH'>Ghana</option>
  <option Value='GI'>Gibraltar</option>
  <option Value='GR'>Greece</option>
  <option Value='GL'>Greenland</option>
  <option Value='GD'>Grenada</option>
  <option Value='GP'>Guadeloupe</option>
  <option Value='GU'>Guam</option>
  <option Value='GT'>Guatemala</option>
  <option Value='GN'>Guinea</option>
  <option Value='GW'>Guinea-Bissau</option>
  <option Value='GY'>Guyana</option>
  <option Value='HT'>Haiti</option>
  <option Value='HM'>Heard And Mc Donald Islands</option>
  <option Value='VA'>Holy See (Vatican City State)</option>
  <option Value='HN'>Honduras</option>
  <option Value='HK'>Hong Kong</option>
  <option Value='HU'>Hungary</option>
  <option Value='IS'>Icel And</option>
  <option Value='IN'>India</option>
  <option Value='ID'>Indonesia</option>
  <option Value='IR'>Iran (Islamic Republic Of)</option>
  <option Value='IQ'>Iraq</option>
  <option Value='IE'>Ireland</option>
  <option Value='IL'>Israel</option>
  <option Value='IT'>Italy</option>
  <option Value='JM'>Jamaica</option>
  <option Value='JP'>Japan</option>
  <option Value='JO'>Jordan</option>
  <option Value='KZ'>Kazakhstan</option>
  <option Value='KE'>Kenya</option>
  <option Value='KI'>Kiribati</option>
  <option Value='KP'>Korea, Dem People'S Republic</option>
  <option Value='KR'>Korea, Republic Of</option>
  <option Value='KW'>Kuwait</option>
  <option Value='KG'>Kyrgyzstan</option>
  <option Value='LA'>Lao People'S Dem Republic</option>
  <option Value='LV'>Latvia</option>
  <option Value='LB'>Lebanon</option>
  <option Value='LS'>Lesotho</option>
  <option Value='LR'>Liberia</option>
  <option Value='LY'>Libyan Arab Jamahiriya</option>
  <option Value='LI'>Liechtenstein</option>
  <option Value='LT'>Lithuania</option>
  <option Value='LU'>Luxembourg</option>
  <option Value='MO'>Macau</option>
  <option Value='MK'>Macedonia</option>
  <option Value='MG'>Madagascar</option>
  <option Value='MW'>Malawi</option>
  <option Value='MY'>Malaysia</option>
  <option Value='MV'>Maldives</option>
  <option Value='ML'>Mali</option>
  <option Value='MT'>Malta</option>
  <option Value='MH'>Marshall Islands</option>
  <option Value='MQ'>Martinique</option>
  <option Value='MR'>Mauritania</option>
  <option Value='MU'>Mauritius</option>
  <option Value='YT'>Mayotte</option>
  <option Value='MX'>Mexico</option>
  <option Value='FM'>Micronesia, Federated States</option>
  <option Value='MD'>Moldova, Republic Of</option>
  <option Value='MC'>Monaco</option>
  <option Value='MN'>Mongolia</option>
  <option Value='MS'>Montserrat</option>
  <option Value='MA'>Morocco</option>
  <option Value='MZ'>Mozambique</option>
  <option Value='MM'>Myanmar</option>
  <option Value='NA'>Namibia</option>
  <option Value='NR'>Nauru</option>
  <option Value='NP'>Nepal</option>
  <option Value='NL'>Netherlands</option>
  <option Value='AN'>Netherlands Ant Illes</option>
  <option Value='NC'>New Caledonia</option>
  <option Value='NZ'>New Zealand</option>
  <option Value='NI'>Nicaragua</option>
  <option Value='NE'>Niger</option>
  <option Value='NG'>Nigeria</option>
  <option Value='NU'>Niue</option>
  <option Value='NF'>Norfolk Island</option>
  <option Value='MP'>Northern Mariana Islands</option>
  <option Value='NO'>Norway</option>
  <option Value='OM'>Oman</option>
  <option Value='PK'>Pakistan</option>
  <option Value='PW'>Palau</option>
  <option Value='PA'>Panama</option>
  <option Value='PG'>Papua New Guinea</option>
  <option Value='PY'>Paraguay</option>
  <option Value='PE'>Peru</option>
  <option Value='PH'>Philippines</option>
  <option Value='PN'>Pitcairn</option>
  <option Value='PL'>Poland</option>
  <option Value='PT'>Portugal</option>
  <option Value='PR'>Puerto Rico</option>
  <option Value='QA'>Qatar</option>
  <option Value='RE'>Reunion</option>
  <option Value='RO'>Romania</option>
  <option Value='RU'>Russian Federation</option>
  <option Value='RW'>Rwanda</option>
  <option Value='KN'>Saint K Itts And Nevis</option>
  <option Value='LC'>Saint Lucia</option>
  <option Value='VC'>Saint Vincent, The Grenadines</option>
  <option Value='WS'>Samoa</option>
  <option Value='SM'>San Marino</option>
  <option Value='ST'>Sao Tome And Principe</option>
  <option Value='SA'>Saudi Arabia</option>
  <option Value='SN'>Senegal</option>
  <option Value='SC'>Seychelles</option>
  <option Value='SL'>Sierra Leone</option>
  <option Value='SG'>Singapore</option>
  <option Value='SK'>Slovakia (Slovak Republic)</option>
  <option Value='SI'>Slovenia</option>
  <option Value='SB'>Solomon Islands</option>
  <option Value='SO'>Somalia</option>
  <option Value='ZA'>South Africa</option>
  <option Value='GS'>South Georgia , S Sandwich Is.</option>
  <option Value='ES'>Spain</option>
  <option Value='LK'>Sri Lanka</option>
  <option Value='SH'>St. Helena</option>
  <option Value='PM'>St. Pierre And Miquelon</option>
  <option Value='SD'>Sudan</option>
  <option Value='SR'>Suriname</option>
  <option Value='SJ'>Svalbard, Jan Mayen Islands</option>
  <option Value='SZ'>Sw Aziland</option>
  <option Value='SE'>Sweden</option>
  <option Value='CH'>Switzerland</option>
  <option Value='SY'>Syrian Arab Republic</option>
  <option Value='TW'>Taiwan</option>
  <option Value='TJ'>Tajikistan</option>
  <option Value='TZ'>Tanzania, United Republic Of</option>
  <option Value='TH'>Thailand</option>
  <option Value='TG'>Togo</option>
  <option Value='TK'>Tokelau</option>
  <option Value='TO'>Tonga</option>
  <option Value='TT'>Trinidad And Tobago</option>
  <option Value='TN'>Tunisia</option>
  <option Value='TR'>Turkey</option>
  <option Value='TM'>Turkmenistan</option>
  <option Value='TC'>Turks And Caicos Islands</option>
  <option Value='TV'>Tuvalu</option>
  <option Value='UG'>Uganda</option>
  <option Value='UA'>Ukraine</option>
  <option Value='AE'>United Arab Emirates</option>
  <option Value='GB'>United Kingdom</option>
  <option Value='US'>United States</option>
  <option Value='UM'>United States Minor Is.</option>
  <option Value='UY'>Uruguay</option>
  <option Value='UZ'>Uzbekistan</option>
  <option Value='VU'>Vanuatu</option>
  <option Value='VE'>Venezuela</option>
  <option Value='VN'>Viet Nam</option>
  <option Value='VG'>Virgin Islands (British)</option>
  <option Value='VI'>Virgin Islands (U.S.)</option>
  <option Value='WF'>Wallis And Futuna Islands</option>
  <option Value='EH'>Western Sahara</option>
  <option Value='YE'>Yemen</option>
  <option Value='YU'>Yugoslavia</option>
  <option Value='ZR'>Zaire</option>
  <option Value='ZM'>Zambia</option>
  <option Value='ZW'>Zimbabwe</option>
  </select>"
);

$formfields[450] = array (
  "field_title" => "German States",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Bundesland wählen</option>
  <option value='BW'>Baden-Württemberg</option>
  <option value='BY'>Bayern</option>
  <option value='BE'>Berlin</option>
  <option value='BB'>Brandenburg</option>
  <option value='HB'>Bremen</option>
  <option value='HH'>Hamburg</option>
  <option value='HE'>Hessen</option>
  <option value='MV'>Mecklenburg-Vorpommern</option>
  <option value='NI'>Niedersachsen</option>
  <option value='NW'>Nordrhein-Westfalen</option>
  <option value='RP'>Rheinland-Pfalz</option>
  <option value='SL'>Saarland</option>
  <option value='SN'>Sachsen</option>
  <option value='ST'>Sachsen-Anhalt</option>
  <option value='SH'>Schleswig-Holstein</option>
  <option value='TH'>Thüringen</option>
  </select>"
);

$formfields[454] = array (
  "field_title" => "USA States",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Select State</option>
  <option value='AL'>Alabama</option>
  <option value='AK'>Alaska</option>
  <option value='AZ'>Arizona</option>
  <option value='AR'>Arkansas</option>
  <option value='CA'>California</option>
  <option value='CO'>Colorado</option>
  <option value='CT'>Connecticut</option>
  <option value='DE'>Delaware</option>
  <option value='DC'>District of Columbia</option>
  <option value='FL'>Florida</option>
  <option value='GA'>Georgia</option>
  <option value='HI'>Hawaii</option>
  <option value='ID'>Idaho</option>
  <option value='IL'>Illinois</option>
  <option value='IN'>Indiana</option>
  <option value='IA'>Iowa</option>
  <option value='KS'>Kansas</option>
  <option value='KY'>Kentucky</option>
  <option value='LA'>Louisiana</option>
  <option value='ME'>Maine</option>
  <option value='MD'>Maryland</option>
  <option value='MA'>Massachusetts</option>
  <option value='MI'>Michigan</option>
  <option value='MN'>Minnesota</option>
  <option value='MS'>Mississippi</option>
  <option value='MO'>Missouri</option>
  <option value='MT'>Montana</option>
  <option value='NE'>Nebraska</option>
  <option value='NV'>Nevada</option>
  <option value='NH'>New Hampshire</option>
  <option value='NJ'>New Jersey</option>
  <option value='NM'>New Mexico</option>
  <option value='NY'>New York</option>
  <option value='NC'>North Carolina</option>
  <option value='ND'>North Dakota</option>
  <option value='OH'>Ohio</option>
  <option value='OK'>Oklahoma</option>
  <option value='OR'>Oregon</option>
  <option value='PA'>Pennsylvania</option>
  <option value='RI'>Rhode Island</option>
  <option value='SC'>South Carolina</option>
  <option value='SD'>South Dakota</option>
  <option value='TN'>Tennessee</option>
  <option value='TX'>Texas</option>
  <option value='UT'>Utah</option>
  <option value='VT'>Vermont</option>
  <option value='VA'>Virginia</option>
  <option value='WA'>Washington</option>
  <option value='WV'>West Virginia</option>
  <option value='WI'>Wisconsin</option>
  <option value='WY'>Wyoming</option>
  </select>"
);

$formfields[458] = array (
  "field_title" => "Canadian Provinces/Territories",
  "field_html"  => "<select size='1' name='af_fieldname' class='inputbox' style='af_fieldstyle'>
  <option value=''>Select Province/Territory</option>
  <option value='AB'>Alberta</option>
  <option value='BC'>British Columbia</option>
  <option value='MB'>Manitoba</option>
  <option value='NB'>New Brunswick</option>
  <option value='NL'>Newfoundland and Labrador</option>
  <option value='NT'>Northwest Territories</option>
  <option value='NS'>Nova Scotia</option>
  <option value='NU'>Nunavut</option>
  <option value='ON'>Ontario</option>
  <option value='PE'>Prince Edward Island</option>
  <option value='QC'>Quebec</option>
  <option value='SK'>Saskatchewan</option>
  <option value='YT'>Yukon</option>
  </select>"
);


/**
* Load custom userfields - Only add fields to this file and use ID above 800
**/
if (file_exists($mosConfig_absolute_path.'/administrator/components/com_akoforms/fields_custom.akoforms.php'))
  include($mosConfig_absolute_path.'/administrator/components/com_akoforms/fields_custom.akoforms.php');

?>