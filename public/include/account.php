<?php

function get_account_link($vendor, $account)
{
	global $_data_path;

	if ($vendor == "aws" || $vendor == "azure")
	{
		$file = "$_data_path/inventory/users-$vendor-$account.list";
		if (!file_exists($file))
			return $account;

		$enc = urlencode($account);
		return "<a href=\"$vendor-account.php?account=$enc\">$account</a>";
	}

	if ($vendor == "backblaze")
	{
		$file = "$_data_path/inventory/raw-b2-user-$account.json";
		if (!file_exists($file))
			return $account;

		$enc = urlencode($account);
		return "<a href=\"$vendor-account.php?account=$enc\">$account</a>";
	}

	if ($vendor == "cloudflare")
	{
		$file = "$_data_path/inventory/raw-cloudflare-user-$account.json";
		if (!file_exists($file))
			return $account;

		$enc = urlencode($account);
		return "<a href=\"$vendor-account.php?account=$enc\">$account</a>";
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
