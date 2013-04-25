<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Akinak
 * Date: 26.01.13
 * Time: 18:44
 * To change this template use File | Settings | File Templates.
 */

class Akinak_CustomUserFieldSearch_Model_Account extends XenForo_Model
{
	public function getLocs($q,$citys)
	{
		$len=strlen($q);
		$q=strtoupper($q);
		foreach ($citys as $city)
		{
			if (substr(strtoupper($city),0,$len) == $q)
			{
			$tags[]['tag']=$city;
			}
		}
		return $tags;
	}
}