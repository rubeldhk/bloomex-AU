<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
?>
<script src="components/com_currencyconverter/ajax.js" type="text/javascript"></script>
<form name="converter">
<Table border=0 cellpadding=2 cellspacing=2>
<TR><TD>Value :</TD>
<td> <input name="val" type="text" size="7" /></td>
</TR><TR><TD>From :</TD>
<td><select name="cur_from">
<option value="CAD">Canadian Dollar (CAD)</option>
</select>
</td></TR>
<TR><TD>TO :</TD>
<TD>
<select name="cur_to">
<option selected value="USD">U.S. Dollar (USD)</option>
<option value="EUR">Euro (EUR)</option>
<option value="GBP">British Pound (GBP)</option>
<option value="DZD">Algerian Dinar (DZD)</option>
<option value="XAL">Aluminium Ounces (XAL)</option>
<option value="ARS">Argentine Peso (ARS)</option>
<option value="AWG">Aruba Florin (AWG)</option>
<option value="AUD">Australian Dollar (AUD)</option>
<option value="BSD">Bahamian Dollar (BSD)</option>
<option value="BHD">Bahraini Dinar (BHD)</option>
<option value="BDT">Bangladesh Taka (BDT)</option>
<option value="BBD">Barbados Dollar (BBD)</option>
<option value="BYR">Belarus Ruble (BYR)</option>
<option value="BZD">Belize Dollar (BZD)</option>
<option value="BMD">Bermuda Dollar (BMD)</option>
<option value="BTN">Bhutan Ngultrum (BTN)</option>
<option value="BOB">Bolivian Boliviano (BOB)</option>
<option value="BRL">Brazilian Real (BRL)</option>
<option value="BND">Brunei Dollar (BND)</option>
<option value="BGN">Bulgarian Lev (BGN)</option>
<option value="BIF">Burundi Franc (BIF)</option>
<option value="KHR">Cambodia Riel (KHR)</option>
<option value="CAD">Canadian Dollar (CAD)</option>
<option value="KYD">Cayman Islands Dollar (KYD)</option>
<option value="XOF">CFA Franc (BCEAO) (XOF)</option>
<option value="XAF">CFA Franc (BEAC) (XAF)</option>
<option value="CLP">Chilean Peso (CLP)</option>
<option value="CNY">Chinese Yuan (CNY)</option>
<option value="COP">Colombian Peso (COP)</option>
<option value="KMF">Comoros Franc (KMF)</option>
<option value="XCP">Copper Ounces (XCP)</option>

<option value="CRC">Costa Rica Colon (CRC)</option>
<option value="HRK">Croatian Kuna (HRK)</option>
<option value="CUP">Cuban Peso (CUP)</option>
<option value="CYP">Cyprus Pound (CYP)</option>
<option value="CZK">Czech Koruna (CZK)</option>
<option value="DKK">Danish Krone (DKK)</option>
<option value="DJF">Dijibouti Franc (DJF)</option>
<option value="DOP">Dominican Peso (DOP)</option>
<option value="XCD">East Caribbean Dollar (XCD)</option>

<option value="ECS">Ecuador Sucre (ECS)</option>
<option value="EGP">Egyptian Pound (EGP)</option>
<option value="SVC">El Salvador Colon (SVC)</option>
<option value="ERN">Eritrea Nakfa (ERN)</option>
<option value="EEK">Estonian Kroon (EEK)</option>
<option value="ETB">Ethiopian Birr (ETB)</option>

<option value="FKP">Falkland Islands Pound (FKP)</option>
<option value="GMD">Gambian Dalasi (GMD)</option>

<option value="GHC">Ghanian Cedi (GHC)</option>
<option value="GIP">Gibraltar Pound (GIP)</option>
<option value="XAU">Gold Ounces (XAU)</option>
<option value="GTQ">Guatemala Quetzal (GTQ)</option>
<option value="GNF">Guinea Franc (GNF)</option>
<option value="HTG">Haiti Gourde (HTG)</option>
<option value="HNL">Honduras Lempira (HNL)</option>
<option value="HKD">Hong Kong Dollar (HKD)</option>
<option value="HUF">Hungarian Forint (HUF)</option>

<option value="ISK">Iceland Krona (ISK)</option>
<option value="INR">Indian Rupee (INR)</option>
<option value="IDR">Indonesian Rupiah (IDR)</option>
<option value="IRR">Iran Rial (IRR)</option>
<option value="ILS">Israeli Shekel (ILS)</option>
<option value="JMD">Jamaican Dollar (JMD)</option>
<option value="JPY">Japanese Yen (JPY)</option>
<option value="JOD">Jordanian Dinar (JOD)</option>
<option value="KZT">Kazakhstan Tenge (KZT)</option>

<option value="KES">Kenyan Shilling (KES)</option>
<option value="KRW">Korean Won (KRW)</option>
<option value="KWD">Kuwaiti Dinar (KWD)</option>
<option value="LAK">Lao Kip (LAK)</option>
<option value="LVL">Latvian Lat (LVL)</option>
<option value="LBP">Lebanese Pound (LBP)</option>
<option value="LSL">Lesotho Loti (LSL)</option>
<option value="LYD">Libyan Dinar (LYD)</option>
<option value="LTL">Lithuanian Lita (LTL)</option>

<option value="MOP">Macau Pataca (MOP)</option>
<option value="MKD">Macedonian Denar (MKD)</option>
<option value="MGF">Malagasy Franc (MGF)</option>
<option value="MWK">Malawi Kwacha (MWK)</option>
<option value="MYR">Malaysian Ringgit (MYR)</option>
<option value="MVR">Maldives Rufiyaa (MVR)</option>
<option value="MTL">Maltese Lira (MTL)</option>
<option value="MRO">Mauritania Ougulya (MRO)</option>
<option value="MUR">Mauritius Rupee (MUR)</option>

<option value="MXN">Mexican Peso (MXN)</option>
<option value="MDL">Moldovan Leu (MDL)</option>
<option value="MNT">Mongolian Tugrik (MNT)</option>
<option value="MAD">Moroccan Dirham (MAD)</option>
<option value="MZM">Mozambique Metical (MZM)</option>
<option value="NAD">Namibian Dollar (NAD)</option>
<option value="NPR">Nepalese Rupee (NPR)</option>
<option value="ANG">Neth Antilles Guilder (ANG)</option>
<option value="TRY">New Turkish Lira (TRY)</option>

<option value="NZD">New Zealand Dollar (NZD)</option>
<option value="NIO">Nicaragua Cordoba (NIO)</option>
<option value="NGN">Nigerian Naira (NGN)</option>
<option value="NOK">Norwegian Krone (NOK)</option>
<option value="OMR">Omani Rial (OMR)</option>
<option value="XPF">Pacific Franc (XPF)</option>
<option value="PKR">Pakistani Rupee (PKR)</option>
<option value="XPD">Palladium Ounces (XPD)</option>
<option value="PAB">Panama Balboa (PAB)</option>

<option value="PGK">Papua New Guinea Kina (PGK)</option>
<option value="PYG">Paraguayan Guarani (PYG)</option>
<option value="PEN">Peruvian Nuevo Sol (PEN)</option>
<option value="PHP">Philippine Peso (PHP)</option>
<option value="XPT">Platinum Ounces (XPT)</option>
<option value="PLN">Polish Zloty (PLN)</option>
<option value="QAR">Qatar Rial (QAR)</option>
<option value="ROL">Romanian Leu (ROL)</option>
<option value="RON">Romanian New Leu (RON)</option>

<option value="RUB">Russian Rouble (RUB)</option>
<option value="RWF">Rwanda Franc (RWF)</option>
<option value="WST">Samoa Tala (WST)</option>
<option value="STD">Sao Tome Dobra (STD)</option>
<option value="SAR">Saudi Arabian Riyal (SAR)</option>
<option value="SCR">Seychelles Rupee (SCR)</option>
<option value="SLL">Sierra Leone Leone (SLL)</option>
<option value="XAG">Silver Ounces (XAG)</option>
<option value="SGD">Singapore Dollar (SGD)</option>

<option value="SKK">Slovak Koruna (SKK)</option>
<option value="SIT">Slovenian Tolar (SIT)</option>
<option value="SOS">Somali Shilling (SOS)</option>
<option value="ZAR">South African Rand (ZAR)</option>
<option value="LKR">Sri Lanka Rupee (LKR)</option>
<option value="SHP">St Helena Pound (SHP)</option>
<option value="SDD">Sudanese Dinar (SDD)</option>
<option value="SRG">Surinam Guilder (SRG)</option>
<option value="SZL">Swaziland Lilageni (SZL)</option>

<option value="SEK">Swedish Krona (SEK)</option>
<option value="CHF">Swiss Franc (CHF)</option>
<option value="SYP">Syrian Pound (SYP)</option>
<option value="TWD">Taiwan Dollar (TWD)</option>
<option value="TZS">Tanzanian Shilling (TZS)</option>
<option value="THB">Thai Baht (THB)</option>
<option value="TOP">Tonga Pa'anga (TOP)</option>
<option value="TTD">Trinidad&Tobago Dollar (TTD)</option>

<option value="TND">Tunisian Dinar (TND)</option>
<option value="AED">UAE Dirham (AED)</option>
<option value="UGX">Ugandan Shilling (UGX)</option>
<option value="UAH">Ukraine Hryvnia (UAH)</option>
<option value="UYU">Uruguayan New Peso (UYU)</option>
<option value="VUV">Vanuatu Vatu (VUV)</option>
<option value="VEB">Venezuelan Bolivar (VEB)</option>
<option value="VND">Vietnam Dong (VND)</option>

<option value="YER">Yemen Riyal (YER)</option>
<option value="ZMK">Zambian Kwacha (ZMK)</option>
<option value="ZWD">Zimbabwe Dollar (ZWD)</option>
</select></TD></TR>
<TR><TD colspan=2>
<input name="submit" type="button" value="Convert" onclick="showDataInput()" /></TD>
</TR></Table>
</form>
<div id="result"> </div>