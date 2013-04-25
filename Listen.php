<?php

/**
* Listen for code events
*/
class Akinak_CustomUserFieldSearch_Listen
{

/**
* Load controller
*
* @param	string			$class
* @param	array			array
*
* @return	void
*/
public static function load_class($class, array &$extend)
{
	if ($class == 'XenForo_ControllerPublic_Member')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_ControllerPublic_Member';
	}

	if ($class == 'XenForo_ControllerPublic_Account')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_ControllerPublic_Account';
	}

	if ($class == 'XenForo_Model_User')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_Model_User';
	}

	if ($class == 'XenForo_Model_Account')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_Model_Account';
	}

	if ($class == 'XenForo_Route_Prefix_Members')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_Route_Prefix_Members';
	}

	if ($class == 'XenForo_Model_UserField')
	{
		$extend[] = 'Akinak_CustomUserFieldSearch_Model_UserField';
	}


}

}