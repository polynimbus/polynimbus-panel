<?php

require "include/config.php";

function get_image_name($vendor, $image) {
	global $_data_path;

	if ($vendor != "aws")
		return $image;

	$file = "$_data_path/cache/aws/describe-images/$image.json";
	if (!file_exists($file))
		return $image;

	$json = file_get_contents($file);
	$data = json_decode($json, true);

	if (is_null($data) || !isset($data["Images"][0]["Name"]))
		return $image;
	else
		return basename($data["Images"][0]["Name"]);
}

function get_instance_link($vendor, $account, $region, $id) {
	global $_data_path;

	if ($vendor != "aws")
		return $id;

	$region = substr($region, 0, -1);
	$file = "$_data_path/inventory/raw-aws-instances-$account-$region.json";
	if (!file_exists($file) || filesize($file) < 30)
		return $id;

	$enc1 = urlencode($account);
	$enc2 = urlencode($region);
	$enc3 = urlencode($id);
	return "<a href=\"aws-instance.php?account=$enc1&region=$enc2&id=$enc3\">$id</a>";
}

function get_aws_instance_local_ip($account, $region, $id) {
	global $_data_path;
	$file = "$_data_path/inventory/raw-aws-instances-$account-$region.json";
	if (!file_exists($file) || filesize($file) < 30)
		return false;

	$json = file_get_contents($file);
	$data = json_decode($json, true);

	foreach ($data["Reservations"] as $reservation)
		foreach ($reservation["Instances"] as $instance)
			if ($instance["InstanceId"] == $id)
				return $instance["PrivateIpAddress"];

	return false;
}

function get_hostname($vendor, $account, $region, $public_hostname, $id) {
	if ($public_hostname != "-" || $vendor != "aws")
		return $public_hostname;

	$region = substr($region, 0, -1);
	$ip = get_aws_instance_local_ip($account, $region, $id);
	if ($ip)
		return "local/$ip";
	else
		return $public_hostname;
}


$file = "$_data_path/inventory/instances.list";
$date = date("Y-m-d H:i:s", filemtime($file));

require "include/page.php";
require "include/acl.php";
require "include/account.php";
page_header("Polynimbus - cloud instances inventory");
echo "<strong>List of all cloud instances as of $date</strong><br />\n";
table_start("instances", array(
	"vendor",
	"account",
	"hostname",
	"state",
	"created",
	"label",
	"ssh-key",
	"location",
	"instance-type",
	"instance-id",
	"image-name",
	"net",
	"ssh-acl",
));

$data = file_get_contents($file);
$lines = explode("\n", $data);

foreach ($lines as $line) {
	$line = trim($line);
	if (empty($line))
		continue;

	$tmp = explode(" ", $line, 13);
	if (!isset($tmp[3]))
		continue;

	$vendor = $tmp[0];
	$account = $tmp[1];
	$state = $tmp[3];
	$region = $tmp[5];
	$id = $tmp[7];
	$style = ($state != "running" ? "background-color: #f4cccc;" : false);

	table_row(array(
		$vendor,
		get_account_link($vendor, $account),
		get_hostname($vendor, $account, $region, $tmp[2], $id),  // [2] - raw hostname
		$state,
		$tmp[9],  // created date
		str_replace(";", "<br />", $tmp[10]),  // tag(s)/label(s) - printed line by line
		$tmp[4],  // ssh key
		get_region_link($vendor, $account, $region),
		$tmp[6],  // instance type
		get_instance_link($vendor, $account, $region, $id),
		get_image_name($vendor, $tmp[8]),
		@$tmp[11],  // optional vpc-id/project
		map_acl_to_ranges($vendor, $account, $region, 22, @$tmp[12]),  // optional list of security groups (not divided by spaces)
	), $style);
}

table_end("instances");
page_end();
