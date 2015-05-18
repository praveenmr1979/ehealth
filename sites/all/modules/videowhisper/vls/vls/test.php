<form action="http://127.0.0.1/hotel/vwcredits/plimusipn" method="post">
TxnID<input type="text" name="txn_id"></input><br/>
Type<input name="transactionType"></input><br/>
Amount:<input name="invoiceAmount"></input><br/>
Parent<input name="parent_txn_id"></input><br/>
Contract<input name="contractId"></input><br/>
Invoice<input name="invoice"></input><br/>
UserID<input name="userid"></input><br/>
<input type="submit" value="submit"></input>
</form>


<?
///conf_init();
// Create base URL
$base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

$base_url = $base_root .= '://'. $_SERVER['HTTP_HOST'];

// $_SERVER['SCRIPT_NAME'] can, in contrast to $_SERVER['PHP_SELF'], not
// be modified by a visitor.
//$dir=trim(dirname($_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_NAME']=str_replace('/sites/all/modules/videos/vls/vls/','/',$_SERVER['SCRIPT_NAME']);
if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
	$base_path = "/$dir";
	$base_url .= $base_path;
	$base_path .= '/';
}
else {
	$base_path = '/';
}

echo "base path $base_path ";
list( , $session_name) = explode('://', $base_url, 2);

$cookie_domain = check_plain($_SERVER['HTTP_HOST']);
// Strip leading periods, www., and port numbers from cookie domain.
$cookie_domain = ltrim($cookie_domain, '.');
if (strpos($cookie_domain, 'www.') === 0) {
	$cookie_domain = substr($cookie_domain, 4);
}
$cookie_domain = explode(':', $cookie_domain);
$cookie_domain = '.'. $cookie_domain[0];

echo "cookie $cookie_domain sname $session_name baseurl $base_url";
function check_plain($text) {
	static $php525;

	if (!isset($php525)) {
		$php525 = version_compare(PHP_VERSION, '5.2.5', '>=');
	}
	// We duplicate the preg_match() to validate strings as UTF-8 from
	// drupal_validate_utf8() here. This avoids the overhead of an additional
	// function call, since check_plain() may be called hundreds of times during
	// a request. For PHP 5.2.5+, this check for valid UTF-8 should be handled
	// internally by PHP in htmlspecialchars().
	// @see http://www.php.net/releases/5_2_5.php
	// @todo remove this when support for either IE6 or PHP < 5.2.5 is dropped.

	if ($php525) {
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
	return (preg_match('/^./us', $text) == 1) ? htmlspecialchars($text, ENT_QUOTES, 'UTF-8') : '';
}

exit;
$var="aa%ds\bd";
echo mysql_escape_string($var)."\n";
$forbidden=array("'", "\"", "Â´", "`", "\\", "%");
foreach ($forbidden as $search) $var=str_replace($search,"",$var);
$var=mysql_escape_string($var);

echo "var $var\n";