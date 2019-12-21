<?php

function load_azure_group_memberships($account) {
	global $_data_path;
	$file = "$_data_path/inventory/membership-azure-$account.list";
	if (!file_exists($file))
		return array();
	$data = file_get_contents($file);
	if (empty($data))
		return array();

	$out = array();
	$lines = explode("\n", $data);
	foreach ($lines as $line) {
		$tmp = explode(" ", $line);
		$group = $tmp[0];
		$mail = $tmp[1];
		$out[$mail][] = $group;
	}

	return $out;
}
