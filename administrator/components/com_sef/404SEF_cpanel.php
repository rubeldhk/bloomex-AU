<?php

/**
 * SEF CPANEL for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: 404SEF_cpanel.php 308 2008-04-01 18:51:45Z silianacom-svn $
 */


/** ensure this file is being included by a parent file */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

function displaySecLine( $color, $title, $ItemName, $shSecStats ) {?>
		<tr>
			<td style="width:120px; background-color:<?php echo $color ?>"><?php echo $title; ?></td>
            <td style="width:120px; background-color:<?php echo $color ?>; text-align: center;" ><?php echo $shSecStats[$ItemName]; ?></td>
            <td style="background-color:<?php echo $color ?>; text-align: right;" >
            	<?php
                echo sprintf('%1.1f',$shSecStats[$ItemName.'Pct']). ' %  |  '.sprintf("%05.1f",$shSecStats[$ItemName.'Hrs']).' '._COM_SEF_SH_TOTAL_PER_HOUR.'&nbsp;';  ?>
           </td>
        </tr>
        <?php
}


function displayCPanelHTML( $sefCount, $Count404, $customCount, $shSecStats ) {
	global $sefConfig; ?>

	<table class="adminform">
		<tr>
			<td width="50%" valign="top">
				<table width="100%">
					<tr>
						<td colspan="3">
							<table class="adminform">
								<tr>
									<td width="8%"><?php echo _COM_SEF_SH_REDIR_TOTAL.':'; ?></td>
									<td align="left" width="12%" style="font-weight: bold">
										<?php echo $sefCount + $Count404 + $customCount; ?>
									</td>
									<td width="8%"><?php echo _COM_SEF_SH_REDIR_SEF.':'; ?></td>
									<td align="left" width="12%" style="font-weight: bold"><?php echo $sefCount; ?></td>
									<td width="8%"><?php echo _COM_SEF_SH_REDIR_404.':'; ?></td>
									<td align="left" width="12%" style="font-weight: bold"><?php echo $Count404; ?></td>
									<td width="8%"><?php echo _COM_SEF_SH_REDIR_CUSTOM.':'; ?></td>
									<td align="left" width="12%" style="font-weight: bold"><?php echo $customCount; ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" height="90" width="90">
							<a href="index2.php?option=com_sef&amp;task=showconfig" style="text-decoration:none;" title="<?php echo _COM_SEF_CONFIG_DESC;?>">
								<img src="components/com_sef/images/config.png" width="48" height="48" align="middle" border="0" />
								<br />
								<?php echo _COM_SEF_CONFIG; ?>
							</a>
						</td>
						<?php
						if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=import_export" style="text-decoration:none;" title="<?php echo _COM_SEF_IMPORT_EXPORT;?>">
									<img src="components/com_sef/images/help.png" width="48" height="48" align="middle" border="0"/>
									<br />
									<?php echo _COM_SEF_IMPORT_EXPORT; ?>
								</a>
							</td>
							<?php
						}
						if( $sefConfig->shAdminInterfaceType == SH404SEF_STANDARD_ADMIN ) { ?>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=view&amp;viewmode=0" style="text-decoration:none;" title="<?php echo _COM_SEF_VIEWURLDESC; ?>">
									<img src="components/com_sef/images/url.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_VIEWURL; ?>
								</a>
							</td>
							<?php
						} ?>
						<td align="center" height="90" width="90">
							<a href="index2.php?option=com_sef&amp;task=info" style="text-decoration:none;" title="<?php echo _COM_SEF_INFODESC;?>">
								<img src="components/com_sef/images/info.png" width="48" height="48" align="middle" border="0" />
								<br />
								<?php echo _COM_SEF_INFO; ?>
							</a>
						</td>
					</tr>
					<?php
					if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
						<tr>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=view&amp;viewmode=0" style="text-decoration:none;" title="<?php echo _COM_SEF_VIEWURLDESC; ?>">
									<img src="components/com_sef/images/url.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_VIEWURL; ?>
								</a>
							</td>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=view&amp;viewmode=1" style="text-decoration:none;" title="<?php echo _COM_SEF_VIEW404DESC; ?>">
									<img src="components/com_sef/images/logs.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_VIEW404; ?>
								</a>
							</td>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=view&amp;viewmode=2" style="text-decoration:none;" title="<?php echo _COM_SEF_VIEWCUSTOMDESC; ?>">
									<img src="components/com_sef/images/redirect.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_VIEWCUSTOM;?>
								</a>
							</td>
						</tr>
						<?php
					} ?>
					<tr>
						<td align="center" height="90" width="90">
							<a href="index2.php?option=com_sef&amp;task=purge&amp;viewmode=0&amp;confirmed=0" style="text-decoration:none;" title="<?php echo _COM_SEF_PURGEURLDESC; ?>">
								<img src="components/com_sef/images/cut-url.png" width="48" height="48" align="middle" border="0" />
								<br />
								<?php echo _COM_SEF_PURGEURL; ?>
							</a>
						</td>
						<td align="center" height="90" width="90">
							<a href="index2.php?option=com_sef&amp;task=purge&amp;viewmode=1&amp;confirmed=0" style="text-decoration:none;" title="<?php echo _COM_SEF_PURGE404DESC; ?>">
								<img src="components/com_sef/images/cut-logs.png" width="48" height="48" align="middle" border="0" />
								<br />
								<?php echo _COM_SEF_PURGE404 ;?>
							</a>
						</td>
						<?php
						if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=purge&amp;viewmode=2&amp;confirmed=0" style="text-decoration:none;" title="<?php echo _COM_SEF_PURGECUSTOMDESC; ?>">
									<img src="components/com_sef/images/cut-redirect.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_PURGECUSTOM; ?>
								</a>
							</td>
							<?php
						} ?>
					</tr>
					<?php
					if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) { ?>
						<tr>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=viewMeta" style="text-decoration:none;" title="<?php echo _COM_SEF_META_TAGS_DESC; ?>">
									<img src="components/com_sef/images/cut-url.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_META_TAGS; ?>
								</a>
							</td>
							<td align="center" height="90" width="90">
								<a href="index2.php?option=com_sef&amp;task=purgeMeta&amp;confirmed=0" style="text-decoration:none;" title="<?php echo _COM_SEF_PURGE_META_DESC; ?>">
									<img src="components/com_sef/images/cut-logs.png" width="48" height="48" align="middle" border="0" />
									<br />
									<?php echo _COM_SEF_PURGE_META; ?>
								</a>
							</td>
							<td align="center" height="90" width="90">&nbsp;</td>
						</tr>
						<?php
					} ?>
				</table>
			</td>
			<td width="50%" valign="top" align="center">
				<table border="1" width="100%" class="adminform">
					<tr>
						<th colspan = "3">
							<?php
							if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
								$shCommand		= 'setStandardAdmin';
								$shCommandDesc	= _COM_SEF_STANDARD_ADMIN;
							}else{
								$shCommand		= 'setAdvancedAdmin';
								$shCommandDesc	= _COM_SEF_ADVANCED_ADMIN;
							} ?>
							<a href="index2.php?option=com_sef&amp;task=<?php echo $shCommand;?>" style="text-decoration:none;" title="<?php echo $shCommandDesc; ?>"><?php echo $shCommandDesc; ?></a>
						</th>
					</tr>
					<tr>
						<?php
						$output = '';
						foreach( $sefConfig->fileAccessStatus as $file => $access ) {
							if( $access == _COM_SEF_UNWRITEABLE ) {
								$output .= '<tr><td>'.$file.'</td><td colspan="2">'._COM_SEF_UNWRITEABLE.'</td></tr>' . "\n";
							}
						}
						if( !empty( $output ) ) {
							echo '<th class="cpanel" colspan="3">' . _COM_SEF_NOACCESS . '</th>' . "\n";
							echo $output;
						}
						if( $sefConfig->debugToLogFile ) {
							echo '<tr><th class="cpanel" colspan="3" >DEBUG to log file : ACTIVATED <small>at '
							. date('Y-m-d H:i:s', $sefConfig->debugStartedAt).'</small></th></tr>';
						} ?>
						<th class="cpanel" colspan="3">
							<?php echo _COM_SEF_SH_SEC_STATS_TITLE.': ';
							if( $sefConfig->shSecEnableSecurity ) {
								echo $shSecStats['curMonth']
								. ' <a href="index2.php?option=com_sef&amp;task=updateSecStats"'
								. ' title="' . _COM_SEF_SH_SEC_STATS_UPDATE . '">'
								. ' [' . _COM_SEF_SH_SEC_STATS_UPDATE . ']</a>'
								. '<small> (' . $shSecStats['lastUpdated'] . ')</small>'
								;
							}else{
								echo _COM_SEF_SH_SEC_DEACTIVATED;
							} ?>
						</th>
					</tr>
					<tr>
						<td style="width:240px; background-color:#EFEFEF; font-weight:bold">
							<?php echo _COM_SEF_SH_TOTAL_ATTACKS; ?>
						</td>
						<td style="width:240px; background-color:#EFEFEF; font-weight:bold; text-align:center">
							<?php echo $shSecStats['totalAttacks']; ?>
						</td>
						<td style="background-color:#EFEFEF; text-align: right;">
							<?php echo sprintf('%5.1f',$shSecStats['totalAttacksHrs']).' '._COM_SEF_SH_TOTAL_PER_HOUR.'&nbsp;'?>
						</td>
					</tr>
					<?php
					if( $sefConfig->shAdminInterfaceType == SH404SEF_ADVANCED_ADMIN ) {
						displaySecLine('#F4F4F4', _COM_SEF_SH_TOTAL_CONFIG_VARS,		'totalConfigVars',		$shSecStats );
						displaySecLine('#EFEFEF', _COM_SEF_SH_TOTAL_BASE64,				'totalBase64',			$shSecStats );
						displaySecLine('#F4F4F4', _COM_SEF_SH_TOTAL_SCRIPTS,			'totalScripts',			$shSecStats );
						displaySecLine('#EFEFEF', _COM_SEF_SH_TOTAL_STANDARD_VARS,		'totalStandardVars', 	$shSecStats );
						displaySecLine('#F4F4F4', _COM_SEF_SH_TOTAL_IMG_TXT_CMD,		'totalImgTxtCmd',		$shSecStats );
						displaySecLine('#EFEFEF', _COM_SEF_SH_TOTAL_IP_DENIED,			'totalIPDenied',		$shSecStats );
						displaySecLine('#F4F4F4', _COM_SEF_SH_TOTAL_USER_AGENT_DENIED,	'totalUserAgentDenied', $shSecStats );
						displaySecLine('#EFEFEF', _COM_SEF_SH_TOTAL_FLOODING,			'totalFlooding',		$shSecStats );
						displaySecLine('#F4F4F4', _COM_SEF_SH_TOTAL_PHP,				'totalPHP',				$shSecStats );
						displaySecLine('#EFEFEF', _COM_SEF_SH_TOTAL_PHP_USER_CLICKED,	'totalPHPUserClicked',	$shSecStats );
					} ?>
					<tr>
						<th class="cpanel" colspan="3"><?php echo 'sh404SEF'; ?></th>
					</tr>
					<tr>
						<td style="width:120px; background-color:#EFEFEF"><?php echo _COM_SEF_INSTALLED_VERS ;?></td>
						<td style="background-color:#EFEFEF">
							<?php
							if( !empty( $sefConfig ) ) {
								echo $sefConfig->version;
							}else{
								echo 'Please review and save configuration first';
							}?>
						</td>
						<td rowspan="3">
							<img src="components/com_sef/images/sh404SEF-logo-big.png" align="middle" alt="sh404SEF" title="sh404SEF logo" border="0" width="291" height="186" />
						</td>
					</tr>
					<tr>
						<td style="background-color:#F4F4F4"><?php echo _COM_SEF_COPYRIGHT ;?></td>
						<td style="background-color:#F4F4F4">&copy; 2004-<?php echo date( 'Y' ); ?> Yannick Gaultier</td>
					</tr>
					<tr>
						<td style="background-color:#EFEFEF"><?php echo _COM_SEF_LICENSE ;?></td>
						<td style="background-color:#EFEFEF">
							<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank" title="GNU GPL">GNU GPL</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
} ?>

