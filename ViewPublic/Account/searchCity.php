<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Оленька
 * Date: 25.01.13
 * Time: 20:35
 * To change this template use File | Settings | File Templates.
 */

class Akinak_CustomUserFieldSearch_ViewPublic_Account_searchCity extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$results = array();
		foreach ($this->_params['tags'] AS $tag)
		{
			$results[$tag['tag']]['username'] = $tag['tag'];
		}

		return array(
			'results' => $results
		);
	}
}