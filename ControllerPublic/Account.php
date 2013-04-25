<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akinak
 * Date: 25.01.13
 * Time: 16:18
 * To change this template use File | Settings | File Templates.
 */

class Akinak_CustomUserFieldSearch_ControllerPublic_Account extends XFCP_Akinak_CustomUserFieldSearch_ControllerPublic_Account
{
	/**
	 * @return mixed
	 */
	public function actionSearchCity()
	{
		$q = $this->_input->filterSingle('q', XenForo_Input::STRING);
		$type = $this->_input->filterSingle('type', XenForo_Input::STRING);

		if (XenForo_Application::get('options')->useAutocomplete==false)
		{
			return false;
		}

		$tags = array();
		if ($type==='location')
		{
		$citys =$this->getModelFromCache('Akinak_CustomUserFieldSearch_Model_User')->getLocationName($type);
		$citys = array_keys($citys);

		if ($q !== '')
		{
			$tags = $this->getModelFromCache('Akinak_CustomUserFieldSearch_Model_Account')->getLocs($q,$citys);
		}

		$viewParams = array(
			'tags' => $tags
		);
		return $this->responseView(
			'Akinak_CustomUserFieldSearch_ViewPublic_Account_searchCity',
			'',
			$viewParams
		);
		}

		$citys =$this->getModelFromCache('Akinak_CustomUserFieldSearch_Model_User')->getLocationName($type);
		if ($q !== '')
		{
			$tags = $this->getModelFromCache('Akinak_CustomUserFieldSearch_Model_Account')->getLocs($q,$citys);
		}

		$viewParams = array(
			'tags' => $tags
		);

		return $this->responseView(
			'Akinak_CustomUserFieldSearch_ViewPublic_Account_searchCity',
			'',
			$viewParams
		);


	}
}