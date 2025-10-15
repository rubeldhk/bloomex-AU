<?php

/***************************************
 * $Id: french.php,v 1.10.2.2 2005/08/09 00:54:39 tcp Exp $
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @Translated to french by pangel
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.10.2.2 $
 **/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

@define('_LANG_NO_ACCESS','Direct Access to this location is not allowed.');
@define('_LANG_INVALID_INPUT','Type de donn&eacute;es invalide');
@define('_LANG_ACCESS_DENIED','Vous n\'avez pas l\'autorisation d\'acc&eacute;der &agrave; cette ressource. ');
@define('_LANG_GENERAL_ERROR','Une erreur est survenue durant le traitement de votre requ&ecirc;te');
@define('_LANG_VARIABLE','Variable');
@define('_LANG_VARIABLES','Variables');
@define('_LANG_GENERAL_ERROR', 'Une erreur est survenue');
@define('_LANG_EREG','Expressions R&eacute;guli&egrave;res');
@define('_LANG_INPUT_NOT_IN_LIST','Le champ s&eacute;lectionn&eacute; n\'est pas dans la liste');
@define('_LANG_INPUT_DOESNT_MATCH_REGEX','Le champ contient des caract&egrave;res non autoris&eacute;s');
@define('_LANG_MISSING_INPUT','Un champ manquant est n&eacute;cessaire');
@define('_LANG_SQLPROBLEM_EXECUTE','Impossible d\'&eacute;x&eacute;cuter la requ&ecirc;te ');
@define('_LANG_SQLPROBLEM_PARSE_FROM','Impossible de trouver la clause FROM dans la requ&ecirc;te ');
@define('_LANG_DB_CONNECT_FAILED','Connexion &agrave; la base de donn&eacute;es impossible');
@define('_LANG_UNSUPPORTED_DRIVER','Erreur de configuration de la base de donn&eacute;es : pilote non support&eacute;');
@define('_LANG_LANG_FAILED_CUSTOM_QUERY','Echec lors de l\'&eacute;x&eacute;cution de la requ&ecirc;te personnalis&eacute;e pour QID ');
@define('_LANG_UNKNOWN_QID','Query ID (ID de requ&ecirc;te) inconnu');
@define('_LANG_NO_VARIABLES_TO_DEACTIVATE','Aucune variable &agrave; d&eacute;sactiver');
@define('_LANG_INVALID_VID','ID de variable invalide');
@define('_LANG_INPUT_RECEIVED','Merci de votre contribution');
@define('_LANG_PARAMETER_MISSING','Un param&egrave;tre n&eacute;cessaire a &eacute;t&eacute; omis lors de l\'appel d\'une fonction');
@define('_LANG_KEYWORD','Mot-clef');
@define('_LANG_STATEMENT','Clause');
@define('_LANG_NO_TEMPLATE_LANGUAGE_FILE','Unable to retrieve the template language file');
@define('_LANG_COULD_NOT_LOAD_FILE','Echec lors du chargement du fichier');
@define('_LANG_TOO_LARGE','Nombre maximal de caract&egrave;res d&eacute;pass&eacute; pour le champ');
@define('_LANG_INPUT_REQUIRED','Vous n\'avez pas renseign&eacute; tous les champs requis');

// New in 1.1
@define('_LANG_UNKNOWN_REGEX','Champ regex inconnu');
@define('_LANG_NO_RETRIEVE_SUB','Impossible de r&eacute;cup&eacute;rer les listes de substituion de la variable');
@define('_LANG_NO_DATABASE_CONFIG', 'Impossible de r&eacute;cup&eacute;rer la configuration de la base de donn&eacute;es');

// New in 1.2
@define('_LANG_CANNOT_READ_DIRECTORY', 'Lecture de fichiers impossible &agrave; partir du dossier ');

// New in 1.3
@define('_LANG_DIRECTORY_NOT_WRITTABLE', 'Le dossier destination est prot&eacute;g&eacute; en &eacute;criture');
@define('_LANG_FILE_EXISTS', 'Un fichier du m&ecirc;me nom existe d&eacute;j&agrave;');
@define('_LANG_TEMP_FILE_MISSING', 'Le fichier temporaire n\'existe pas');
@define('_LANG_FILE_TOO_LARGE', 'Le fichier est trop volumineux');
@define('_LANG_FILE_WACKO', 'Aucun lien entre le fichier envoy&eacute; et le fichier temporaire cr&eacute;e');
@define('_LANG_STMT_INCOMPLETE', 'Le requ&ecirc;te doit &ecirc;tre compl&eacute;t&eacute;e par des champs suppl&eacute;mentaires');
@define('_LANG_INPUT_EXCEED_MAX_SIZE', 'La cha&icirc;ne d&eacute;passe la longueur maximal autoris&eacute;e : ');
@define('_LANG_CODE', 'Code');

// Required by the DBQBase class when evaling configuration info from the db
@define('_LANG_REQUIRED','Options de requ&ecirc;te');
@define('_LANG_CONFIG','Configurations de DBQ');
@define('_LANG_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_INPUT','Types de champs');
@define('_LANG_DBTYPE','Types de BDDs');
@define('_LANG_DBDRIVER','Pilotes de la base de donn&eacute;es');
@define('_LANG_COUNTSQL','Compteur SQL');
@define('_LANG_DISPLAY_REGEX','Afficher les Regex');
@define('_LANG_USER_CONFIG','Configurations utilisateur');
@define('_LANG_FILES','Fichier de templates');

?>
