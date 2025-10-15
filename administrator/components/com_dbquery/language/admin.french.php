<?php

/***************************************
 * $Id: admin.english.php,v 1.9 2005/06/16 21:53:13 tcp Exp $
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.9 $
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

@define("_LANG_ID","ID");
@define('_LANG_NAME','Nom');
@define('_LANG_DESCRIPTION','Description');
@define('_LANG_ACCESS_DENIED','Accès à cette ressource interdit');
@define('_LANG_INPUT_REQUIRED','Vous n\'avez pas renseigné tous les champs requis');
@define('_LANG_YES','Oui');
@define('_LANG_NO','Non');
@define('_LANG_COMMENT','Commentaire');
@define('_LANG_LABEL','Légende');
@define('_LANG_REQUIRED','Requis');
@define('_LANG_SIZE','Taille');
@define('_LANG_PUBLISHED','Publié');
@define('_LANG_CATEGORY','Catégorie');
@define('_LANG_CATEGORIES','Catégories');
@define('_LANG_PARSED','Parsé');
@define('_LANG_PARSE','Parser');
@define('_LANG_PREVIEW','Aperçu');
@define('_LANG_ACCESS','Accès');
@define('_LANG_REORDER','Réorganiser');
@define('_LANG_HITS','Hits');
@define('_LANG_ORDER','Ordre');
@define('_LANG_CHECKED_OUT','Validé');
@define('_LANG_HOSTNAME','H&eacute;bergeur SQL');
@define('_LANG_ONLINE','En ligne');
@define('_LANG_SEARCH','Recherche');
@define('_LANG_DISPLAY','Affichage');
@define('_LANG_SELECT','Sélectionner ');
@define('_LANG_GROUP','Groupe');
@define('_LANG_TYPE','Type');
@define('_LANG_REGEX','Regex');
@define('_LANG_DB', 'Base de données');
@define('_LANG_DBS', 'Bases de données');
@define('_LANG_ALL','Tous ');
@define('_LANG_EDIT','Editer');
@define('_LANG_ADD','Ajouter');
@define('_LANG_EDIT_DATABASE','Editer BDD');
@define('_LANG_ADD_DATABASE','Ajouter BDD');
@define('_LANG_FIELD_REQUIRED','Vous devez fournir une valeur pour: ');
@define('_LANG_USES_VARIABLES','Utiliser variables');
@define('_LANG_DISPLAY_NAME','Titre');
@define('_LANG_STATEMENT','Rapport');
@define('_LANG_LIST','Liste');
@define('_LANG_KEYWORD','Mot-clef');
@define('_LANG_CONFIGURATION','Configuration');
@define('_LANG_CONFIGURATIONS','Configurations');
@define('_LANG_QUERY','Requête');
@define('_LANG_QUERIES','Requêtes');
@define('_LANG_STAR_INDICATES','Un astérisque (*) indique un champ obligatoire');
@define('_LANG_REQUIRED_MARK','<SPAN style="color:red">*</SPAN>');
@define('_LANG_BEGIN_RED_SPAN','<SPAN style="color:red">');
@define('_LANG_END_RED_SPAN','</SPAN>');
@define('_LANG_KEY','Clé');
@define('_LANG_VALUE','Valeur');
@define('_LANG_INPUT','Input Types');
@define('_LANG_DBTYPE','Types de BDDs');
@define('_LANG_DBDRIVER','Pilotes de la base de donn&eacute;es');
@define('_LANG_COUNTSQL','Compteur SQL');
@define('_LANG_DISPLAY_REGEX','Afficher les Regex');
@define('_LANG_SUBSTITUTION','Substitution');
@define('_LANG_SUBSTITUTIONS','Substitutions');
@define('_LANG_TEMPLATE','Template');
@define('_LANG_MAX_SIZE','La taille maximale autorisée est : ');
@define('_LANG_EQWR_NO_RESULTS','Aucune donnée ne correspond à votre sélection');
@define('_LANG_EQWR_RESULTS',"Résultats de la requête: ");
@define('_LANG_PARENT','Parent');
@define('_LANG_QUERY_NO_GO','Votre requête risque de ne pas être éxécutée');
@define('_LANG_INPUT_RECEIVED','Merci de votre contribution');
@define('_LANG_YOUR_SQL','Votre SQL : ');
@define('_LANG_REPRESENTED_BY','Représenté par : ');
@define('_LANG_IN_STATEMENT','Trouvé dans le rapport : ');
@define('_LANG_NO_PASS_REGEX','a échoué lors du test regex');
@define('_LANG_PAGE_COMPANY','Professional Consulting');
@define('_LANG_PAGE_SUPPORT','Support et FAQ');
@define('_LANG_PAGE_FORUM','Forum');
@define('_LANG_PAGE_DOWNLOAD','Télécharger');
@define('_LANG_DBQ_VERSION','Version deDBQ');
@define('_LANG_PHP_VERSION','Version de PHP');
@define('_LANG_MAMBO_VERSION','Version de Mambo');
@define('_LANG_WEBSERVER_VERSION','Version du serveur web');
@define('_LANG_SUBMISSIONS','Soumissions');
@define('_LANG_STATS','Stats');
@define('_LANG_USER_CONFIG','Configurations utilisateur');

// admin screens
// Added for 1.1
@define('_LANG_ADMIN_IN_USE','Une autre personne est en train d\'éditer cet enregistrement');
@define('_LANG_SELECT_ITEM', 'Sélectionnez');
@define('_LANG_ITEM_NOT_DELETED',' L\'item n\'a pu être supprimé');
@define('_LANG_ERROR', 'Erreur: ');
@define('_LANG_COULDNOT_PUBLISH', 'L\'item n\'a pu être (dé)publié');
@define('_LANG_ACTIVE','Activé');
@define('_LANG_TIME_START', 'De');
@define('_LANG_TIME_END', 'Jusqu\'à');
@define('_LANG_INVALID_DATE_RANGE','Intervalle de dates invalide');
@define('_LANG_MANAGER','Manager');
@define('_LANG_DIRECTORY','Dossier');
@define('_LANG_AUTHOR','Auteur');
@define('_LANG_VERSION','Version');
@define('_LANG_DATE','Date');
@define('_LANG_AUTHOR_URL','URL de l\'auteur');
@define('_LANG_INFORMATION', 'Information');

// Added for 1.2
@define('_LANG_DEFAULT', 'Défaut');
@define('_LANG_OPENING_HELP_WINDOW', 'En train d\'ouvrir la fenêtre d\'aide...');
@define('_LANG_PERCENT', 'Pourcent');


// details.html.php
// Added for 1.1
@define('_LANG_DETAILS_CONFIG','Configuration');
@define('_LANG_DETAILS_ATTRIBUTES','Attributs');
@define('_LANG_DETAILS_TEXT','Texte');
@define('_LANG_DETAILS_PARSE','Parser');
// Added for 1.2
@define('_LANG_DETAILS_PARAMS','Paramètres');
@define('_LANG_DETAILS_DEFAULT_VALUE', 'Valeur par défaut');
@define('_LANG_DETAILS_LIST', 'Liste');

// summary.html.php
// Added for 1.1
@define('_LANG_FILTER','Filtre: ');
@define('_LANG_UNDER_DEVELOPMENT','Cette fonction est encore en développement');
@define('_LANG_ON_LINE','En Line');
@define('_LANG_COPY_OF','Copie de ');

// internal in class files but used only by admin screens
// Added for 1.1
@define('_LANG_CHANGES_SAVED', 'Changements sauvegardés');
@define('_LANG_CHANGES_NOT_SAVED', 'Sauvegarde des changements impossible');
@define('_LANG_UNKNOWN','Inconnu');

// support.html.php
// Added for 1.0
@define('_LANG_PEAR_SUPPORT','Support de l\'interface PEAR');
// Added for 1.1
@define('_LANG_MAMBO_SUPPORT','Support de l\'interface Mambo Database');
@define('_LANG_ADODB_SUPPORT','Support de l\'interface ADODB');
@define('_LANG_MYSQL_SUPPORT','Support de l\'interface MySQL');
@define('_LANG_MYSQLI_SUPPORT','Support de l\'interface MySQLi');
// Added for 1.2
@define('_LANG_INCLUDE_PATH', 'Chemin pour include()');

// details.html.php
// Added for 1.3
@define('_LANG_DETAILS_NOTIFICATION', 'Notifications');
@define('_LANG_DETAILS_SPECIAL', 'Spécial');

// General 1.3 Additions
@define ('_LANG_DETAILS_CONTACT','Notifications');
@define ('_LANG_UPDATE','Mettre à jour');

// Template Editing Text
// Added for 1.3
@define('_LANG_TEMPLATE_NOT_FOUND', 'Impossible de trouver le fichier template');
@define('_LANG_TEMPLATE_NOT_READABLE', 'Impossible de lire le fichier template');
@define('_LANG_TEMPLATE_NOT_WRITABLE', 'Impossible d\'écrire dans le fichier template');
@define('_LANG_DIRECTORY_NOT_FOUND', 'Dossier non trouvé');
@define('_LANG_DIRECTORY_NOT_READABLE', 'Lecture du dossier impossible');
@define('_LANG_DIRECTORY_NOT_WRITABLE', 'Impossible d\'écrire dans le dossier');
@define('_LANG_DIRECTORY_COPIED', 'Dossier copié avec succès');
@define('_LANG_DIRECTORY_NOT_COPIED', 'Le dossier n\'a pu être copié');

// File Editor
// Added for 1.3
@define('_LANG_FILE_EDITOR', 'Editeur de fichiers');
@define('_LANG_FILE_IS', 'Nom du fichier : ');
@define('_LANG_IS_WRITABLE', 'Libre en écriture');
@define('_LANG_IS_UNWRITABLE', 'Protégé contre l\'écriture');

// Error Reporting
@define('_LANG_ERRORS', 'Erreurs');
@define('_LANG_SOURCE', 'Source');
@define('_LANG_OBJECT', 'Objet');
@define('_LANG_DATE_REPORTED', 'Date repoussée');
@define('_LANG_MESSAGE', 'Message');
@define('_LANG_PRIORITY', 'Priorité');
?>
