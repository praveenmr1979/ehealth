<?php
$pdir = getcwd();

chdir('../../../../../../');
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
$x = url('vwrooms/logout', array('absolute' => true));
$d = variable_get('vwrooms_saved', '');

header("location: $d/index.php?q=vwrooms/logout&message=" . urlencode($_GET['message']) . "&module=" . $module);
exit;
