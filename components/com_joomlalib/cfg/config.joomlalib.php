<?php
$JL = new stdClass();
global $JL;
$JL = (object)[];
/* JLLog */
/** @var integer Default logging level for JL. 1=verbose, 5=debug, 10=production, 15=critical only */
$JL->logLevel = 10;
/** @var yesno Log messages to database */
$JL->dbLogging = 1;
/** @var enumInteger Time to keep the log entries into the database before pruning them. */
$JL->logttl = 0;

/* JLGraph */
/** @var integer How many seconds to cache charts for. Charts will not be regenerated before this time expires. */
$JL->graphExpireSecs = 30;
/** @var integer Default width of the chart to be generated. */
$JL->graphWidth = 500;
/** @var integer Default height of the chart to be generated. */
$JL->graphHeight = 500;

/* JLTime */
/** @var string Default Time Format for JoomlaLib. A date() compatible string. */
$JL->timeFormat = 'G:i';
/** @var string Default Date Format for JoomlaLib. A date() compatible string. */
$JL->dateFormat = 'n/j';
/** @var enumInteger How many hours away from server time is the Joomla user base. */
$JL->timeOffsetJoomla = 0;
/** @var enumInteger How many hours away from server time are the system administrators. */
$JL->timeOffsetReporting = 0;

/* JLFileTransfer */
/** @var yesno Use the Curl libraries to fetch files remotely. */
$JL->ftUseCurl = 1;
/** @var yesno Use the wget console command to fetch files remotely. */
$JL->ftUseWget = 1;
/** @var yesno Use raw sockets to fetch files remotely. */
$JL->ftUseRawSocket = 1;
/** @var yesno Use the fopen URL wrappers to fetch files remotely. */
$JL->ftUseFopen = 1;

?>