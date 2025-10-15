var digits = "0123456789";
var lowercaseLetters = "abcdefghijklmnopqrstuvwxyz"
var uppercaseLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
var whitespace = " \t\n\r";
var decimalPointDelimiter = "."
var phoneNumberDelimiters = "()-xX ";
var validUSPhoneChars = digits + phoneNumberDelimiters;
var validWorldPhoneChars = digits + phoneNumberDelimiters + "+";
var digitsInUSPhoneNumber = 10;
var ZIPCodeDelimiters = "-";
var ZIPCodeDelimeter = "-"
var validZIPCodeChars = digits + ZIPCodeDelimiters
var digitsInZIPCode1 = 5
var digitsInZIPCode2 = 9
var creditCardDelimiters = " "
var passwordLength = 4;
var lowercaseUS = 'us';
var uppercaseUS = 'US';
var lowercaseCA = 'ca';
var uppercaseCA = 'CA';

var defaultEmptyOK = false

function makeArray(n) {
//*** BUG: If I put this line in, I get two error messages:
//(1) Window.length can't be set by assignment
//(2) daysInMonth has no property indexed by 4
//If I leave it out, the code works fine.
//   this.length = n;
   for (var i = 1; i <= n; i++) {
      this[i] = 0
   }
   return this
}

var daysInMonth = makeArray(12);
daysInMonth[1] = 31;
daysInMonth[2] = 28;
daysInMonth[3] = 31;
daysInMonth[4] = 30;
daysInMonth[5] = 31;
daysInMonth[6] = 30;
daysInMonth[7] = 31;
daysInMonth[8] = 31;
daysInMonth[9] = 30;
daysInMonth[10] = 31;
daysInMonth[11] = 30;
daysInMonth[12] = 31;

var months = new Array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

var USStateCodeDelimiter = "|";
var USStateCodes = "AL|AK|AS|AZ|AR|CA|CO|CT|DE|DC|FM|FL|GA|GU|HI|ID|IL|IN|IA|KS|KY|LA|ME|MH|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|MP|OH|OK|OR|PW|PA|PR|RI|SC|SD|TN|TX|UT|VT|VI|VA|WA|WV|WI|WY|AE|AA|AE|AE|AP"
var CanadianProvinces = "AB|BC|MB|NB|NF|NT|NS|ON|PE|QC|SK|YT"

function isEmpty(s)
{   return ((s == null) || (s.length == 0))
}

function isWhitespace (s)
{   var i;

  if (isEmpty(s)) return true;

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);

    if (whitespace.indexOf(c) == -1) return false;
  }
  return true;
}

function stripCharsInBag (s, bag)
{   var i;
  var returnString = "";

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);
    if (bag.indexOf(c) == -1) returnString += c;
  }
  return returnString;
}

function stripCharsNotInBag (s, bag)
{ var i;
  var returnString = "";

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);
    if (bag.indexOf(c) != -1) returnString += c;
  }
  return returnString;
}

function stripWhitespace (s)
{ return stripCharsInBag (s, whitespace)
}

function charInString (c, s)
{ for (i = 0; i < s.length; i++)
  {   if (s.charAt(i) == c) return true;
  }
  return false
}


function stripInitialWhitespace (s)
{ var i = 0;

  while ((i < s.length) && charInString (s.charAt(i), whitespace))
     i++;

  return s.substring (i, s.length);
}

function rtrim( s )
{
	if( s == null )
		return '';
	if( s.length == 0 )
		return '';
	var li = s.length - 1;
	while( s.charAt( li ) == ' ' )
		li--;
	return s.substring( 0, li+1 );
}

function ltrim( s )
{
	if( s.length == 0 )
		return '';
	var fi = 0;
	while( fi < s.length && s.charAt( fi ) == ' ' )
		fi++;
	if( fi == s.length )
		return '';
	else
		return s.substring( fi );
}

function btrim( s )
{
	return ltrim( rtrim( s ) );
}

function isLetter (c)
{ return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) )
}

function isDigit (c)
{  return ((c >= "0") && (c <= "9"))
}

function isLetterOrDigit (c)
{  return (isLetter(c) || isDigit(c))
}

function isInteger (s)
{ var i;

  if (isEmpty(s))
     if (isInteger.arguments.length == 1) return defaultEmptyOK;
     else return (isInteger.arguments[1] == true);

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);

    if (!isDigit(c)) return false;
  }
  return true;
}

function isSignedInteger (s)
{ if (isEmpty(s))
    if (isSignedInteger.arguments.length == 1) return defaultEmptyOK;
    else return (isSignedInteger.arguments[1] == true);

  else {
    var startPos = 0;
    var secondArg = defaultEmptyOK;

    if (isSignedInteger.arguments.length > 1)
      secondArg = isSignedInteger.arguments[1];

    if ( (s.charAt(0) == "-") || (s.charAt(0) == "+") )
       startPos = 1;
    return (isInteger(s.substring(startPos, s.length), secondArg))
  }
}

function isPositiveInteger (s)
{ var secondArg = defaultEmptyOK;
  if (isPositiveInteger.arguments.length > 1)
    secondArg = isPositiveInteger.arguments[1];

  return (isSignedInteger(s, secondArg)
     && ( (isEmpty(s) && secondArg)  || (parseInt (s) > 0) ) );
}

function isNonnegativeInteger (s)
{ 
  var secondArg = defaultEmptyOK;

  if (isNonnegativeInteger.arguments.length > 1)
    secondArg = isNonnegativeInteger.arguments[1];

  return (isSignedInteger(s, secondArg)
     && ( (isEmpty(s) && secondArg)  || (parseInt (s) >= 0) ) );
}

function isNegativeInteger (s)
{ var secondArg = defaultEmptyOK;

  if (isNegativeInteger.arguments.length > 1)
    secondArg = isNegativeInteger.arguments[1];

  return (isSignedInteger(s, secondArg)
     && ( (isEmpty(s) && secondArg)  || (parseInt (s) < 0) ) );
}

function isNonpositiveInteger (s)
{ var secondArg = defaultEmptyOK;

  if (isNonpositiveInteger.arguments.length > 1)
    secondArg = isNonpositiveInteger.arguments[1];

  return (isSignedInteger(s, secondArg)
     && ( (isEmpty(s) && secondArg)  || (parseInt (s) <= 0) ) );
}

function isFloat (s)
{ var i;
  var seenDecimalPoint = false;

  if (isEmpty(s))
     if (isFloat.arguments.length == 1) return defaultEmptyOK;
     else return (isFloat.arguments[1] == true);

  if (s == decimalPointDelimiter) return false;

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);

    if ((c == decimalPointDelimiter) && !seenDecimalPoint) seenDecimalPoint = true;
    else if (!isDigit(c)) return false;
  }

  return true;
}


function isSignedFloat (s)
{ if (isEmpty(s))
     if (isSignedFloat.arguments.length == 1) return defaultEmptyOK;
     else return (isSignedFloat.arguments[1] == true);

  else {
    var startPos = 0;
    var secondArg = defaultEmptyOK;

    if (isSignedFloat.arguments.length > 1)
      secondArg = isSignedFloat.arguments[1];

    if ( (s.charAt(0) == "-") || (s.charAt(0) == "+") )
       startPos = 1;
    return (isFloat(s.substring(startPos, s.length), secondArg))
  }
}

function isAlphabetic (s)
{ var i;

  if (isEmpty(s))
     if (isAlphabetic.arguments.length == 1) return defaultEmptyOK;
     else return (isAlphabetic.arguments[1] == true);

  for (i = 0; i < s.length; i++)
  {
    // Check that current character is letter.
    var c = s.charAt(i);

    if (!isLetter(c))
    return false;
  }
  return true;
}

function isAlphanumeric (s)
{ var i;

  if (isEmpty(s))
     if (isAlphanumeric.arguments.length == 1) return defaultEmptyOK;
     else return (isAlphanumeric.arguments[1] == true);

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);

    if (! (isLetter(c) || isDigit(c) ) )
    return false;
  }
  return true;
}

function reformat (s)
{ var arg;
  var sPos = 0;
  var resultString = "";

  for (var i = 1; i < reformat.arguments.length; i++) {
     arg = reformat.arguments[i];
     if (i % 2 == 1) resultString += arg;
     else {
       resultString += s.substring(sPos, sPos + arg);
       sPos += arg;
     }
  }
  return resultString;
}

function isUSPhoneNumber (s)
{ if (isEmpty(s))
    if (isUSPhoneNumber.arguments.length == 1) return defaultEmptyOK;
    else return (isUSPhoneNumber.arguments[1] == true);
  if( !isInteger( s ) || s.length < digitsInUSPhoneNumber )
  	return false;

  return true;
}

function isInternationalPhoneNumber (s)
{
 if (isEmpty(s))
    if (isInternationalPhoneNumber.arguments.length == 1) return defaultEmptyOK;
    else return (isInternationalPhoneNumber.arguments[1] == true);
  return (isPositiveInteger(s))
}

function isZIPCode (s)
{ if (isEmpty(s))
    if (isZIPCode.arguments.length == 1) return defaultEmptyOK;
    else return (isZIPCode.arguments[1] == true);
  return (isInteger(s) &&
      ((s.length == digitsInZIPCode1) ||
       (s.length == digitsInZIPCode2)))
}

function isCanadianZIPCode( s )
{
 if (isEmpty(s))
    if (isCanadianZIPCode.arguments.length == 1) return defaultEmptyOK;
    else return (isCanadianZIPCode.arguments[1] == true);
 if( s.length != 7 || !isDigit(s.charAt(1)) || !isDigit(s.charAt(4)) || 
     !isDigit(s.charAt(6)) || s.charAt(3) != ' ' || !isLetter(s.charAt(0)) ||
		 !isLetter(s.charAt(2)) || !isLetter(s.charAt(5)) )
		return false
	return true
}

function isStateCode(s)
{ if (isEmpty(s))
    if (isStateCode.arguments.length == 1) return defaultEmptyOK;
    else return (isStateCode.arguments[1] == true);
  return ( (USStateCodes.indexOf(s) != -1) &&
       (s.indexOf(USStateCodeDelimiter) == -1) )
}

function isCanadianProvince(s)
{ if (isEmpty(s))
    if (isCanadianProvince.arguments.length == 1) return defaultEmptyOK;
    else return (isCanadianProvince.arguments[1] == true);
  return ( (CanadianProvinces.indexOf(s) != -1) &&
       (s.indexOf(USStateCodeDelimiter) == -1) )
}

function isEmail (s)
{ if (isEmpty(s))
    if (isEmail.arguments.length == 1) return defaultEmptyOK;
    else return (isEmail.arguments[1] == true);

  if (isWhitespace(s)) return false;

  var i = 1;
  var sLength = s.length;

  while ((i < sLength) && (s.charAt(i) != "@"))
  { i++
  }

  if ((i >= sLength) || (s.charAt(i) != "@")) return false;
  else i += 2;

  while ((i < sLength) && (s.charAt(i) != "."))
  { i++
  }
  // there must be at least one character after the .
  if ((i >= sLength - 1) || (s.charAt(i) != ".")) return false;

  //check for illegal chars
  if ( !( isProper(s) ) ) return false;
  else return true;
}


function isYear (s,oktoOmit)
{ if (isEmpty(s))
     if (isYear.arguments.length == 1) return defaultEmptyOK;
     else return (isYear.arguments[1] == true);
  if (!isNonnegativeInteger(s)) return false;
  return ((s.length == 2) || (s.length == 4));
}


function isIntegerInRange (s, a, b)
{ if (isEmpty(s))
    if (isIntegerInRange.arguments.length == 1) return defaultEmptyOK;
    else return (isIntegerInRange.arguments[1] == true);

  if (!isInteger(s, false)) return false;

  var num = parseInt (s);
  return ((num >= a) && (num <= b));
}

function isMonth (s,oktoOmit)
{ if (isEmpty(s))
    if (isMonth.arguments.length == 1) return defaultEmptyOK;
    else return (isMonth.arguments[1] == true);
  return isIntegerInRange (s, 1, 12);
}

function isDay (s,oktoOmit)
{ if (isEmpty(s))
    if (isDay.arguments.length == 1) return defaultEmptyOK;
    else return (isDay.arguments[1] == true);
  return isIntegerInRange (s, 1, 31);
}

function isProper(string) {
   if (!string) return false;
   var iChars = " *|,\":<>[]{}`\';()&$#%";
   for (var i = 0; i < string.length; i++) {
      if (iChars.indexOf(string.charAt(i)) != -1)
         return false;
   }
   return true;
} 
function daysInFebruary (year)
{ // February has 29 days in any year evenly divisible by four,
  // EXCEPT for centurial years which are not also divisible by 400.
  return (  ((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0) ) ) ? 29 : 28 );
}

function isDate (year, month, day)
{ if (! (isYear(year, false) && isMonth(month, false) && isDay(day, false))) return false;

  var intYear = parseInt(year);
  var intMonth = parseInt(month);
  var intDay = parseInt(day);

  if (intDay > daysInMonth[intMonth]) return false;

  if ((intMonth == 2) && (intDay > daysInFebruary(intYear))) return false;

  return true;
}

function checkString (theField, s, emptyOK)
{ if (checkString.arguments.length == 2) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  if (isWhitespace(theField.value))
     return 'error_required';
  else return null;
}

function checkStateOrProvince( theField, emptyOk, otherFields )
{
	var countryField = otherFields[0];
	if( !countryField )
		return null;
		
	// For now assume that the country field is a drop down
	if(countryField.options)
	{
		var countryCode = countryField.options[countryField.selectedIndex].value;
		if( countryCode == 'us' || countryCode == 'US' )
			return checkStateCode( theField, emptyOk );
		else
		if( countryCode == 'ca' || countryCode == 'CA' )
			return checkCanadianProvince( theField, emptyOk )
		else
			// We accept anything when we don't recognize the country
			return null;
	}
	return null;
}

function checkStateCode (theField, emptyOK)
{ if (checkStateCode.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  {  theField.value = theField.value.toUpperCase();
     if (!isStateCode(theField.value, false))
      return 'error_statecode';
     else return null;
  }
}

function checkCanadianProvince (theField, emptyOK)
{ if (checkCanadianProvince.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  {  theField.value = theField.value.toUpperCase();
     if (!isCanadianProvince(theField.value, false))
      return 'error_canadian_province';
     else return null;
  }
}

function reformatZIPCode (ZIPString)
{ if (ZIPString.length == 5) return ZIPString;
  else return (reformat (ZIPString, "", 5, "-", 4));
}

function checkPostalCode( theField, emptyOk, otherFields )
{
	var countryField = otherFields[0];
		
	if(countryField.options)
	{
		// For now assume that the country field is a drop down
		var countryCode = countryField == null ? 'us' : countryField.options[countryField.selectedIndex].value;
		if( countryCode == 'us' || countryCode == 'US' )
			return checkZIPCode( theField, emptyOk );
		else
		if( countryCode == 'ca' || countryCode == 'CA' ) {
			//Help out HBC migrated data adding spaces
			theField.value=btrim(theField.value);
			  
			if ((emptyOk == true) && (isEmpty(theField.value))) {
			  	return null;
			}
			if (theField.value.length == 6 ) {
				var first = theField.value.substring(0, 3);
				var second = theField.value.substring(3);
				theField.value = first + ' ' + second;
			}
  			
			return checkCanadianZIPCode( theField, emptyOk )
		}
		else
			// We accept anything when we don't recognize the country
			return null;
	}
	return null;
}

function checkZIPCode (theField, emptyOK)
{ if (checkZIPCode.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  { var normalizedZIP = stripCharsInBag(theField.value, ZIPCodeDelimiters)
    if (!isZIPCode(normalizedZIP, false))
     return 'error_zipcode';
    else
    {  
     theField.value = reformatZIPCode(normalizedZIP)
     return null;
    }
  }
}

function checkCanadianZIPCode (theField, emptyOK)
{ if (checkCanadianZIPCode.arguments.length == 1) emptyOK = defaultEmptyOK;
  	theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  { if (!isCanadianZIPCode(theField.value, false))
     return 'error_canadian_zipcode';
    else
     return null;
  }
}

function reformatUSPhone (USPhone)
{  
	if( USPhone.length == digitsInUSPhoneNumber )
		return (reformat (USPhone, "(", 3, ") ", 3, "-", 4));
	else
	{
		var phoneNumber = reformat( USPhone.substring( 0, digitsInUSPhoneNumber ), "(", 3, ") ", 3, "-", 4);
		var ext = USPhone.substring( digitsInUSPhoneNumber );
		return phoneNumber + ' x' + ext;
	}
}

function checkPhone( theField, emptyOk, otherFields )
{
	var countryField = otherFields[0];
	if(	countryField.options )
	{
		// For now assume that the country field is a drop down
		var countryCode = countryField != null ?
			countryField.options[countryField.selectedIndex].value :
			'us';
		if( countryCode == lowercaseUS || countryCode == uppercaseUS )
			return checkUSPhone( theField, emptyOk, otherFields );
		else
		if (  countryCode == lowercaseCA || countryCode == uppercaseCA )
			return checkUSPhone( theField, emptyOk, otherFields );
		else
			return checkInternationalPhone( theField, emptyOk );
	}
}

function checkUSPhone (theField, emptyOK,otherFields)
{ if (checkUSPhone.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  {  
	var normalizedPhone = stripCharsInBag(theField.value, phoneNumberDelimiters)
     if (!isUSPhoneNumber(normalizedPhone, false))
		return 'error_us_phone';
     else
     {  
     	if ( otherFields[1] != undefined ) {
		 	var newPhone = reformatUSPhone(normalizedPhone);
		 	if( otherFields != null && otherFields.length > 1 )
		 	{
				var iExt = newPhone.indexOf( ' x' );
				if( iExt != - 1 )
				{
					var curExt = btrim( otherFields[1].value );
					if( curExt.length == 0 )
						otherFields[1].value = newPhone.substring( iExt + 2 );
					newPhone = newPhone.substring( 0, iExt );
				}
			}
			theField.value = newPhone;
		} else {
			//If there is no NightPhoneExt in the eventpersonfieldrequirements
			var number = theField.value
			if ( number.length > 10 ) {
				var regex = new RegExp("(\d{3}) \d{3}-\d{4}||\d{7,10}");
				if ( !regex.test(number) ) {
					return 'error_us_phone';
				}
			} else {
				theField.value = reformatUSPhone(normalizedPhone);
			}
		}
      	return null;
     }
  }
}

function checkInternationalPhone (theField, emptyOK)
{ 
/* 	5.7.2004 RS - seems too difficult to pinpoint sufficient international 
	phone number validation, so for now we'll pass anything through
	
if (checkInternationalPhone.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else
  { var normalizedPhone = stripCharsInBag(theField.value, phoneNumberDelimiters) 
  	if (!isInternationalPhoneNumber(normalizedPhone, false))
      return 'error_international_phone';
     else return null;
  }
  */
  return null;
}

function checkEmail (theField, emptyOK)
{ if (checkEmail.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  else if (!isEmail(theField.value, false))
     return 'error_email';
  else return null;
}

function checkYear (theField, emptyOK)
{ if (checkYear.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  if (!isYear(theField.value, false))
     return 'error_year';
  else return null;
}

function checkMonth (theField, emptyOK)
{ 
	if (checkMonth.arguments.length == 1) 
		emptyOK = defaultEmptyOK;
		
	theField.value=btrim(theField.value);
  
	if ((emptyOK == true) && (isEmpty(theField.value))) 
		return null;
		
	  if (!isMonth(theField.value, false)) 
	  	return 'error_month';
  
  else return null;
}

function checkDay (theField, emptyOK)
{ if (checkDay.arguments.length == 1) emptyOK = defaultEmptyOK;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
  if (!isDay(theField.value, false))
     return 'error_day';
  else return null;
}

function checkDate (yearField, monthField, dayField, labelString, OKtoOmitDay)
{ if (checkDate.arguments.length == 4) OKtoOmitDay = false;
  yearField.value=btrim(yearField.value);
  monthField.value=btrim(monthField.value);
  dayField.value=btrim(dayField.value);
  if (!isYear(yearField.value)) return warnInvalid (yearField, iYear);
  if (!isMonth(monthField.value)) return warnInvalid (monthField, iMonth);
  if ( (OKtoOmitDay == true) && isEmpty(dayField.value) ) return null;
  else if (!isDay(dayField.value))
     return 'error_date';
  if (isDate (yearField.value, monthField.value, dayField.value))
     return null;
//  alert (iDatePrefix + labelString + iDateSuffix)
  return false
}

function verifyDate(theField, emptyOK)
{ if (verifyDate.arguments.length == 1) emptyOK = defaultEmptyOK;
  if( theField == null ) return null;
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;

  // Accepts a wide variety of dates including Jun 3, 1998, 11/09/49, etc
  var t = Date.parse(theField.value)
  if (isNaN(t)) 
     return 'error_date';
  var dobj = new Date(t);
  var year = dobj.getFullYear();
   
  if( year < 1999 )
     return 'error_date';
  theField.value = ((dobj.getMonth() + 1) + "/" + dobj.getDate() + "/" + dobj.getFullYear());
  return null;
}

/**
 * Gets an array of date values: month/day/year
 */
function getDropDateParts( theField )
{
	var i = theField.name.indexOf( '_month' );
	if( i == -1 )
		return null;
		
	var rootName = theField.name.substring( 0, i );
	
	var dayField = theField.form.elements[rootName + '_day'];
	var yearField = theField.form.elements[rootName + '_year'];
	var monthField = theField;
	
	var dayValue = dayField == null ? null : dayField.options[dayField.selectedIndex].value;
	var monthValue = monthField == null ? null : monthField.options[monthField.selectedIndex].value;
	var yearValue = yearField == null ? null : yearField.options[yearField.selectedIndex].value;
	
	return new Array( monthValue, dayValue, yearValue );
}

/**
 * Determines if the date has a month day and year
 */
function isDateComplete( dateParts )
{
	if( dateParts == null )
		return false;

	var i;
	for( i=0; i < dateParts.length; i++ )
	{
		if( dateParts[i] == null || parseInt( dateParts[i] ) == 0 )
			return false;
	}
	
	return true;
}

function isDateEmpty( dateParts )
{
	if( dateParts == null )
		return true;

	var i;
	for( i=0; i < dateParts.length; i++ )
	{
		if( dateParts[i] != null && parseInt( dateParts[i] ) > 0 )
			return false;
	}
	
	return true;
}

/**
 * Verifies the contents of a three drop down date field
 */
function verifyDropDate( theField, emptyOK )
{
	var dateParts = getDropDateParts( theField );
	if( dateParts == null )
		return null;
		
	if( isDateEmpty( dateParts ) && emptyOK )
		return null;

	if( !isDateComplete( dateParts ) )
		return 'error_date';

	return null;
}

function getDateFromDateParts( dateParts )
{
	var parseValue = months[parseInt(dateParts[0])-1] + ' ' + dateParts[1] + ', ' + dateParts[2];
	return new Date( Date.parse( parseValue ) );
}

function verifyDropDateStart( theField, emptyOk, otherFields )
{
	var err = verifyDropDate( theField, emptyOk );
	if( err != null )
		return err;

	if( otherFields == null || otherFields.length == 0 )
		return 'error_range_date_missing';

	var datePartsStart = getDropDateParts( theField );
	var datePartsEnd = getDropDateParts( otherFields[0] );
	
	if( isDateEmpty( datePartsStart ) && !isDateEmpty( datePartsEnd ) )
		return 'error_range_date_missing';

	return null;
}

function verifyDropDateEnd( theField, emptyOk, otherFields )
{
	var err = verifyDropDate( theField, emptyOk );
	if( err != null )
		return err;

	if( otherFields == null || otherFields.length == 0 )
		return null;

	var datePartsEnd = getDropDateParts( theField );
	var datePartsStart = getDropDateParts( otherFields[0] );
	
	if( isDateEmpty( datePartsEnd ) && !isDateEmpty( datePartsStart ) )
		return 'error_range_date_missing';

	var startDateField = otherFields[0];
	if( startDateField == null )
		return null;

	var startDate = getDateFromDateParts( datePartsStart );
	var endDate = getDateFromDateParts( datePartsEnd );
	var now = new Date();
	
	if( endDate - now < 0 )
		return 'error_future_date';

	if( !isDateComplete( startDate ) )
		return null;		
	
	if( startDate - endDate > 0 )
		return 'error_date_range';

	return null;
}

function verifyPastDate(theField, emptyOk) {
	if ( theField == null ) return null;
 	theField.value=btrim(theField.value);
  	if (isEmpty(theField.value)) return null;
	// Accepts a wide variety of dates including Jun 3, 1998, 11/09/49, etc
  	var t = Date.parse(theField.value)
  	if (isNaN(t)) 
    	return 'error_date';

	var fieldDate = Date.parse(theField.value);
	var now = new Date();
	// Only concern ourselves with the day
	// A future date is valid from today on...
	now.setHours(0); now.setMinutes(0); now.setSeconds(0); now.setMilliseconds(0);
	if ( now.getTime() < fieldDate ) {
		return 'error_past_date';
	}

	// Set the date value to a known format (2/30/2004)
	var dobj = new Date(t);
	theField.value = ((dobj.getMonth() + 1) + "/" + dobj.getDate() + "/" + dobj.getFullYear());

	return null;
}

function verifyMoney( theField, emptyOK )
{
	if( verifyMoney.arguments.length == 1 )
		emptyOK = defaultEmptyOK;
		
	if( theField == null )
		return null;

	theField.value = btrim( theField.value );
	if( isEmpty( theField.value ) )
		return emptyOK ? null : 'error_required';
	
	var s = theField.value;
	var i;
	var numDems = -1;
	var demPos = -1;
	for( i=0; i < s.length; i++ )
	{
    	var c = s.charAt( i );
    	if( c == '.' )
    	{
    		if( i > 9 )
    			return 'error_money_too_large';
    		demPos = i;
    	}
    	else
	    if( !isDigit( c ) )
	    	return 'error_money';

	    if( demPos > -1 )
	    	numDems++;
	}

	// Here, then everything is cool, but let's fix the decimal position
	// to only have two places.
	if( demPos > - 1 )
	{
		// How many decimal places?
		var numDems = s.length - ( demPos + 1 );
		
		if( numDems > 2 )
		{
			var decNumber = Number( s ) + 0.005;
			s = String( decNumber );
			s = s.substring( 0, s.length-1 );
			
			demPos = s.indexOf( '.' );
			if( demPos > 9 )
				return 'error_money_too_large';
				
			theField.value = s;
		}
	}

	return null;
}

function verifySku( theField, emptyOK )
{
	if( verifySku.arguments.length == 1 )
		emptyOK = defaultEmptyOK;
		
	if( theField == null )
		return null;

	theField.value = btrim( theField.value );
	if( isEmpty( theField.value ) )
		return emptyOK ? null : 'error_required';
		
	return theField.value.length < 1 ? 'error_sku' : null;
}

function verifyUpccd( theField, emptyOK )
{
	if( verifyUpccd.arguments.length == 1 )
		emptyOK = defaultEmptyOK;
		
	if( theField == null )
		return null;

	theField.value = btrim( theField.value );
	if( isEmpty( theField.value ) )
		return emptyOK ? null : 'error_required';
	
	return theField.value.length < 1 ? 'error_upccd' : null;
}

function getRadioButtonValue (radio)
{ for (var i = 0; i < radio.length; i++)
  {   if (radio[i].checked) { break }
  }
  return radio[i].value
}

function checkAnyCreditCard (theField,emptyOK)
{ 
  theField.value=btrim(theField.value);
  if ((emptyOK == true) && (isEmpty(theField.value))) return null;
	len = theField.value.length;
	if( len < 8 && len > 1 )
		return checkDigits( theField )
		
	var normalizedCCN = stripCharsInBag(theField.value, creditCardDelimiters)
  if (!isAnyCard(normalizedCCN))
     return 'error_credit_card';
  else
  {  theField.value = normalizedCCN
     return null
  }
}

function checkDigits( theField )
{
  cardval = theField.value
	len = cardval.length
	if( len % 2 == 0 )
	{
		cardval = '0' + cardval;
		len += 1;
	}
		
	sumodd = 0;
	sumeven = 0;
	oddlen = len - 1;
	checkDigit = cardval.substring( len-1, len )

	for( i=0; i < oddlen; i+=2 )
		sumodd += parseInt( cardval.substring( i, i+1 ) );
	
	for( i=1; i < len; i+=2 )
	{
	  val = parseInt( cardval.substring( i, i+1 ) ) * 2;
		if( val >= 10 )
			sumeven += Math.floor( val / 10 ) + val % 10;
		else
			sumeven += val;
	}
		
	total = sumodd + sumeven;
	sumdigit = 10 * Math.ceil( total / 10 ) - total;
	if( sumdigit == checkDigit )
		return null;

	return 'error_credit_card';
}

function checkCreditCard (radio, theField)
{ var cardType = getRadioButtonValue (radio)
  var normalizedCCN = stripCharsInBag(theField.value, creditCardDelimiters)
  if (!isCardMatch(cardType, normalizedCCN))
     return 'error_credit_card';
  else
  {  theField.value = normalizedCCN;
     return null;
  }
}

function checkCreditCardFields( theField, emptyOk, otherFields )
{ 
	var otherField = otherFields[0];
	if( !otherField )
		return null;
	
	var cardType = otherField.options[ otherField.selectedIndex ].value;
	if( cardType == -1 )
  		// assume that the first field is required and don't do any validation on this field right now
	  	return null;

	var normalizedCCN = stripCharsInBag( theField.value, creditCardDelimiters )
	var cardTypeName = null;
	switch( cardType )
	{
		case '0': cardTypeName = 'VISA'; break;
		case '1': cardTypeName = 'MASTERCARD'; break;
		case '2': cardTypeName = 'AMERICANEXPRESS'; break;
		case '3': cardTypeName = 'JCB'; break;
		case '4': cardTypeName = 'DISCOVER'; break;
		case '5': cardTypeName = 'DINERS'; break;
		case '6': cardTypeName = 'CARTEBLANCHE'; break;
		case '7': cardTypeName = 'ENROUTE'; break;
		case '8': cardTypeName = 'NEIMANMARCUS'; break;
		case '9': cardTypeName = 'BERGDORFGOODMAN'; break;
		case '10': cardTypeName = 'DAVIDMBRIAN'; break;
		case '11': cardTypeName = 'IKEA'; break;		
	}
	if( cardTypeName == null )
		return 'error_credit_card';

	if( !isCardMatch( cardTypeName, normalizedCCN ) )
     return 'error_credit_card';
	else
	{
		theField.value = normalizedCCN;
		return null;
	}
}

function checkPassword( theField, emptyOk, otherFields )
{
	var otherField = otherFields[0];
	var ret = checkString( theField, null, emptyOk );
	if( ret != null )
		return ret;

	if ( theField.disabled || otherField.disabled )
	{
		return ret;	
	}
	
	if( theField.value != otherField.value )
		return 'error_password_mismatch';
	// Check that the passwords meet the minimum length
	if ( (theField.value.length < passwordLength) || (otherField.value.length < passwordLength) )
		return 'error_min_length';
	return null;
}

function checkMonetaryAllowNulls( theField, emptyOk ) 
{
	var value = theField.value;

	if ( !value ) 
		return null;

	if ( !isDigit(value.substring(0,1)) ) 
	{
		value = value.substring(1,value.length+1);
		if ( isEmpty(value) ) 
			return 'error_non_positive_integer';
   	}

   	if ( isFloat( value, emptyOk ) )
   		return null;

	if( value == '0')
		return null;
		
	if( !isPositiveInteger( value, false ) )
		return 'error_non_positive_integer';
	else
		return null;
}

function checkPositiveIntegerAllowNulls( theField, emptyOk ) 
{
	if ( theField.disabled )
		return null;

	if ( !theField.value ) 
		return null;
		
	if ( btrim(theField.value) == '' )
		return null;
		
	return checkPositiveInteger( theField, emptyOk );
}

function checkPositiveInteger( theField, emptyOk )
{
	if ( theField.disabled )
		return null;

	if( theField.value == '0')
		return null;
		
	if( !isPositiveInteger( theField.value, false ) )
		return 'error_non_positive_integer';
	else
		return null;
}

function checkNonnegativeInteger( theField, emptyOk )
{

	if( !isNonnegativeInteger( theField.value, emptyOk ) )
		return 'error_non_negative_integer';
	else
		return null;
}

function checkPositiveFloat( theField, emptyOk )
{
	var theValue = btrim( theField.value );
	if( theValue == '' && !emptyOk )
		return 'error_required';
		
	if( theValue.indexOf( '-' ) != -1 )
		return 'error_non_positive_float';
		
	if( theValue == '0' || theValue == '0.0' )
		return null;
		
	if( !isFloat( theField.value ) )
		return 'error_non_positive_float';
	else
		return null;
}

function checkFutureShortDate( theField, emptyOk, otherFields )
{ 
	var otherField = otherFields[0];
	if( !otherField )
		return null;
	
	var year = otherField.options[ otherField.selectedIndex ].value;	
	var mth  = theField.options[ theField.selectedIndex ].value;		
		
	var d = new Date();
	
	if( mth < 0 )
		return 'error_card_month';
		
	if( mth < (d.getMonth()+1) && year <= d.getYear() )		
		return 'error_card_month';
		
	if( year <= 0 )  		
		return 'error_card_year';
		
	if( year < d.getYear() )
		return 'error_card_year';  
		
}

function isCreditCard(st) {
  if (st.length > 19)
  return (false);

  sum = 0; mul = 1; l = st.length;
  for (i = 0; i < l; i++) {
  digit = st.substring(l-i-1,l-i);
  tproduct = parseInt(digit ,10)*mul;
  if (tproduct >= 10)
    sum += (tproduct % 10) + 1;
  else
    sum += tproduct;
  if (mul == 1)
    mul++;
  else
    mul--;
  }
  if ((sum % 10) == 0)
  return (true);
  else
  return (false);

} 

function isVisa(cc)
{ if (((cc.length == 16) || (cc.length == 13)) &&
    (cc.substring(0,1) == 4))
  return isCreditCard(cc);
  return false;
}  

function isMasterCard(cc)
{ firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 16) && (firstdig == 5) &&
    ((seconddig >= 1) && (seconddig <= 5)))
  return isCreditCard(cc);
  return false;

} 

function isAmericanExpress(cc)
{ firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 15) && (firstdig == 3) &&
    ((seconddig == 4) || (seconddig == 7)))
  return isCreditCard(cc);
  return false;

} 

function isDinersClub(cc)
{ firstdig = cc.substring(0,1);
  seconddig = cc.substring(1,2);
  if ((cc.length == 14) && (firstdig == 3) &&
    ((seconddig == 0) || (seconddig == 6) || (seconddig == 8)))
  return isCreditCard(cc);
  return false;
}

function isCarteBlanche(cc)
{
  return isDinersClub(cc);
}

function isDiscover(cc)
{ first4digs = cc.substring(0,4);
  if ((cc.length == 16) && (first4digs == "6011"))
  return isCreditCard(cc);
  return false;
} 

function isEnRoute(cc)
{ first4digs = cc.substring(0,4);
  if ((cc.length == 15) &&
    ((first4digs == "2014") ||
     (first4digs == "2149")))
  return isCreditCard(cc);
  return false;
}

function isJCB(cc)
{ first4digs = cc.substring(0,4);
  if( cc.substring( 0,1 ) == 3 && cc.length == 16 ||
  		( ( first4digs == "2131" || first4digs == "1800" ) && cc.length == 15 ) )
  return isCreditCard(cc);
  return false;
} 

//function isJCB(cc)
//{ first4digs = cc.substring(0,4);
//  if ((cc.length == 16) &&
//    ((first4digs == "3088") ||
//     (first4digs == "3096") ||
//     (first4digs == "3112") ||
//     (first4digs == "3158") ||
//     (first4digs == "3337") ||
//     (first4digs == "3528")))
//  return isCreditCard(cc);
//  return false;
//} 

function isNeimanMarcus(cc)
{ 
  //if (((cc.length == 16) || (cc.length == 13)) &&
  //  (cc.substring(0,1) == 4))
  return isCreditCard(cc);
  //return false;
}  

function isBergdorfGoodman(cc)
{ 
  //if (((cc.length == 16) || (cc.length == 13)) &&
  //  (cc.substring(0,1) == 4))
  return isCreditCard(cc);
  //return false;
}  

function isDavidMBrian( cardNumber )
{
	return isCreditCard( cardNumber );
}

function isAnyCard(cc)
{ if (!isCreditCard(cc))
    return false;
  if (!isMasterCard(cc) && !isVisa(cc) && !isAmericanExpress(cc) && !isDinersClub(cc) &&
    !isDiscover(cc) && !isEnRoute(cc) && !isJCB(cc) && !isNeimanMarcus(cc) && !isBergdorfGoodman(cc) ) {
    return false;
  }
  return null;
} 

function isIKEA(cc)
{
	return ( cc.length == 16 );
}

function isCardMatch (cardType, cardNumber)
{ 
	cardType = cardType.toUpperCase();
	if( cardType == "VISA" && isVisa( cardNumber ) )
    	return true;
	if( cardType == "MASTERCARD" && isMasterCard( cardNumber ) )
		return true;
	if( ( (cardType == "AMERICANEXPRESS") || (cardType == "AMEX") )
    	&& (isAmericanExpress(cardNumber))) 
		return true;
	if((cardType == "DISCOVER") && (isDiscover(cardNumber)))
		return true;
	if((cardType == "JCB") && (isJCB(cardNumber)))
		return true;
	if((cardType == "DINERS") && (isDinersClub(cardNumber)))
		return true;
	if((cardType == "CARTEBLANCHE") && (isCarteBlanche(cardNumber)))
		return true;
	if((cardType == "ENROUTE") && (isEnRoute(cardNumber)))
		return true;
	if((cardType == "NEIMANMARCUS") && (isNeimanMarcus(cardNumber)))
		return true;
	if((cardType == "BERGDORFGOODMAN") && (isBergdorfGoodman(cardNumber)))
		return true;
	if( cardType == 'DAVIDMBRIAN' && isDavidMBrian( cardNumber ) )
		return true;
	if( cardType == 'IKEA' && isIKEA( cardNumber ) )
		return true;

	return false;
}  

function IsCC (st) {
  return isCreditCard(st);
}
function IsVisa (cc)  {
  return isVisa(cc);
}
function IsVISA (cc)  {
  return isVisa(cc);
}
function IsMasterCard (cc)  {
  return isMasterCard(cc);
}
function IsMastercard (cc)  {
  return isMasterCard(cc);
}
function IsMC (cc)  {
  return isMasterCard(cc);
}
function IsAmericanExpress (cc)  {
  return isAmericanExpress(cc);
}
function IsAmEx (cc)  {
  return isAmericanExpress(cc);
}
function IsDinersClub (cc)  {
  return isDinersClub(cc);
}
function IsDC (cc)  {
  return isDinersClub(cc);
}
function IsDiners (cc)  {
  return isDinersClub(cc);
}
function IsCarteBlanche (cc)  {
  return isCarteBlanche(cc);
}
function IsCB (cc)  {
  return isCarteBlanche(cc);
}
function IsDiscover (cc)  {
  return isDiscover(cc);
}
function IsEnRoute (cc)  {
  return isEnRoute(cc);
}
function IsenRoute (cc)  {
  return isEnRoute(cc);
}
function IsJCB (cc)  {
  return isJCB(cc);
}
function IsAnyCard(cc)  {
  return isAnyCard(cc);
}
function IsCardMatch (cardType, cardNumber)  {
  return isCardMatch (cardType, cardNumber);
}
function setFocusToFirstField()
{ 
if( document.forms[0].length > 0 )
  document.forms[0].elements[0].focus();
}

function noValidation() {}