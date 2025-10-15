<?php


/***************************************
 * $Id: english.php,v 1.3.2.2 2005/08/09 00:54:39 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.3.2.2 $
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// dbquery.php file
@define('_LANG_INVALID_INPUT','Requ&ecirc;te invalide');
@define('_LANG_ACCESS_DENIED','Acc&egrave;s refus&eacute;');
@define('_LANG_COULD_NOT_LOAD','Echec lors du chargement des donn&eacute;es');

// print.page.php
// New in 1.1
@define('_LANG_TEMPLATE_SELECT_QUERY','S&eacute;lectionner');
@define('_LANG_TEMPLATE_PREPARE_QUERY','Pr&eacute;parer');
@define('_LANG_TEMPLATE_EXECUTE_QUERY','Ex&eacute;cuter');
@define('_LANG_TEMPLATE_A_QUERY',' Une requ&ecirc;te');
@define('_LANG_TEMPLATE_CLOSE_WINDOW','Fermer cette fen&ecirc;tre');

// General
@define('_LANG_TEMPLATE_NO_ACCESS', 'Acc&egrave;s refus&eacute;');
@define('_LANG_TEMPLATE_GENERAL_ERROR', 'Une erreur est survenue');
@define('_LANG_TEMPLATE_STAR_INDICATES','Un ast&eacute;risque (*) indique un champ obligatoire');
@define('_LANG_TEMPLATE_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_TEMPLATE_BEGIN_RED_SPAN','<span style="color:red">');
@define('_LANG_TEMPLATE_END_RED_SPAN','</span>');
@define('_LANG_TEMPLATE_GENERAL_ERROR','Une erreur est survenue lors de l\'&eacute;x&eacute;cution de votre requ&ecirc;te');

// Select Query
@define('_LANG_TEMPLATE_NAME', 'Nom');
@define('_LANG_TEMPLATE_DESCRIPTION', 'Description');
@define('_LANG_TEMPLATE_NO_QUERIES_AVAILABLE', 'Aucune requ&ecirc;te disponible');

// PrepareQuery
@define('_LANG_TEMPLATE_FIELD', 'Attribut');
@define('_LANG_TEMPLATE_DATA', 'Option');
@define('_LANG_TEMPLATE_DESC', 'Description');
@define('_LANG_TEMPLATE_ENTER_OPTION', 'Choisissez une option');
@define('_LANG_TEMPLATE_SUBMIT', 'Envoyer');
@define('_LANG_TEMPLATE_MISSING_VARS_FROM_STATEMENT', 'Veuillez fournir toutes les valeurs demand&eacute;es');
// Added for 1.3
@define('_LANG_TEMPLATE_MAX_FILE_SIZE', 'Taille maximum d\'un fichier : ');

// ExecuteQuery.Results
@define('_LANG_TEMPLATE_EQWR_RESULTS', 'Voici les r&eacute;sultats qui r&eacute;pondent &agrave; vos crit&egrave;res');
@define('_LANG_TEMPLATE_DISPLAY','Afficher');

// ExecuteQuery.woResults
@define('_LANG_TEMPLATE_EQWR_NO_RESULTS', 'Aucun enregistrement ne correspond &agrave; votre choix');
@define('_LANG_TEMPLATE_INPUT_RECEIVED','Merci de votre contribution');

// ExecuteQuery.Failure.html.php
@define('_LANG_TEMPLATE_QUERY_NO_GO','Impossible d\'&eacute;x&eacute;cuter la requ&ecirc;te');
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_ABOVE','Please complete ze following form');
@define('_LANG_TEMPLATE_INPUT_DESCRIPTION_NAME','Nom tu');
@define('_LANG_TEMPLATE_INPUT_COMMENT_AMOUNT','Enter ze money');

?>
