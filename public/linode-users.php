<?php

require "include/config.php";
$path = "$_data_path/inventory";
$file = "$path/projects-linode.list";
$date = date("Y-m-d H:i:s", filemtime($file));

require "include/page.php";
require "include/raw.php";
page_header("Polynimbus - list of Linode users and their grants");
echo "List of Linode users and their grants as of $date<br />\n";
table_start("linode", array(
	"account",
	"username",
	"email",
	"tfa enabled",
	"restricted",
	"ssh keys",
));


$data = file_get_contents($file);
$lines = explode("\n", $data);

foreach ($lines as $line) {
	$account = trim($line);
	if (empty($account))
		continue;

	$file2 = "$path/users-linode-$account.list";
	$data2 = file_get_contents($file2);
	$lines2 = explode("\n", $data2);

	foreach ($lines2 as $line2) {
		$line2 = trim($line2);
		if (empty($line2))
			continue;

		$tmp = explode(" ", $line2);
		$username = $tmp[0];
		$email = $tmp[1];
		$tfa = $tmp[2];
		$restricted = $tmp[3];
		$keys = empty($tmp[4]) ? "-" : str_replace(";", "<br />", $tmp[4]);

		if ($restricted == 0)
			$style = "background-color: #fcf3cf;";
		else
			$style = false;

		$file3 = "$_data_path/inventory/raw-linode-grants-$account-$username.json";
		if (file_exists($file3)) {
			$enc1 = urlencode($account);
			$enc2 = urlencode($username);
			$userfield = "<a href=\"linode-grants.php?account=$enc1&username=$enc2\">$username</a>";
		} else
			$userfield = $username;

		table_row(array($account, $userfield, $email, $tfa, $restricted, $keys), $style);
	}
}

table_end("linode");
page_end();
