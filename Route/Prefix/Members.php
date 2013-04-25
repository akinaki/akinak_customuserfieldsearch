<?php

class Akinak_CustomUserFieldSearch_Route_Prefix_Members extends XFCP_Akinak_CustomUserFieldSearch_Route_Prefix_Members
{
	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'user_id');
		return $router->getRouteMatch('XenForo_ControllerPublic_Member', $action, 'members');
	}

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 *
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		if (isset($extraParams['page']))
		{
			if (strval($extraParams['page']) !== XenForo_Application::$integerSentinel && $extraParams['page'] <= 1)
			{
				unset($extraParams['page']);
			}
		}
//оп
		if ($data && !empty($data['search_query']))
		{
			if (!empty($data['location'])) { $extraParams['location'] = $data['location']; }
			if (!empty($data['username'])) { $extraParams['username'] = $data['username']; }
			if (!empty($data['occupation'])) { $extraParams['ocuppation'] = $data['ocuppation']; }
			if (!empty($data['field_type'])) { $extraParams['field_type'] = $data['field_type']; }
			if (!empty($data['some_field'])) { $extraParams['some_field'] = $data['some_field'][1]; }

		}

		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'user_id', 'username');
	}
}