<?php
defined('_VALID_MOS') or die('Restricted access');

$tstart = mosProfiler::getmicrotime();
?>
<?php echo "<?xml version=\"1.0\"?>\r\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $mosConfig_sitename; ?> - Administration [Joomla]</title>
    <style type="text/css">
        @import url(templates/joomla_admin/css/admin_login.css);
        .button:hover {
            background: white;
        }
    </style>
    <link rel="shortcut icon" href="<?php echo $mosConfig_live_site . '/images/favicon.ico'; ?>" />
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="joomla"><img src="templates/joomla_admin/images/header_text.png" alt="Joomla! Logo" /></div>
    </div>
</div>
<div id="ctr" align="center">
    <?php
    // handling of mosmsg text in url
    include_once( $mosConfig_absolute_path . '/administrator/modules/mod_mosmsg.php' );
    ?>

    <div id="sessionData" style="color: red; margin: 20px; font-size: 20px">
        <?php echo isset($errorMessage) ? htmlspecialchars($errorMessage) : ''; ?>
    </div>

    <div class="login">
        <div class="login-form">
            <svg width="200" height="50" xmlns="http://www.w3.org/2000/svg">
                <text x="10" y="30" font-family="Arial" font-size="24" fill="red">Email Verification</text>
            </svg>
            <form action="index.php" method="post" name="verificationForm" id="verificationForm">
                <div class="form-block">
                    <div class="inputlabel">Verification code</div>
                    <div><input required name="verification_code" type="text" class="inputbox" size="15" /></div>
                    <div align="left"><input type="submit" name="verify" class="button" value="Verify" /></div>
                </div>
            </form>
            <button class="button" id="resend" style="margin:8px">Resend code ?</button>
        </div>
        <div class="login-text">
            <div class="ctr"><img src="templates/joomla_admin/images/security.png" width="64" height="64" alt="security" /></div>
            <p>Welcome to Joomla!</p>
            <p>Please enter a verification code that was sent to your email.</p>
        </div>
        <div class="clr"></div>
    </div>
</div>
<div id="break"></div>
<noscript>
    !Warning! Javascript must be enabled for proper operation of the Administrator
</noscript>
<div class="footer" align="center">
    <div align="center">
        <?php echo $_VERSION->URL; ?>
    </div>
</div>
<script language="javascript" type="text/javascript">
    function setFocus() {
        document.verificationForm.code.select();
        document.verificationForm.code.focus();
    }
    document.getElementById('resend').addEventListener('click', function() {
        document.getElementById('sessionData').textContent = 'Resending the verification code...!';
        fetch('/administrator/sendVerificationCode.php')
            .then(response => response.json())
            .then(data => {
                if (data.message === 'ok') {
                    document.getElementById('sessionData').textContent = 'Verification code sent successfully to '+data.email+' email address';
                } else if (data.message === 'session_expired') {
                    window.location.href = '/administrator/index.php?mosmsg=Session Expired, Please login again';
                } else {
                    document.getElementById('sessionData').textContent = 'Error sending verification code. Please try again.';
                }
            })
            .catch(error => {
                document.getElementById('sessionData').textContent = 'Error sending verification code. Please try again.';
            });
    });
</script>
</body>
</html>
