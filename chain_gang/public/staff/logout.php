<?php
require_once('../../private/initialize.php');

// Log out the admin

$session->logout($admin);

redirect_to(url_for('/staff/login.php'));

?>
