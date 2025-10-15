<?php


defined( '_VALID_MOS' ) or die( 'Directe toegang tot deze locatie is niet toegestaan' );

@define('_LANG_NO_ACCESS', 'Directe toegang tot deze locatie is niet toegestaan');
@define('_LANG_INVALID_INPUT','De verzonden gegevens zijn geen valide invoer');
@define('_LANG_ACCESS_DENIED','Toegang tot deze bron is geweigerd');
@define('_LANG_GENERAL_ERROR','Een fout is opgetreden bij het uitvoeren van uw verzoek');
@define('_LANG_VARIABLE','Variabele');
@define('_LANG_VARIABLES','Variabelen');
@define('_LANG_GENERAL_ERROR', 'Een algemene fout is opgetreden');
@define('_LANG_EREG','Regular Expressions');
@define('_LANG_INPUT_NOT_IN_LIST','De gekozen invoer staat niet in de lijst');
@define('_LANG_INPUT_DOESNT_MATCH_REGEX','De invoer bevat niet toegestane karakters');
@define('_LANG_MISSING_INPUT','De invoer is verplicht maar ontbreekt');
@define('_LANG_SQLPROBLEM_EXECUTE','Kon de Query niet uitvoeren');
@define('_LANG_SQLPROBLEM_PARSE_FROM','Kon geen FROM statement in de  Query vinden');
@define('_LANG_DB_CONNECT_FAILED','Kon geen verbinding met de database maken');
@define('_LANG_UNSUPPORTED_DRIVER','Database foutief ingericht: gebruik van niet ondersteunde driver (besturingssoftware)');
@define('_LANG_LANG_FAILED_CUSTOM_QUERY','Kan de aangepaste Query niet uitvoeren voor QID ');
@define('_LANG_UNKNOWN_QID','Onbekende Query ID');
@define('_LANG_INVALID_VID','Niet valide variabele ID');
@define('_LANG_INPUT_RECEIVED','Bedankt voor uw invoer.');
@define('_LANG_PARAMETER_MISSING','Een functie werd opgeroepen zonder een verplichte parameter.');
@define('_LANG_KEYWORD','Sleutelwoord');
@define('_LANG_NO_VARIABLES_TO_DEACTIVATE','Geen variabelen om te deactiveren');
@define('_LANG_STATEMENT','Statement');
@define('_LANG_NO_TEMPLATE_LANGUAGE_FILE','Kan het taalbestand van de template (sjabloon) niet ophalen');
@define('_LANG_COULD_NOT_LOAD_FILE','Kon het gevraagde bestand niet laden');
@define('_LANG_TOO_LARGE','De invoer overschrijdt de gespecificeerde grootte');
@define('_LANG_INPUT_REQUIRED','U heeft niet de juiste invoer gegeven');

// New in 1.1
@define('_LANG_UNKNOWN_REGEX','Onbekende Regex invoer');
@define('_LANG_NO_RETRIEVE_SUB','Kon de lijstvervanging voor de variabele niet ophalen');
@define('_LANG_NO_DATABASE_CONFIG', 'Kon de database configuratie niet ophalen');

// New in 1.2
@define('_LANG_CANNOT_READ_DIRECTORY', 'Kan de bestanden niet uit de map lezen');

// New in 1.3
@define('_LANG_DIRECTORY_NOT_WRITTABLE', 'The bestemmingsmap kan niet worden beschreven');
@define('_LANG_FILE_EXISTS', 'De bestandsnaam bestaat al');
@define('_LANG_TEMP_FILE_MISSING', 'Het tijdelijke bestand bestaat niet');
@define('_LANG_FILE_TOO_LARGE', 'Het bestand overschrijdt de toegestane grootte');
@define('_LANG_FILE_WACKO', 'Het tijdelijke bestand wordt niet herkend als het bestand werd ge-upload');
// Vondeken add


@define('_LANG_STMT_INCOMPLETE', 'Aanvullende invoer is nodig om de Query aan te vullen');
@define('_LANG_INPUT_EXCEED_MAX_SIZE', 'De invoer overschrijdt de maximale grootte van');
@define('_LANG_CODE', 'Code');

// Required by the DBQBase class when evaling configuration info from the db
@define('_LANG_REQUIRED','Benodigdheden Opties');
@define('_LANG_CONFIG','DBQ Configuraties');
@define('_LANG_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_INPUT','Invoer Typen');
@define('_LANG_DBTYPE','Database Typen');
@define('_LANG_DBDRIVER','Database Drivers');
@define('_LANG_COUNTSQL','Count SQL');
@define('_LANG_DISPLAY_REGEX','Toon Regex');
@define('_LANG_USER_CONFIG','Gebruikersconfiguraties');
@define('_LANG_FILES','Template (sjabloon) bestanden');
?>


