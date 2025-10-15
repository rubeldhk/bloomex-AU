<?php
/**
* $Id: english.php 21 2007-04-16 10:47:37Z eaxs $
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
define ('HL_SYS_MESSAGE', 'System Message');
define ('HL_HOMEPAGE', 'Homepage');
define ('HL_PRINT', 'Print');


/**
* @desc    FILE MANAGER
* @version 0.6.5
**/
define ('HL_FILES_UPLOADPATH_NOT_WRITABLE', 'The upload path is not writable!');

/**
* @desc    PROJECTS AND TASKS
* @version 0.6.5
**/
define ('HL_PARENT_TASK', 'Parent task');
define ('HL_NO_PARENT', 'None');

define ('HL_PROJECT_DIRECTORIES', 'Directories');
define ('HL_PROJECT_DOCUMENTS', 'Documents');
define ('HL_PROJECT_FILES', 'Files');

define ('HL_TASK_PRIORITY', 'Priority');
define ('HL_TASK_PRIORITY_DESC', 'Set the priority of this task');
define ('HL_TASK_PRIORITY_LOW', 'Low priority');
define ('HL_TASK_PRIORITY_MED', 'Medium priority');
define ('HL_TASK_PRIORITY_HI', 'High priority');

define ('HL_TASK_ASSIGNED_TO', 'Assigned to');
define ('HL_CUSTOM_STATUS', 'Custom status');
define ('HL_CUSTOM_STATUS_DESC', 'Indicates the status/maturity of a task. Example: Bug, Canceled, Delayed');
define ('HL_NOTIFICATION', 'Notification');
define ('HL_NOTIFICATION_ENABLED', 'Notification is enabled');
define ('HL_NOTIFICATION_DISABLED', 'Notification is disabled');
define ('HL_NOTIFICATION_ENABLE', 'Enable notification');
define ('HL_NOTIFICATION_DISABLE', "Disable notification");
define ('HL_NOTIFICATION_TASK_UPDATE_EMAIL_SUBJECT', "{user} has updated task:");
define ('HL_NOTIFICATION_TASK_NEW_EMAIL_SUBJECT', "{user} has created a new task:");
define ('HL_NOTIFICATION_TASK_ASSIGNED_EMAIL_SUBJECT', "{user} has assigned a task to you:");
define ('HL_NOTIFY_USER', "Notify user?");

/**
* @desc    CONTROLPANEL
* @version 0.6.5
**/
define ('HL_DEBUG', 'Debug Hydra');
define ('HL_DEBUG_DESC', 'Activate this feature to show the system console');
define ('HL_HYDRA_TIMEFORMAT', 'Time format');
define ('HL_RAW_OUTPUT', 'Raw output');
define ('HL_RAW_OUTPUT_DESC', 'Force Joomla to hide everything except Project Fork (Frontend only)');


/**
* @desc    SYSTEM MESSAGES
* @version 0.6.5
**/
define ('HL_MSG_PROJECT_CREATED', 'Project has been created');
define ('HL_MSG_PROJECT_MODIFIED', 'Project has been modified');
define ('HL_MSG_PROJECTS_DELETED', 'The selected projects have been deleted!');
define ('HL_MSG_TASK_CREATED', 'Task has been created');
define ('HL_MSG_TASK_MODIFIED', 'Task has been modified');
define ('HL_MSG_TASK_DELETED', 'Selected tasks have been deleted!');
define ('HL_MSG_TASK_DELETE_FAILED', 'Could not delete selected tasks properly! Database error!');
define ('HL_MSG_PROGRESS_UPDATED', 'Progress has been updated');
define ('HL_MSG_PROGRESS_UPDATED_FAILED', 'Could not update progress properly! Database error!');
define ('HL_MSG_HACKER', 'Hacking attempt!');
define ('HL_MSG_HACKER_PROJECT', 'The selected project does not exist or is not available!');
define ('HL_MSG_HACKER_TASK', 'The selected task does not exist or is not available!');
define ('HL_MSG_NOTIFICATION_REMOVED', 'Notification has been disabled.');
define ('HL_MSG_NOTIFICATION_ADD', 'Notification has been enabled.');
define ('HL_MSG_FOLDER_CREATED', 'Folder has been created');
define ('HL_MSG_FOLDER_MODIFIED', 'Folder has been modified');
define ('HL_MSG_FOLDER_DELETED', 'Folder has been deleted');
define ('HL_MSG_DIR_NOT_AVAILABLE', 'This directory does not exist or is not available!');
define ('HL_MSG_COMMENT_CREATED', 'Comment has been added!');
define ('HL_MSG_COMMENT_MODIFIED', 'Comment has been modified!');
define ('HL_MSG_COMMENT_DELETED', 'Comment has been deleted!');
define ('HL_MSG_PROFILE_UPDATED', 'Your profile has been updated!');

/**
* @desc    PERMISSION LABELS
* @version 0.6.5
**/
define ('HL_CMD_TASK_NOTIFICATION', 'Can enable/disable task notification');
define ('HL_CMD_TASK_VIEWCOMMENTS', 'Can view comments');
define ('HL_CMD_TASK_ADDCOMMENTS', 'Can add comments');
define ('HL_CMD_TASK_DELCOMMENTS', 'Can delete own comments');

/***********************HYDRA060+061***********************/


/**
* @desc    GENERAL LANGUAGE
**/
/**
* @version 0.6.0
**/
define ('HL_NAV_BOX', 'Navigation');
define ('HL_SETTINGS_BOX', 'Settings');
define ('HL_CONTROLPANEL', 'Controlpanel');
define ('HL_PROJECTS', 'Projects');
define ('HL_PROJECT', 'Project');
define ('HL_FILES', 'Filemanager');
define ('HL_TASKS', 'Tasks');
define ('HL_CALENDAR', 'Calendar');

define ('HL_AVAILABLE_ACTIONS', 'Available actions');
define ('HL_SORT_ASC', 'Sort ascending');
define ('HL_SORT_DESC', 'Sort descending');
define ('HL_GO_BACK', 'Back');
define ('HL_CREATED_BY', 'Created by');
define ('HL_NAME', 'Name');
define ('HL_USERNAME', 'Username');
define ('HL_EDIT', 'Modify');
define ('HL_LANG', 'Language');
define ('HL_DISPLAY', 'Show');
define ('HL_THEME', 'Theme');
define ('HL_IMPORT', 'Import');
define ('HL_FORM_ALERT', 'Please complete your information!');
define ('HL_GENERAL_INFO', 'General information');
define ('HL_GENERAL_SETTINGS', 'General settings');
define ('HL_USER_TYPE', 'Usertype');
define ('HL_USER_TYPE_CLIENT', 'Client');
define ('HL_USER_TYPE_MEMBER', 'Member');
define ('HL_USER_TYPE_GROUPLEADER', 'Group-Leader');
define ('HL_USER_TYPE_ADMINISTRATOR', 'Administrator');
define ('HL_FILTER', 'Filter');
define ('HL_APPLY_FILTER', 'Apply filter');
define ('HL_WELCOME', 'Welcome');
define ('HL_OPEN_MENU', 'Click to open menu');
define ('HL_USER', 'User');
define ('HL_ADD', 'Add');
define ('HL_DELETE', 'Delete');
define ('HL_SHOW', 'Show');
define ('HL_CONFIRM', 'Are you sure ?');
/**
* @version 0.6.1
**/
define ('HL_ACCESS_DENIED', 'Access denied');
define ('HL_IS_WRITEABLE', 'Is writeable');
define ('HL_NOT_WRITEABLE', 'Is not writeable');


/**
* @desc    CONTROLPANEL
**/
/**
* @version 0.6.0
**/
define ('HL_MY_PROFILE', 'My profile');
define ('HL_SYSTEM_SETTINGS', 'System');
define ('HL_USER_GROUPS', 'Usergroups');
define ('HL_GROUPS', 'Groups');
define ('HL_USERS', 'Users');
define ('HL_JOOMLAUSERS', 'Joomla-Users');
define ('HL_EMAIL', 'Email');
define ('HL_USERS_MANAGEMENT', 'User management');
define ('HL_NO_USERGROUPS', 'There is no usergroup.');
define ('HL_NEW_USERGROUP', 'New usergroup');
define ('HL_DELETE_USERGROUPS', 'Delete usergroup(s)');
define ('HL_CREATE_USERGROUP', 'Create usergroup');
define ('HL_EDIT_USERGROUP', 'Modify usergroup');
define ('HL_GROUP_NAME', 'Group Name');
define ('HL_GROUP_NAME_DESC', 'Please enter a name like: "Usergroup1"');
define ('HL_GROUP_DESC', 'Description');
define ('HL_GROUP_MEMBERS_AND_PERMS', 'Members and permissions');
define ('HL_GROUP_MEMBERS', 'Members');
define ('HL_GROUP_DELETE_WARN', 'Please choose a group from the list!');
define ('HL_PERMS_FOR', 'Permissions for');
define ('HL_IMPORT_JOOMLA_USERS', 'Import Joomla-User(s)');
define ('HL_DELETE_USERS', 'Delete User');
define ('HL_DELETE_USERS_WARN', 'Please choose a user from the list!');
define ('HL_IMPORT_JOOMLA_USERS_WARN', 'Please choose a user from the list!');
define ('HL_IMPORT_SELECTED', 'Import selected');
define ('HL_SETTINGS_FOR', 'Settings for');
define ('HL_NO_JOOMLAUSERS', 'All Joomla-Users have been imported!');
define ('HL_NO_HYDRAUSERS', 'There are no imported Joomla-Users available');
define ('HL_HYDRA_VERSION', 'Project Fork version');
define ('HL_UPLOAD_SETTINGS', 'Upload settings');
define ('HL_UPLOAD_STORAGE_PATH', 'Upload storagepath');
define ('HL_SAVE_SETTINGS', 'Save settings');
define ('HL_USER_GROUP', 'Usergroup');
define ('HL_HYDRA_TIMEOFFSET', 'Time offset');
define ('HL_HYDRA_LATEST_TASKS', 'Latest tasks');
define ('HL_HYDRA_LATEST_FILES', 'Latest files');
define ('HL_HYDRA_LATEST_COMMENTS', 'Latest comments');
define ('HL_GRANT', 'Grant');
define ('HL_PERMISSION', 'Permission');
define ('HL_REQUIRED_TYPE', 'Required usertype');
define ('HL_OR_HIGHER', 'or higher');
define ('HL_UPCOMING_EVENTS', 'Upcoming events');
define ('HL_NO_UPCOMING_EVENTS', 'There are no upcoming events');
define ('HL_EDIT_REGISTRY', 'Edit registry');
define ('HL_REG_AREA', 'Area');
define ('HL_REG_CMD', 'Command');
define ('HL_REG_USER_TYPE', 'Req. usertype');
define ('HL_REG_AREA_LABEL', 'Area label');
define ('HL_REG_CMD_LABEL', 'Command label');
define ('HL_REG_INHERIT', 'Inherited from');
define ('HL_REG_AREA_HLP', 'The Area defines a section in Hydra.');
define ('HL_REG_CMD_HLP', 'A Command is an action that can be perfomed by a user.');
define ('HL_REG_USER_TYPE_HLP', 'Defines the usertype that a user must at least have to perform the action.');
define ('HL_REG_AREA_LABEL_HLP', 'Area label defines the name of the Area. The label must be defined in the language-file!');
define ('HL_REG_CMD_LABEL_HLP', 'Command label defines the name of the command. The label must be defined in the language-file!');
define ('HL_REG_INHERIT_HLP', 'Some commands get inherited by a parent command.');
define ('HL_REG_WARNING', 'WARNING! Read this before editing!');
define ('HL_REG_WARNING_TXT', "Don't change anything here unless you know what you are doing!<br/>Please read the legend below carefully!");
define ('HL_REG_ADD_ENTRY', 'Add new entry to the registry');
define ('HL_REG_DEL_ENTRIES', 'Delete selected entries');
define ('HL_REG_DEL_ENTRIES_WARN', 'Please select an entry from the list!');
define ('HL_REG_DEL_ENTRIES_CONFIRM', 'Are you sure?');
define ('HL_REGISTRY', 'Registry');
define ('HL_UPDATE_REGISTRY', 'Update registry');
define ('HL_CHECK_HOMEPAGE_UPDATES', 'Visit homepage / Check for updates');
define ('HL_SHOW_QUICKPANEL', 'Show Quickpanel');
define ('HL_SHOW_LATEST_TASKS', 'Show latest tasks');
define ('HL_SHOW_UPCOMING_EVENTS', 'Show upcoming events');
define ('HL_HIGHLIGHT_LATE_TASKS', 'Highlight late tasks');
define ('HL_INSTALLED_LANGUAGES', 'Installed languages');
define ('HL_INSTALLED_THEMES', 'Installed themes');
define ('HL_DELETE_LANG', 'Delete language');
define ('HL_DELETE_THEME', 'Delete theme');
define ('HL_DELETE_THEME_INFO', 'The default theme (Joomla) cannot be uninstalled.');
define ('HL_DELETE_LANG_INFO', 'The default language (English) cannot be uninstalled.');

/**
* @desc    PROJECTS AND TASKS
**/
/**
* @version 0.6.0
**/
define ('HL_NEW_PROJECT', 'New project');
define ('HL_DEL_PROJECT', 'Delete project(s)');
define ('HL_CREATE_PROJECT', 'Create project');
define ('HL_PROJECT_NAME', 'Project name');
define ('HL_PROJECT_NAME_DESC', 'Please enter a name');
define ('HL_PROJECT_DESC', 'Description');
define ('HL_PROJECT_TIME_LIMIT', 'Project start and end');
define ('HL_PROJECT_START_TIME', 'Project-Start');
define ('HL_PROJECT_START_TIME_DESC', 'When does the project start');
define ('HL_PROJECT_END_TIME', 'Project-Finish');
define ('HL_PROJECT_END_TIME_DESC', 'When should the project be finished');
define ('HL_PROJECT_GROUPS', 'Project groups');
define ('HL_PROJECT_ACCESS', 'Project access');
define ('HL_NO_PROJECTS', 'There are no projects available');
define ('HL_STATUS', 'Status');
define ('HL_STATUS_COMPLETE', 'Completed');
define ('HL_BEHIND_SCHEDULE', 'Days behind schedule');
define ('HL_EDIT_PROJECT', 'Save changes');
define ('HL_PROGRESS', 'Progress');
define ('HL_TOTAL_TASKS', 'Total tasks');
define ('HL_ACCESS', 'Access');
define ('HL_TIME_LEFT', 'Remaining time');
define ('HL_TIME_TOTAL', 'Total time');
define ('HL_DAYS', 'Days');
define ('HL_SHOW_TASKS', 'Show tasks');
define ('HL_NEW_TASK', 'New task');
define ('HL_TASK_NAME_DESC', 'Please enter a name');
define ('HL_DEL_PROJECT_WARN', 'Please choose a project from the list!');
define ('HL_TASK_TASK_DESC', 'Task');
define ('HL_TIME_LIMIT_AND_PROGRESS', 'Deadline and progress');
define ('HL_TASK_START', 'Start date');
define ('HL_TASK_START_DESC', 'When does the work start');
define ('HL_TASK_END', 'End date');
define ('HL_TASK_END_DESC', 'When should this task be completed');
define ('HL_TASK_PROGRESS_DESC', 'The current progress of this task');
define ('HL_TASK_LINKS', 'Links');
define ('HL_CREATE_TASK', 'Create task');
define ('HL_DELETE_TASK', 'Delete task(s)');
define ('HL_NO_TASKS', 'There are no tasks available.');
define ('HL_EDIT_TASK', 'Modify task');
define ('HL_TASK_INFO', 'Info - Click to view task');
define ('HL_ASSIGNED_TO', 'Assigned to');
define ('HL_FILTER_COMPLETED_TASKS', 'Hide completed tasks');
define ('HL_FILTER_UNCOMPLETED_TASKS', 'Hide uncompleted tasks');
define ('HL_FILTER_SHOW_ALL_TASKS', 'Show all tasks');
define ('HL_CONFIRM_TASK_DELETE', 'Are you sure?');
define ('HL_CONFIRM_PROJECT_DELETE', 'Are you sure?');
define ('HL_UPDATE_PROGRESS', 'Click to update the progress');
define ('HL_VIEW_PROJECT_DETAILS', 'View project details');
define ('HL_VIEW_TASK_DETAILS', 'View task details');

/**
* @desc    FILEMANAGER
**/
/**
* @version 0.6.0
**/
define ('HL_BROWSER_ROOT', 'Root');
define ('HL_FILE_BROWSER', 'Browser');
define ('HL_DEL_FILE', 'Delete file(s)');
define ('HL_NEW_FOLDER', 'New Folder');
define ('HL_CREATE_FOLDER', 'Create Folder');
define ('HL_EDIT_FOLDER', 'Modify Folder');
define ('HL_FOLDER_NAME', 'Folder name');
define ('HL_FOLDER_TYPE', 'Folder type and Access');
define ('HL_FOLDER_TYPE_0', 'Public');
define ('HL_FOLDER_TYPE_1', 'Project folder');
define ('HL_FOLDER_TYPE_2', 'Private folder');
define ('HL_PUBLIC', 'Public');
define ('HL_INACTIVE', 'Inactive');
define ('HL_PRIVATE', 'Private');
define ('HL_DATA_TYPE', 'Type');
define ('HL_FOLDER', 'Folder');
define ('HL_CHANGE_DATE', 'Last changed');
define ('HL_CREATE_DATE', 'Created');
define ('HL_DATA_SIZE', 'Size');
define ('HL_SIZE_KB', 'Kb');
define ('HL_MOVE_DATA', 'Move');
define ('HL_DELETE_DATA', 'Delete');
define ('HL_NEW_DOCUMENT', 'New document');
define ('HL_TITLE', 'Title');
define ('HL_DOC_TYPE', 'Document type and access');
define ('HL_DOC_TYPE_1', 'Project document');
define ('HL_DOC_TYPE_2', 'Private document');
define ('HL_CREATE_DOC', 'Create document');
define ('HL_EDIT_DOC', 'Change document');
define ('HL_HYDRA_DOC', 'Project Fork Document');
define ('HL_BROWSER_MODE', 'Browser mode');
define ('HL_BROWSER_MODE_MOVE_DATA', 'Move');
define ('HL_BROWSER_MODE_SEEK', 'Browse');
define ('HL_MOVE_HERE', 'Paste here');
define ('HL_DETAILS', 'Details');
define ('HL_PRIVATE_FOLDER', 'Private folder');
define ('HL_FOLDERS', 'Folders');
define ('HL_HYDRA_DOCS', 'Project Fork document(s)');
define ('HL_ABORT', 'Abort');
define ('HL_COMMENTS', 'Comments');
define ('HL_ACTION_NOT_AVAILABLE', 'Action not available');
define ('HL_BROWSE_UP', 'Browse to parent directory');
define ('HL_BROWSE_PATH', 'Browse to path');
define ('HL_OPEN_FOLDER', 'Open folder');
define ('HL_OPEN_DOC', 'Open Document');
define ('HL_NEW_COMMENT', 'New comment');
define ('HL_VIEW_COMMENTS', 'View comments');
define ('HL_CREATE_COMMENT', 'Create comment');
define ('HL_EDIT_COMMENT', 'Edit comment');
define ('HL_NEW_UPLOAD', 'New file');
define ('HL_FILE_NAME', 'File name');
define ('HL_FILE_TYPE', 'File type and access');
define ('HL_FILE_NAME_DESC', 'Leave blank to use the real name');
define ('HL_UPDATE_FILE', 'Update File');
define ('HL_UPDATE_FILE_DESC', 'Leave blank to keep your current file');
define ('HL_FILE_SOURCE', 'Source');
define ('HL_FILE_SOURCE_DESC', 'Select a file from your computer');
define ('HL_FILE_UPLOAD', 'Upload');
define ('HL_FILE_UPLOAD_ERROR', 'An error has occured!');
define ('HL_FILE_NOT_EXISTS', "This file doesn't exist!");
define ('HL_FILE_NOT_READABLE', "The file is not readable!");
define ('HL_DOWNLOAD_FILE', "Download file");
define ('HL_CONFIRM_DELETE', "Are you sure that you want to delete this file?");
define ('HL_DATA', "Files");
define ('HL_IS_ACTIVE', "Is active");


/**
* @desc    CALENDAR
**/
/**
* @version 0.6.0
**/

define ('HL_DAY_MONDAY', "Monday");
define ('HL_DAY_TUESDAY', "Tuesday");
define ('HL_DAY_WEDNESDAY', "Wednesday");
define ('HL_DAY_THURSDAY', "Thursday");
define ('HL_DAY_FRIDAY', "Friday");
define ('HL_DAY_SATURDAY', "Saturday");
define ('HL_DAY_SUNDAY', "Sunday");

define ('HL_MONTH_JANUARY', 'January');
define ('HL_MONTH_FEBRUARY', 'February');
define ('HL_MONTH_MARCH', 'March');
define ('HL_MONTH_APRIL', 'April');
define ('HL_MONTH_MAY', 'May');
define ('HL_MONTH_JUNE', 'June');
define ('HL_MONTH_JULY', 'July');
define ('HL_MONTH_AUGUST', 'August');
define ('HL_MONTH_SEPTEMBER', 'September');
define ('HL_MONTH_OCTOBER', 'October');
define ('HL_MONTH_NOVEMBER', 'November');
define ('HL_MONTH_DECEMBER', 'December');

define ('HL_MONTH', 'Month');
define ('HL_WEEK', 'Week');
define ('HL_DAY', 'Day');

define ('HL_SHOW_DATE', 'Show date');
define ('HL_NEW_EVENT', 'New event');
define ('HL_EVENT', 'Event');
define ('HL_EVENTS', 'Events');
define ('HL_HOUR', 'Hour');
define ('HL_MINUTE', 'Minute');
define ('HL_START_AND_END_DATE', 'Start/End date');
define ('HL_START_DATE', 'Start date');
define ('HL_END_DATE', 'End date');
define ('HL_MISC_SETTINGS', 'Misc. settings');
define ('HL_SHARE_ENTRY', 'Other users can see this event');
define ('HL_CREATE_EVENT', 'Create event');
define ('HL_CONFLICTS_FOUND', "Couldn't create event! Conflicts found");
define ('HL_ENDDATE_CONFLICT', "Couldn't create event! An event can not end before it starts!");
define ('HL_COLOR', 'Color');
define ('HL_COLOR_WHITE', 'White');
define ('HL_COLOR_YELLOW', 'Yellow');
define ('HL_COLOR_ORANGE', 'Orange');
define ('HL_COLOR_RED', 'Red');
define ('HL_COLOR_GREEN', 'Green');
define ('HL_COLOR_BLUE', 'Blue');
define ('HL_COLOR_PURPLE', 'Purple');
define ('HL_COLOR_PINK', 'Pink');

define ('HL_DELEVENT_CONFIRM', 'Are you sure that you want to delete this event?');
define ('HL_UPDATE_EVENT', 'Update event');
define ('HL_NO_USER_SELECTED', 'No user selected');
define ('HL_VIEW_SHARED', 'View shared events');


/**
* @desc    MIME TYPES
**/
/**
* @version 0.6.0
**/
define ('HL_MIME_PNG', 'PNG-Image');
define ('HL_MIME_JPG', 'JPEG-Image');
define ('HL_MIME_WORD', 'Word-Document');
define ('HL_MIME_PDF', 'PDF-Document');
define ('HL_MIME_HTML', 'HTML-Document');
define ('HL_MIME_CSS', 'Stylesheet');
define ('HL_MIME_GIF', 'GIF-Image');
define ('HL_MIME_TXT', 'Text-Document');
define ('HL_MIME_EXE', 'Executable File');
define ('HL_MIME_ZIP', 'ZIP-Archive');
define ('HL_MIME_ODT', 'Open Document');
define ('HL_MIME_UNKNOWN', 'Unknown');


/**
* @desc    PERMISSION LABLES
**/
/**
* @version 0.6.0
**/
define ('HL_CMD_SHOW_USERGROUPS', 'Can see usergroups');
define ('HL_CMD_NEW_USERGROUPS', 'Can create new usergroups');
define ('HL_CMD_DEL_USERGROUPS', 'Can delete usergroups');
define ('HL_CMD_SHOW_USERS', 'Can see Project Fork Users');
define ('HL_CMD_SHOW_JOOMLAUSERS', 'Can see Joomla-Users');
define ('HL_CMD_IMPORTUSERS', 'Can Import Joomla-Users');
define ('HL_CMD_DEL_USERS', 'Can delete Project Fork Users');
define ('HL_CMD_SHOW_SETTINGS', 'Can see system configuration');
define ('HL_CMD_EDIT_SETTINGS', 'Can edit system configuration');
define ('HL_CMD_SHOW_PROFILE', 'Can see/update personal profile');

define ('HL_CMD_NEW_PROJECT', 'Can create new projects');
define ('HL_CMD_DEL_PROJECT', 'Can delete projects');
define ('HL_CMD_TASKS', 'Can see tasks');
define ('HL_CMD_NEW_TASK', 'Can create/edit tasks');
define ('HL_CMD_DEL_TASK', 'Can delete tasks');

define ('HL_CMD_DEL_DATA', 'Can delete files/folders');
define ('HL_CMD_CREATE_FILE', 'Can create/upload new files');
define ('HL_CMD_MOVE_DATA', 'Can move files');
define ('HL_CMD_NEW_FOLDER', 'Can create new folders');
define ('HL_CMD_NEW_DOCUMENT', 'Can create new Project Fork documents');
define ('HL_CMD_READ_DATA', 'Can view/download files');
define ('HL_CMD_VIEW_COMMENTS', 'Can see comments');
define ('HL_CMD_NEW_COMMENT', 'Can write/edit own comments');

define ('HL_CMD_VIEW_GROUP_CAL', 'Can view shared events');
define ('HL_CMD_NEW_CAL_ENTRY', 'Can add/edit own events');
define ('HL_CMD_CHANGE_USERTYPE', 'Can change usertype (requires access to Hydra-Users)');
define ('HL_CMD_EDIT_REGISTRY', 'Can edit the Project Fork Registry (requires access to system settings)');
define ('HL_CMD_DEL_LANG', 'Can delete language files (requires access to system settings)');
define ('HL_CMD_DEL_THEME', 'Can delete themes (requires access to system settings)');
?>