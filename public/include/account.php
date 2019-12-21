<?php

function get_account_link($vendor, $account)
{
	global $_data_path;

	if ($vendor == "aws")
	{
		$file = "$_data_path/inventory/users-aws-$account.list";
		if (!file_exists($file))
			return $account;

		$enc = urlencode($account);
		return "<a href=\"aws-account.php?account=$enc\">$account</a>";
	}

	if ($vendor == "azure")
	{
		$file = "$_data_path/inventory/users-azure-$account.list";
		if (!file_exists($file))
			return $account;

		$enc = urlencode($account);
		return "<a href=\"azure-account.php?account=$enc\">$account</a>";
	}

	return $account;
}


function get_region_link($vendor, $account, $region)
{
	global $_data_path;

	if ($vendor == "azure")
	{
		$file = "$_data_path/inventory/usage-azure-$account.list";
		if (!file_exists($file))
			return $region;

		$enc = urlencode($account);
		return "<a href=\"azure-usage.php?account=$enc&region=$region\">$region</a>";
	}

	return $region;
}
