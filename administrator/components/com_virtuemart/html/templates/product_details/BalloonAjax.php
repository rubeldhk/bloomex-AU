<?php
//BalloonAjax
session_start();
session_id($_POST['session_id']);
$_SESSION['balloon_value'] = $_POST['option'];
$_SESSION['input_balloon_value'] = '<input type="hidden" name="balloon_value" id="balloon_value" value="'.$_SESSION['balloon_value'].'">';
?>
