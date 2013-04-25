<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akinak
 * Date: 27.01.13
 * Time: 1:06
 * To change this template use File | Settings | File Templates.
 */

class Akinak_CustomUserFieldSearch_ControllerPublic_Member extends XFCP_Akinak_CustomUserFieldSearch_ControllerPublic_Member
{
	/**
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionSearch()
	{
		$userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);
		if ($userId)
		{
			return $this->responseReroute(__CLASS__, 'member');
		}
		else if ($this->_input->inRequest('user_id'))
		{
			return $this->responseError(new XenForo_Phrase('posted_by_guest_no_profile'));
		}

		$userModel = $this->_getUserModel();

		$username = $this->_input->filterSingle('username', XenForo_Input::STRING);
		if ($username !== '')
		{
			$user = $userModel->getUserByName($username);
			if ($user)
			{
				return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildPublicLink('members', $user)
				);
			}
			else
			{
				$userNotFound = true;
			}
		}
		else
		{
			$userNotFound = false;
		}

		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$usersPerPage = XenForo_Application::get('options')->membersPerPage;

		$criteria = array(
			'user_state' => 'valid',
			'is_banned' => 0
		);

		// users for the member list
		$users = $userModel->getUsers($criteria, array(
		                                              'join' => XenForo_Model_User::FETCH_USER_FULL,
		                                              'perPage' => $usersPerPage,
		                                              'page' => $page
		                                         ));

		// most recent registrations
		$latestUsers = $userModel->getLatestUsers($criteria, array('limit' => 8));

		// most active users (highest post count)
		$activeUsers = $userModel->getMostActiveUsers($criteria, array('limit' => 12));

		$customFieldNames = $this->_getUserModel()->getCustomFieldsName();
		foreach ($customFieldNames as $key=>$customFieldName)
		{
			$customFieldNames[$key]['field_phrase']=new XenForo_Phrase('user_field_' . $customFieldName['field_id']);
		}

		$viewParams = array(
			'users' => $users,

			'totalUsers' => $userModel->countUsers($criteria),
			'page' => $page,
			'usersPerPage' => $usersPerPage,

			'latestUsers' => $latestUsers,
			'activeUsers' => $activeUsers,

			'userNotFound' => $userNotFound,
			'customFields' => $customFieldNames
		);

		return $this->responseView(
			'XenForo_ViewPublic_Search_Form',
			'akinak_cufsearch',
			$viewParams
		);
	}

	/**
	 * @return mixed
	 */
	public function actionList()
	{
		$userModel = $this->_getUserModel();
		$customFields = $userModel->getCustomFieldsName();
		foreach ($customFields as $customField)
		{
			$filterArray[$customField['field_id']] = XenForo_Input::STRING;
		}

		$filterArray['username'] =  XenForo_Input::STRING;
		$filterArray['location'] =  XenForo_Input::STRING;
		$filterArray['occupation'] =  XenForo_Input::STRING;
		$filterArray['any_custom_field'] =  XenForo_Input::STRING;
		$filterArray['some_field'] =  XenForo_Input::STRING;
		$filterArray['field_type'] =  XenForo_Input::STRING;

		$criteria = $this->_input->filter($filterArray);



		$criteria['some_field'] = array($criteria['field_type'],$criteria['some_field']);

		foreach ($customFields as $customField)
		{
			if ($criteria[$customField['field_id']])
			{

				$criteria['some_field'] = array($customField['field_type'],$criteria[$customField['field_id']]);
			}
		}

		$criteria['user_state'] = 'valid';
		$criteria['is_banned'] = 0;


		$search_criteria='You search users where ';

		if (!empty($criteria['username']))
		{
			$search_criteria.=' username like '.$criteria['username'];
		}
		if (!empty($criteria['occupation']))
		{
			$search_criteria=$search_criteria.'occupation like '.$criteria['occupation'];
		}
		if (!empty($criteria['location']))
		{
			$search_criteria=$search_criteria.'location like '.$criteria['location'];
		}
		if (!empty($criteria['any_custom_field']))
		{
			$search_criteria=$search_criteria.' custom field contains '.$criteria['any_custom_field'];
		}

		$userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);
		if ($userId)
		{
			return $this->responseReroute(__CLASS__, 'member');
		}
		else if ($this->_input->inRequest('user_id'))
		{
			return $this->responseError(new XenForo_Phrase('posted_by_guest_no_profile'));
		}


		$username = $this->_input->filterSingle('username', XenForo_Input::STRING);
		if ($username !== '')
		{
			$user = $userModel->getUserByName($username);
			if ($user)
			{
				return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildPublicLink('members', $user)
				);
			}
			else
			{
				$userNotFound = true;
			}
		}
		else
		{
			$userNotFound = false;
		}
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$usersPerPage = XenForo_Application::get('options')->membersPerPage;

		// users for the member list
		$users = $userModel->getUsersByCustomAndProfileFields($criteria, array(
		                                              'join' => XenForo_Model_User::FETCH_USER_PROFILE,
				                                         ));

		$totalUsers=count($users);
		if ($totalUsers==0 or !$users)
		{
			$userNotFound=true;
		}
		else {$userNotFound = false;}
		if (!empty($users))
		{$users=$userModel->getUsersPerPage($users,$page,$usersPerPage);}

		// most recent registrations
		$latestUsers = $userModel->getLatestUsers($criteria, array('limit' => 8));

		// most active users (highest post count)
		$activeUsers = $userModel->getMostActiveUsers($criteria, array('limit' => 12));



		$search = array(
		 "search_query" => $search_criteria,
          "location" =>  $criteria['location'],
          "username" => $criteria['username'],
          "occupation" => $criteria['occupation'],
		"field_type" => $criteria['field_type'],
		"some_field" => $criteria['some_field'],);



		$viewParams = array(
			'users' => $users,

			'totalUsers' => $totalUsers,
			'page' => $page,
			'usersPerPage' => $usersPerPage,

			'latestUsers' => $latestUsers,
			'activeUsers' => $activeUsers,

			'userNotFound' => $userNotFound,
			'search' => $search,
		);

		return $this->responseView('XenForo_ViewPublic_Member_List', 'member_list', $viewParams);
	}
}