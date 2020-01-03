<?php

if (preg_match('/^([a-zA-Z0-9._-]+)$/', $_GET["account"], $tmp1) && preg_match('/^([a-z0-9.-]+)$/', $_GET["domain"], $tmp2)) {
	$account = $tmp1[1];
	$domain = $tmp2[1];
	$enc = urlencode($account);
} else
	die("Missing arguments...");

require "include/config.php";
require "include/page.php";
$file = "$_data_path/inventory/raw-cloudflare-zone-analytics-$account-$domain.json";

if (!file_exists($file))
	die("Invalid account...");

$date = date("Y-m-d H:i:s", filemtime($file));
$json = file_get_contents($file);

page_header("Polynimbus - Cloudflare domain");
echo "Cloudflare account <a href=\"cloudflare-account.php?account=$enc\"><strong>$account</strong></a>, domain <strong>$domain</strong> dump as of $date:<br /><br />\n";
echo "<pre>$json</pre>\n";
page_end();
