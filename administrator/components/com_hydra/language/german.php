<?php
/**
* $Id: german.php 21 2007-04-16 10:47:37Z eaxs $
* @package   Project Fork
* @copyright Copyright (C) 2006-2007 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Project Fork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );

/**
* @desc    GENERAL STUFF
* @version 0.6.5
**/
define ('HL_SYS_MESSAGE', 'System Nachricht');
define ('HL_HOMEPAGE', 'Homepage');
define ('HL_PRINT', 'Drucken');


/**
* @desc    FILE MANAGER
* @version 0.6.5
**/
define ('HL_FILES_UPLOADPATH_NOT_WRITABLE', 'Uploadpfad nicht beschreibbar!');

/**
* @desc    PROJECTS AND TASKS
* @version 0.6.5
**/
define ('HL_PARENT_TASK', 'Übergeordnet');
define ('HL_NO_PARENT', 'Nein');

define ('HL_PROJECT_DIRECTORIES', 'Ordner');
define ('HL_PROJECT_DOCUMENTS', 'Dokumente');
define ('HL_PROJECT_FILES', 'Dateien');

define ('HL_TASK_PRIORITY', 'Priorität');
define ('HL_TASK_PRIORITY_DESC', 'Setzen Sie die Priorität für diese Aufgabe');
define ('HL_TASK_PRIORITY_LOW', 'Geringe Priorität');
define ('HL_TASK_PRIORITY_MED', 'Mittlere Priorität');
define ('HL_TASK_PRIORITY_HI', 'Hohe Priorität');

define ('HL_TASK_ASSIGNED_TO', 'Zugewiesen');
define ('HL_CUSTOM_STATUS', 'Eigener Status');
define ('HL_CUSTOM_STATUS_DESC', 'Geben Sie die Beschaffenheit an. Zum Beispiel: Bug, Feature etc...');
define ('HL_NOTIFICATION', 'Benachrichting');
define ('HL_NOTIFICATION_ENABLED', 'Benachrichting ist eingeschaltet');
define ('HL_NOTIFICATION_DISABLED', 'Benachrichting ist ausgeschaltet');
define ('HL_NOTIFICATION_ENABLE', 'Benachrichting einschalten');
define ('HL_NOTIFICATION_DISABLE', "Benachrichting ausschalten");
define ('HL_NOTIFICATION_TASK_UPDATE_EMAIL_SUBJECT', "{user} hat eine Aufgabe geändert:");
define ('HL_NOTIFICATION_TASK_NEW_EMAIL_SUBJECT', "{user} hat eine neue Aufgabe erstellt:");
define ('HL_NOTIFICATION_TASK_ASSIGNED_EMAIL_SUBJECT', "{user} hat Ihnen eine Aufgabe zugeteilt:");
define ('HL_NOTIFY_USER', "Nutzer benachrichtigen?");

/**
* @desc    CONTROLPANEL
* @version 0.6.5
**/
define ('HL_DEBUG', 'Project Fork debuggen');
define ('HL_DEBUG_DESC', 'Zeigt die System Konsole');
define ('HL_HYDRA_TIMEFORMAT', 'Zeit Format');
define ('HL_RAW_OUTPUT', 'Rohe Ausgabe');
define ('HL_RAW_OUTPUT_DESC', 'Zwingt Joomla! dazu, ausser Project Fork nichts darzustellen');


/**
* @desc    SYSTEM MESSAGES
* @version 0.6.5
**/
define ('HL_MSG_PROJECT_CREATED', 'Das Projekt wurde erstellt!');
define ('HL_MSG_PROJECT_MODIFIED', 'Das Projekt wurde geändert');
define ('HL_MSG_PROJECTS_DELETED', 'Die gewählten Projekte wurde gelöscht!');
define ('HL_MSG_TASK_CREATED', 'Aufgabe wurde erstellt');
define ('HL_MSG_TASK_MODIFIED', 'Aufgabe wurde geändert');
define ('HL_MSG_TASK_DELETED', 'Die ausgewählten Aufgaben wurden gelöscht!');
define ('HL_MSG_PROGRESS_UPDATED', 'Fortschritt wurde geändert!');
define ('HL_MSG_HACKER', 'Hack Versuch!');
define ('HL_MSG_HACKER_PROJECT', 'Das gewählte Projekt existiert nixht oder ist nicht verfügbar!');
define ('HL_MSG_HACKER_TASK', 'Die gewählte Aufgabe existiert nicht oder ist nicht verfügbar!');
define ('HL_MSG_NOTIFICATION_REMOVED', 'Benachrichtigung wurde ausgeschaltet.');
define ('HL_MSG_NOTIFICATION_ADD', 'Benachrichtigung wurde eingeschaltet.');
define ('HL_MSG_FOLDER_CREATED', 'Ordner wurde erstellt');
define ('HL_MSG_FOLDER_MODIFIED', 'Ordner wurde geändert');
define ('HL_MSG_FOLDER_DELETED', 'Ordner wurde gelöscht');
define ('HL_MSG_DIR_NOT_AVAILABLE', 'Dieses Verzeichnis existiert nicht oder ist nicht verfügbar!');
define ('HL_MSG_COMMENT_CREATED', 'Kommentar wurde erstellt!');
define ('HL_MSG_COMMENT_MODIFIED', 'Kommentar wurde geändert!');
define ('HL_MSG_COMMENT_DELETED', 'Kommentar wurde gelöscht!');
define ('HL_MSG_PROFILE_UPDATED', 'Ihr Profil wurde geändert!');

/**
* @desc    PERMISSION LABELS
* @version 0.6.5
**/
define ('HL_CMD_TASK_NOTIFICATION', 'Kann Aufgaben-Benachrichtigung aktivieren');
define ('HL_CMD_TASK_VIEWCOMMENTS', 'Kann Kommentare sehen');
define ('HL_CMD_TASK_ADDCOMMENTS', 'Kann Kommentare schreiben');
define ('HL_CMD_TASK_DELCOMMENTS', 'Kann eigene Kommentare löschen');

/***********************HYDRA060+061***********************/


/**
* @desc    GENERAL LANGUAGE
**/
/**
* @version 0.6.0
**/
define ('HL_NAV_BOX', 'Navigation');
define ('HL_SETTINGS_BOX', 'Einstellungen');
define ('HL_CONTROLPANEL', 'Controlpanel');
define ('HL_PROJECTS', 'Projekte');
define ('HL_PROJECT', 'Projekt');
define ('HL_FILES', 'Dateimanager');
define ('HL_TASKS', 'Aufgaben');
define ('HL_CALENDAR', 'Kalender');

define ('HL_AVAILABLE_ACTIONS', 'Verfügbare Aktionen');
define ('HL_SORT_ASC', 'Aufsteigend Sortieren');
define ('HL_SORT_DESC', 'Absteigend Sortieren');
define ('HL_GO_BACK', 'Zurück');
define ('HL_CREATED_BY', 'Erstellt von');
define ('HL_NAME', 'Name');
define ('HL_USERNAME', 'Benutzer-Name');
define ('HL_EDIT', 'Bearbeiten');
define ('HL_LANG', 'Sprache');
define ('HL_DISPLAY', 'Zeige');
define ('HL_THEME', 'Theme');
define ('HL_IMPORT', 'Importieren');
define ('HL_FORM_ALERT', 'Bitte vervollständigen Sie ihre Angaben!');
define ('HL_GENERAL_INFO', 'Allgemeine Informationen');
define ('HL_GENERAL_SETTINGS', 'Allgemeine Einstellungen');
define ('HL_USER_TYPE', 'Benutzer-Type');
define ('HL_USER_TYPE_CLIENT', 'Kunde');
define ('HL_USER_TYPE_MEMBER', 'Mitglied');
define ('HL_USER_TYPE_GROUPLEADER', 'Gruppenleiter');
define ('HL_USER_TYPE_ADMINISTRATOR', 'Administrator');
define ('HL_FILTER', 'Filter');
define ('HL_APPLY_FILTER', 'Filter anwenden');
define ('HL_WELCOME', 'Willkommen');
define ('HL_OPEN_MENU', 'Clicken um das Menu zu öffnen');
define ('HL_USER', 'Benutzer');
define ('HL_ADD', 'Hinzufügen');
define ('HL_DELETE', 'Löschen');
define ('HL_SHOW', 'Zeige');
define ('HL_CONFIRM', 'Sind Sie sicher ?');
/**
* @version 0.6.1
**/
define ('HL_ACCESS_DENIED', 'Zugriff verweigert');
define ('HL_IS_WRITEABLE', 'Nicht schreibgeschützt');
define ('HL_NOT_WRITEABLE', 'Schreibgeschützt');


/**
* @desc    CONTROLPANEL
**/
/**
* @version 0.6.0
**/
define ('HL_MY_PROFILE', 'Mein Profil');
define ('HL_SYSTEM_SETTINGS', 'System');
define ('HL_USER_GROUPS', 'Gruppen');
define ('HL_GROUPS', 'Gruppen');
define ('HL_USERS', 'Benutzer');
define ('HL_JOOMLAUSERS', 'Joomla-Benutzer');
define ('HL_EMAIL', 'Email');
define ('HL_USERS_MANAGEMENT', 'Benutzer-Verwaltung');
define ('HL_NO_USERGROUPS', 'Keine Benutzergruppen vorhanden.');
define ('HL_NEW_USERGROUP', 'Neue Benutzergruppe');
define ('HL_DELETE_USERGROUPS', 'Benutzergruppe(n) löschen');
define ('HL_CREATE_USERGROUP', 'Benutzergruppe erstellen');
define ('HL_EDIT_USERGROUP', 'Benutzergruppe ändern');
define ('HL_GROUP_NAME', 'Gruppen-Name');
define ('HL_GROUP_NAME_DESC', 'Bitte geben Sie einen Namen ein.');
define ('HL_GROUP_DESC', 'Beschreibung');
define ('HL_GROUP_MEMBERS_AND_PERMS', 'Gruppen-Mitglieder und Rechte');
define ('HL_GROUP_MEMBERS', 'Gruppen-Mitglieder');
define ('HL_GROUP_DELETE_WARN', 'Bitte wählen Sie eine Gruppe aus der Liste!');
define ('HL_PERMS_FOR', 'Rechte für');
define ('HL_IMPORT_JOOMLA_USERS', 'Joomla-Benutzer importieren');
define ('HL_DELETE_USERS', 'Benutzer löschen');
define ('HL_DELETE_USERS_WARN', 'Bitte wählen Sie einen Benutzer aus der Liste!');
define ('HL_IMPORT_JOOMLA_USERS_WARN', 'Bitte wählen Sie einen Benutzer asu der Liste!');
define ('HL_IMPORT_SELECTED', 'Markierte importieren');
define ('HL_SETTINGS_FOR', 'Einstellungen für');
define ('HL_NO_JOOMLAUSERS', 'Es wurden alle Joomla-Benutzer importiert!');
define ('HL_NO_HYDRAUSERS', 'Es sind keine Importierten Benutzer verfügbar!');
define ('HL_HYDRA_VERSION', 'Project Fork Version');
define ('HL_UPLOAD_SETTINGS', 'Upload Einstellungen');
define ('HL_UPLOAD_STORAGE_PATH', 'Upload Pfad');
define ('HL_SAVE_SETTINGS', 'Einstellungen speichern');
define ('HL_USER_GROUP', 'Benutzergruppe');
define ('HL_HYDRA_TIMEOFFSET', 'Zeitzone');
define ('HL_HYDRA_LATEST_TASKS', 'Aktuelle Aufgaben');
define ('HL_HYDRA_LATEST_FILES', 'Aktuelle Dateien');
define ('HL_HYDRA_LATEST_COMMENTS', 'Aktuelle Kommentare');
define ('HL_GRANT', 'Gestatten');
define ('HL_PERMISSION', 'Recht');
define ('HL_REQUIRED_TYPE', 'Ben. Nutzer-Typ');
define ('HL_OR_HIGHER', 'oder höher');
define ('HL_UPCOMING_EVENTS', 'Anstehende Termine');
define ('HL_NO_UPCOMING_EVENTS', 'Es stehen keine Termine an');
define ('HL_EDIT_REGISTRY', 'Registry bearbeiten');
define ('HL_REG_AREA', 'Area');
define ('HL_REG_CMD', 'Command');
define ('HL_REG_USER_TYPE', 'Ben. Nutzer-Typ');
define ('HL_REG_AREA_LABEL', 'Area Label');
define ('HL_REG_CMD_LABEL', 'Command Label');
define ('HL_REG_INHERIT', 'Vererbt von');
define ('HL_REG_AREA_HLP', 'Area definiert eine Art Sektion in Project Fork.');
define ('HL_REG_CMD_HLP', 'Command ist ein Befehl der von einem Benutzer ausgeführt werden kann.');
define ('HL_REG_USER_TYPE_HLP', 'User-Type ist der Benutzer-Typ den ein Benutzer mindestens haben muss, um den Befehl ausführen zu können.');
define ('HL_REG_AREA_LABEL_HLP', 'Area Label definiert den Namen der Sektion. Er muss in der Sprachdatei definiert sein!');
define ('HL_REG_CMD_LABEL_HLP', 'Command Label definiert den Namen des Befehls. Er muss in der Sprachdatei definiert sein');
define ('HL_REG_INHERIT_HLP', 'Einige Befehle werden von Eltern-Befehlen vererbt.');
define ('HL_REG_WARNING', 'Achtung! Vor dem Bearbeiten bitte lesen!');
define ('HL_REG_WARNING_TXT', "Ändern Sie nichts bevor Sie nicht wissen was Sie tun!<br/>Bitte lesen Sie die folgenden Beschreibung!");
define ('HL_REG_ADD_ENTRY', 'Neuen Eintrag hinzufügen');
define ('HL_REG_DEL_ENTRIES', 'Markierte Einträge löschen');
define ('HL_REG_DEL_ENTRIES_WARN', 'Bitte wählen Sie einen Eintrag aus der Liste!');
define ('HL_REG_DEL_ENTRIES_CONFIRM', 'Sind Sie sicher ?');
define ('HL_REGISTRY', 'Registry');
define ('HL_UPDATE_REGISTRY', 'Registry aktualisieren');
define ('HL_CHECK_HOMEPAGE_UPDATES', 'Homepage besuchen / Nach Updates suchen');
define ('HL_SHOW_QUICKPANEL', 'Zeige Quickpanel');
define ('HL_SHOW_LATEST_TASKS', 'Zeige aktuelle Aufgaben');
define ('HL_SHOW_UPCOMING_EVENTS', 'Zeige anstehende Termine');
define ('HL_HIGHLIGHT_LATE_TASKS', 'Hebe Aufgaben hervor, die in Verzug sind');
define ('HL_INSTALLED_LANGUAGES', 'Installierte Sprachen');
define ('HL_INSTALLED_THEMES', 'Installierte Themes');
define ('HL_DELETE_LANG', 'Lösche Sprache');
define ('HL_DELETE_THEME', 'Lösche Theme');
define ('HL_DELETE_THEME_INFO', 'Das Standard-Theme (Joomla) kann nicht gelöscht werden.');
define ('HL_DELETE_LANG_INFO', 'Die Standard-Sprache (English) kann nicht gelöscht werden.');


/**
* @desc    PROJECTS AND TASKS
**/
/**
* @version 0.6.0
**/
define ('HL_NEW_PROJECT', 'Neues Projekt');
define ('HL_DEL_PROJECT', 'Lösche Projekt(e)');
define ('HL_CREATE_PROJECT', 'Erstelle Projekt');
define ('HL_PROJECT_NAME', 'Projekt-Name');
define ('HL_PROJECT_NAME_DESC', 'Bitte geben Sie einen Namen an');
define ('HL_PROJECT_DESC', 'Projekt-Beschreibung');
define ('HL_PROJECT_TIME_LIMIT', 'Projekt-Start und Ende');
define ('HL_PROJECT_START_TIME', 'Projekt-Start');
define ('HL_PROJECT_START_TIME_DESC', 'Wann startet das Projekt');
define ('HL_PROJECT_END_TIME', 'Projekt-Ende');
define ('HL_PROJECT_END_TIME_DESC', 'Wann sollte das Projekt abgeschlossen sein');
define ('HL_PROJECT_GROUPS', 'Projekt-Gruppen');
define ('HL_PROJECT_ACCESS', 'Projekt-Zugriff');
define ('HL_NO_PROJECTS', 'Es sind keine Projekte verfügbar');
define ('HL_STATUS', 'Status');
define ('HL_STATUS_COMPLETE', 'Abgeschlossen');
define ('HL_BEHIND_SCHEDULE', ' Tage in Verzug');
define ('HL_EDIT_PROJECT', 'Änderung speichern');
define ('HL_PROGRESS', 'Fortschritt');
define ('HL_TOTAL_TASKS', 'Aufgaben Gesamt');
define ('HL_ACCESS', 'Zugriff');
define ('HL_TIME_LEFT', 'Verbleibende Zeit');
define ('HL_TIME_TOTAL', 'Zeit Insgesamt');
define ('HL_DAYS', 'Tage');
define ('HL_SHOW_TASKS', 'Zeige Aufgaben');
define ('HL_NEW_TASK', 'Neue Aufgabe');
define ('HL_TASK_NAME_DESC', 'Bitte geben Sie einen Namen ein');
define ('HL_DEL_PROJECT_WARN', 'Bitte wählen Sie ein Projekt aus der Liste!');
define ('HL_TASK_TASK_DESC', 'Details');
define ('HL_TIME_LIMIT_AND_PROGRESS', 'Deadline und Fortschritt');
define ('HL_TASK_START', 'Aufgaben-Start');
define ('HL_TASK_START_DESC', 'Wann soll mit der Aufgabe begonnen werden');
define ('HL_TASK_END', 'Aufgaben-Ende');
define ('HL_TASK_END_DESC', 'Wann sollte die Aufgabe abgeschlossen sein');
define ('HL_TASK_PROGRESS_DESC', 'Der aktuelle Fortschritt der Aufgabe');
define ('HL_TASK_LINKS', 'Verknüpfungen');
define ('HL_CREATE_TASK', 'Erstelle Aufgabe');
define ('HL_DELETE_TASK', 'Lösche Aufgabe(n)');
define ('HL_NO_TASKS', 'Es gibt keine Aufgaben.');
define ('HL_EDIT_TASK', 'Aufgabe speichern');
define ('HL_TASK_INFO', 'Info - Klicken, um Details zu sehen');
define ('HL_ASSIGNED_TO', 'Zuständig');
define ('HL_FILTER_COMPLETED_TASKS', 'Abgeschlossene Aufgaben ausblenden');
define ('HL_FILTER_UNCOMPLETED_TASKS', 'Unvollständige Aufgaben ausblenden');
define ('HL_FILTER_SHOW_ALL_TASKS', 'Alle Aufgaben zeigen');
define ('HL_CONFIRM_TASK_DELETE', 'Sind Sie sicher?');
define ('HL_CONFIRM_PROJECT_DELETE', 'Sind Sie sicher?');
define ('HL_UPDATE_PROGRESS', 'Klicken Sie um den Fortschritt zu ändern');
define ('HL_VIEW_PROJECT_DETAILS', 'Projekt Details zeigen');
define ('HL_VIEW_TASK_DETAILS', 'Aufgaben Details zeigen');

/**
* @desc    FILEMANAGER
**/
/**
* @version 0.6.0
**/
define ('HL_BROWSER_ROOT', 'Root');
define ('HL_FILE_BROWSER', 'Browser');
define ('HL_DEL_FILE', 'Datei(en) löschen');
define ('HL_NEW_FOLDER', 'Neuer Ordner');
define ('HL_CREATE_FOLDER', 'Ordner erstellen');
define ('HL_EDIT_FOLDER', 'Ordner ändern');
define ('HL_FOLDER_NAME', 'Ordner Name');
define ('HL_FOLDER_TYPE', 'Ordner-Typ und Zugriffsrechte');
define ('HL_FOLDER_TYPE_0', 'Öffentlich');
define ('HL_FOLDER_TYPE_1', 'Projekt-Ordner');
define ('HL_FOLDER_TYPE_2', 'Privater Ordner');
define ('HL_PUBLIC', 'Öffentlich');
define ('HL_INACTIVE', 'Nicht mehr aktuell');
define ('HL_PRIVATE', 'Privat');
define ('HL_DATA_TYPE', 'Typ');
define ('HL_FOLDER', 'Ordner');
define ('HL_CHANGE_DATE', 'Geändert am');
define ('HL_CREATE_DATE', 'Erstellt am');
define ('HL_DATA_SIZE', 'Größe');
define ('HL_SIZE_KB', 'Kb');
define ('HL_MOVE_DATA', 'Verschieben');
define ('HL_DELETE_DATA', 'Löschen');
define ('HL_NEW_DOCUMENT', 'Neues Dokument');
define ('HL_TITLE', 'Titel');
define ('HL_DOC_TYPE', 'Dokumenten-Typ und Zugriffsrechte');
define ('HL_DOC_TYPE_1', 'Projekt-Dokument');
define ('HL_DOC_TYPE_2', 'Privates Dokument');
define ('HL_CREATE_DOC', 'Erstelle Dokument');
define ('HL_EDIT_DOC', 'Dokument ändern');
define ('HL_HYDRA_DOC', 'Project Fork Dokument');
define ('HL_BROWSER_MODE', 'Browser Modus');
define ('HL_BROWSER_MODE_MOVE_DATA', 'Verschieben');
define ('HL_BROWSER_MODE_SEEK', 'Durchsuchen');
define ('HL_MOVE_HERE', 'Hier einfügen');
define ('HL_DETAILS', 'Details');
define ('HL_PRIVATE_FOLDER', 'Privater Ordner');
define ('HL_FOLDERS', 'Ordner');
define ('HL_HYDRA_DOCS', 'Project Fork Dokument(e)');
define ('HL_ABORT', 'Abbrechen');
define ('HL_COMMENTS', 'Kommentare');
define ('HL_ACTION_NOT_AVAILABLE', 'Aktion nicht verfügbar');
define ('HL_BROWSE_UP', 'In übergeordnetes Verzeichnis wechseln');
define ('HL_BROWSE_PATH', 'In Pfad wechseln');
define ('HL_OPEN_FOLDER', 'Öffne Ordner');
define ('HL_OPEN_DOC', 'Öffne Dokument');
define ('HL_NEW_COMMENT', 'Neuer Kommentar');
define ('HL_VIEW_COMMENTS', 'Zeige Kommentare');
define ('HL_CREATE_COMMENT', 'Erstelle Kommentar');
define ('HL_EDIT_COMMENT', 'Kommentar bearbeiten');
define ('HL_NEW_UPLOAD', 'Neue Datei');
define ('HL_FILE_NAME', 'Datei-Name');
define ('HL_FILE_TYPE', 'Daten-Typ und Zugriffsrechte');
define ('HL_FILE_NAME_DESC', 'Feld leer lassen um Originalnamen zu verwenden');
define ('HL_UPDATE_FILE', 'Datei hochladen');
define ('HL_UPDATE_FILE_DESC', 'Feld leer lassen um Datei beizubehalten');
define ('HL_FILE_SOURCE', 'Quelle');
define ('HL_FILE_SOURCE_DESC', 'Wählen Sie eine Datei von Ihrem PC');
define ('HL_FILE_UPLOAD', 'Hochladen');
define ('HL_FILE_UPLOAD_ERROR', 'Es ist ein unbekannter Fehler aufgetreten!');
define ('HL_FILE_NOT_EXISTS', "Die angegebene Datei existiert nicht!");
define ('HL_FILE_NOT_READABLE', "Die angegebene Datei konnte nicht gelesen werden!");
define ('HL_DOWNLOAD_FILE', "Datei herunterladen");
define ('HL_CONFIRM_DELETE', "Sind Sie sicher?");
define ('HL_DATA', "Dateien");
define ('HL_IS_ACTIVE', "Ist aktuell");


/**
* @desc    CALENDAR
**/
/**
* @version 0.6.0
**/

define ('HL_DAY_MONDAY', "Montag");
define ('HL_DAY_TUESDAY', "Dienstag");
define ('HL_DAY_WEDNESDAY', "Mittwoch");
define ('HL_DAY_THURSDAY', "Donnerstag");
define ('HL_DAY_FRIDAY', "Freitag");
define ('HL_DAY_SATURDAY', "Samstag");
define ('HL_DAY_SUNDAY', "Sonntag");

define ('HL_MONTH_JANUARY', 'Januar');
define ('HL_MONTH_FEBRUARY', 'Februar');
define ('HL_MONTH_MARCH', 'März');
define ('HL_MONTH_APRIL', 'April');
define ('HL_MONTH_MAY', 'Mai');
define ('HL_MONTH_JUNE', 'Juni');
define ('HL_MONTH_JULY', 'Juli');
define ('HL_MONTH_AUGUST', 'August');
define ('HL_MONTH_SEPTEMBER', 'September');
define ('HL_MONTH_OCTOBER', 'Oktober');
define ('HL_MONTH_NOVEMBER', 'November');
define ('HL_MONTH_DECEMBER', 'Dezember');

define ('HL_MONTH', 'Monat');
define ('HL_WEEK', 'Woche');
define ('HL_DAY', 'Tag');

define ('HL_SHOW_DATE', 'Zeige Datum');
define ('HL_NEW_EVENT', 'Neuer Termin');
define ('HL_EVENT', 'Termin');
define ('HL_EVENTS', 'Termine');
define ('HL_HOUR', 'Stunde');
define ('HL_MINUTE', 'Minute');
define ('HL_START_AND_END_DATE', 'Beginn/Ende');
define ('HL_START_DATE', 'Beginn');
define ('HL_END_DATE', 'Ende');
define ('HL_MISC_SETTINGS', 'Sonstige Einstellungen');
define ('HL_SHARE_ENTRY', 'Andere Benutzer können diesen Termin einsehen');
define ('HL_CREATE_EVENT', 'Termin erstellen');
define ('HL_CONFLICTS_FOUND', "Termin konnte nicht erstellt werden! Es wurden Konflikte gefunden");
define ('HL_ENDDATE_CONFLICT', "Termin konnte nicht erstell werden! Termin kann nicht enden bevor er beginnt!");
define ('HL_COLOR', 'Farbe');
define ('HL_COLOR_WHITE', 'Weiss');
define ('HL_COLOR_YELLOW', 'Gelb');
define ('HL_COLOR_ORANGE', 'Orange');
define ('HL_COLOR_RED', 'Rot');
define ('HL_COLOR_GREEN', 'Grün');
define ('HL_COLOR_BLUE', 'Blau');
define ('HL_COLOR_PURPLE', 'Lila');
define ('HL_COLOR_PINK', 'Rosa');

define ('HL_DELEVENT_CONFIRM', 'Sind Sie sicher?');
define ('HL_UPDATE_EVENT', 'Termin aktualisieren');
define ('HL_NO_USER_SELECTED', 'Kein Benutzer');
define ('HL_VIEW_SHARED', 'Zeige Termine anderer');


/**
* @desc    MIME TYPES
**/
/**
* @version 0.6.0
**/
define ('HL_MIME_PNG', 'PNG-Bild');
define ('HL_MIME_JPG', 'JPEG-Bild');
define ('HL_MIME_WORD', 'Word-Dokument');
define ('HL_MIME_PDF', 'PDF-Dokument');
define ('HL_MIME_HTML', 'HTML-Dokument');
define ('HL_MIME_CSS', 'Stylesheet');
define ('HL_MIME_GIF', 'GIF-Bild');
define ('HL_MIME_TXT', 'Text-Dokument');
define ('HL_MIME_EXE', 'Ausführbare Datei');
define ('HL_MIME_ZIP', 'ZIP-Archiv');
define ('HL_MIME_ODT', 'Open Dokument');
define ('HL_MIME_UNKNOWN', 'Unbekannt');


/**
* @desc    PERMISSION LABLES
**/
/**
* @version 0.6.0
**/
define ('HL_CMD_SHOW_USERGROUPS', 'Kann Benutzergruppen sehen');
define ('HL_CMD_NEW_USERGROUPS', 'Kann Benutzergruppen erstellen');
define ('HL_CMD_DEL_USERGROUPS', 'Kann Benutzergruppen löschen');
define ('HL_CMD_SHOW_USERS', 'Kann Project Fork-Benutzer sehen');
define ('HL_CMD_SHOW_JOOMLAUSERS', 'Kann Joomla-Benutzer sehen');
define ('HL_CMD_IMPORTUSERS', 'Kann Joomla-Benutzer importieren');
define ('HL_CMD_DEL_USERS', 'Kann Project Fork-Benutzer löschen');
define ('HL_CMD_SHOW_SETTINGS', 'Kann System-Einstellungen sehen');
define ('HL_CMD_EDIT_SETTINGS', 'Kann System-Einstellungen ändern');
define ('HL_CMD_SHOW_PROFILE', 'Kann eigenes Profil sehen/ändern');

define ('HL_CMD_NEW_PROJECT', 'Kann neue Projekte erstellen');
define ('HL_CMD_DEL_PROJECT', 'Kann Projekte löschen');
define ('HL_CMD_TASKS', 'Kann Aufgaben sehen');
define ('HL_CMD_NEW_TASK', 'Kann Aufgaben erstellen/ändern');
define ('HL_CMD_DEL_TASK', 'Kann Aufgaben löscehn');

define ('HL_CMD_DEL_DATA', 'Kann Ordner/Dokumente/Daten löschen');
define ('HL_CMD_CREATE_FILE', 'Kann neue Daten hochladen');
define ('HL_CMD_MOVE_DATA', 'Kann Daten verschieben');
define ('HL_CMD_NEW_FOLDER', 'Kann neue Ordner erstellen/ändern');
define ('HL_CMD_NEW_DOCUMENT', 'Kann neue Project Fork-Dokumente erstellen');
define ('HL_CMD_READ_DATA', 'Kann Dokumente betrachten/herunterladen');
define ('HL_CMD_VIEW_COMMENTS', 'Kann Kommentare sehen');
define ('HL_CMD_NEW_COMMENT', 'Kann eigene Kommentare erstellen/ändern');

define ('HL_CMD_VIEW_GROUP_CAL', 'Kann Termine anderer sehen (Falls freigegeben)');
define ('HL_CMD_NEW_CAL_ENTRY', 'Kann eigene Termine anlegen/ändern');
define ('HL_CMD_VIEW_LIST', 'Kann Termin-Liste sehen');
define ('HL_CMD_CHANGE_USERTYPE', 'Kann Benutzer-Typ anderer ändern (Benötigt Zugriff auf Project Fork-Benutzer)');
define ('HL_CMD_EDIT_REGISTRY', 'Kann Registry bearbeiten (Benötigt Zugriff auf System-Einstellugen)');
define ('HL_CMD_DEL_LANG', 'Kann Sprachdateien löschen (Benötigt Zugriff auf System-Einstellugen)');
define ('HL_CMD_DEL_THEME', 'Kann Themes löschen (Benötigt Zugriff auf System-Einstellugen)');
?>