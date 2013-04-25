<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Оленька
 * Date: 26.01.13
 * Time: 21:41
 * To change this template use File | Settings | File Templates.
 */

class Akinak_CustomUserFieldSearch_Model_User extends XFCP_Akinak_CustomUserFieldSearch_Model_User
{
	public function prepareUserConditionsExtended(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();

		if (!empty($conditions['user_id']))
		{
			if (is_array($conditions['user_id']))
			{
				$sqlConditions[] = 'user.user_id IN(' . $db->quote($conditions['user_id']) . ')';
			}
			else
			{
				$sqlConditions[] = 'user.user_id = ' . $db->quote($conditions['user_id']);
			}
		}

		if (!empty($conditions['username']))
		{
			if (is_array($conditions['username']))
			{
				$sqlConditions[] = 'user.username LIKE ' . XenForo_Db::quoteLike($conditions['username'][0], $conditions['username'][1], $db);
			}
			else
			{
				$sqlConditions[] = 'user.username LIKE ' . XenForo_Db::quoteLike($conditions['username'], 'lr', $db);
			}
		}

		// this is mainly for dynamically filtering a search that already matches user names
		if (!empty($conditions['username2']))
		{
			if (is_array($conditions['username2']))
			{
				$sqlConditions[] = 'user.username LIKE ' . XenForo_Db::quoteLike($conditions['username2'][0], $conditions['username2'][1], $db);
			}
			else
			{
				$sqlConditions[] = 'user.username LIKE ' . XenForo_Db::quoteLike($conditions['username2'], 'lr', $db);
			}
		}

		if (!empty($conditions['usernames']) && is_array($conditions['usernames']))
		{
			$sqlConditions[] = 'user.username IN (' . $db->quote($conditions['usernames']) . ')';
		}

		if (!empty($conditions['email']))
		{
			if (is_array($conditions['email']))
			{
				$sqlConditions[] = 'user.email LIKE ' . XenForo_Db::quoteLike($conditions['email'][0], $conditions['email'][1], $db);
			}
			else
			{
				$sqlConditions[] = 'user.email LIKE ' . XenForo_Db::quoteLike($conditions['email'], 'lr', $db);
			}
		}
		if (!empty($conditions['emails']) && is_array($conditions['emails']))
		{
			$sqlConditions[] = 'user.email IN (' . $db->quote($conditions['emails']) . ')';
		}

		if (!empty($conditions['user_group_id']))
		{
			if (is_array($conditions['user_group_id']))
			{
				$sqlConditions[] = 'user.user_group_id IN (' . $db->quote($conditions['user_group_id']) . ')';
			}
			else
			{
				$sqlConditions[] = 'user.user_group_id = ' . $db->quote($conditions['user_group_id']);
			}
		}

		if (!empty($conditions['secondary_group_ids']))
		{
			if (is_array($conditions['secondary_group_ids']))
			{
				$groupConds = array();
				foreach ($conditions['secondary_group_ids'] AS $groupId)
				{
					$groupConds[] = 'FIND_IN_SET(' . $db->quote($groupId) . ', user.secondary_group_ids)';
				}
				$sqlConditions[] = '(' . implode(' OR ', $groupConds) . ')';
			}
			else
			{
				$sqlConditions[] = 'FIND_IN_SET(' . $db->quote($conditions['secondary_group_ids']) . ', user.secondary_group_ids)';
			}
		}

		if (!empty($conditions['last_activity']) && is_array($conditions['last_activity']))
		{
			list($operator, $cutOff) = $conditions['last_activity'];

			$this->assertValidCutOffOperator($operator);
			$sqlConditions[] = "user.last_activity $operator " . $db->quote($cutOff);
		}

		if (!empty($conditions['message_count']) && is_array($conditions['message_count']))
		{
			list($operator, $cutOff) = $conditions['message_count'];

			$this->assertValidCutOffOperator($operator);
			$sqlConditions[] = "user.message_count $operator " . $db->quote($cutOff);
		}

		if (!empty($conditions['user_state']) && $conditions['user_state'] !== 'any')
		{
			if (is_array($conditions['user_state']))
			{
				$sqlConditions[] = 'user.user_state IN (' . $db->quote($conditions['user_state']) . ')';
			}
			else
			{
				$sqlConditions[] = 'user.user_state = ' . $db->quote($conditions['user_state']);
			}
		}

		if (isset($conditions['is_admin']))
		{
			$sqlConditions[] = 'user.is_admin = ' . ($conditions['is_admin'] ? 1 : 0);
		}

		if (isset($conditions['is_moderator']))
		{
			$sqlConditions[] = 'user.is_moderator = ' . ($conditions['is_moderator'] ? 1 : 0);
		}

		if (isset($conditions['is_banned']))
		{
			$sqlConditions[] = 'user.is_banned = ' . ($conditions['is_banned'] ? 1 : 0);
		}

		if (!empty($conditions['receive_admin_email']))
		{
			$sqlConditions[] = 'user_option.receive_admin_email = 1';
			$this->addFetchOptionJoin($fetchOptions, self::FETCH_USER_OPTION);
		}

		if (!empty($conditions['adminQuickSearch']))
		{
			$quotedString = XenForo_Db::quoteLike($conditions['adminQuickSearch'], 'lr', $db);

			$sqlConditions[] = 'user.username LIKE ' . $quotedString . ' OR user.email LIKE ' . $quotedString;
		}
		//User Profile Field
		if (!empty($conditions['location']))
		{
			$quotedString = XenForo_Db::quoteLike($conditions['location'], 'lr', $db);
			$sqlConditions[] = 'location LIKE ' . $quotedString;
		}

		if (!empty($conditions['occupation']))
		{
			$sqlConditions[] = 'occupation = ' . $db->quote($conditions['occupation']);
		}


		if (!empty($conditions['some_field']) AND (is_array($conditions['some_field'])))
		{
		$needUsers=$this->getUsersByCustomField($conditions['some_field'][0],$conditions['some_field'][1]);
			if (isset($needUsers))
			{
				if (!empty($needUsers) AND is_array($needUsers))
				{
					$sqlConditions[] = 'user.user_id IN(' . $db->quote($needUsers) . ')';
				}
			}
		}


		//Custom User Profile Field

		if (!empty($conditions['any_custom_field']))
			{
				$needUsers=$this->getUsersByCustomField('any_custom_field',$conditions['any_custom_field']);
				if (isset($needUsers))
				{
					if (!empty($needUsers) AND is_array($needUsers))
					{
						$sqlConditions[] = 'user.user_id IN(' . $db->quote($needUsers) . ')';
					}
				}
			}

		$customFields=$this->getCustomFieldsName();

		foreach ($customFields as $customField)
		{
			$field_id=$customField['field_id'];
			if (!empty($conditions[$field_id]))
			{
				$needUsers=$this->getUsersByCustomField($field_id,$conditions[$field_id]);
				if (isset($needUsers))
				{
					if (!empty($needUsers) AND is_array($needUsers))
					{
						$sqlConditions[] = 'user.user_id IN(' . $db->quote($needUsers) . ')';
					}
				}
			}
		}

		return $this->getConditionsForClause($sqlConditions);
	}

	/**
	 * @param $field_id
	 * @param $field_value
	 * @return array
	 */
	public function getUsersByCustomField($field_id, $field_value)
	{
		$field_value= addslashes($field_value);
		if ($field_id === 'any_custom_field')
		{
			$query="
			SELECT user_id
            FROM `xf_user_field_value`
 			WHERE `field_value` = '".$field_value."'";
		}
		else
		{
			$query="
			SELECT user_id
            FROM `xf_user_field_value`
 			WHERE `field_id` = '".$field_id."' AND `field_value` LIKE '%".$field_value."%'";
		}
		$maxResults=1000;

		$sql = $this->limitQueryResults($query, $maxResults);
		return $this->fetchAllKeyed($sql, 'user_id');
	}


	public function getUsersByCustomAndProfileFields(array $conditions, array $fetchOptions = array())
	{
		$whereClause = $this->prepareUserConditionsExtended($conditions, $fetchOptions);

		$orderClause = $this->prepareUserOrderOptions($fetchOptions, 'user.username');
		$joinOptions = $this->prepareUserFetchOptions($fetchOptions);
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		return $this->fetchAllKeyed($this->limitQueryResults(
			'
				SELECT user.*
					' . $joinOptions['selectFields'] . '
				FROM xf_user AS user
				' . $joinOptions['joinTables'] . '
				WHERE ' . $whereClause . '
				' . $orderClause . '
			', $limitOptions['limit'], $limitOptions['offset']
		), 'user_id');
	}

	public function getUsersPerPage($users,$page,$usersPerPage)
	{
		if (count($users)<$usersPerPage)
			{return $users;}
		if ($page>0)
		{$page-=1;}
		return  $output = array_slice($users, $page*$usersPerPage,$usersPerPage);
	}

	/**
	 * @return mixed
	 */
	public function getCustomFieldsName()
	{
		$maxResults=1000;
		$query="
			SELECT field_id,field_type
            FROM `xf_user_field`";
		$sql = $this->limitQueryResults($query, $maxResults);
		return $this->fetchAllKeyed($sql, 'field_id');
	}

	public function getLocationName($type)
	{
		if ($type ==='location')
		{
		$maxResults=100000;
		$query="
			SELECT location
            FROM `xf_user_profile`";
		$sql = $this->limitQueryResults($query, $maxResults);
		$result = $this->fetchAllKeyed($sql, 'location');
			return $result;
		}
		if ((!$type)OR ($type==''))
		{
			return false;
		}

		$maxResults=100000;
		$query="
			SELECT field_value
            FROM `xf_user_field_value`
            WHERE `field_id` = '".$type."'
            ";
		$sql = $this->limitQueryResults($query, $maxResults);
		$results = $this->fetchAllKeyed($sql, '');
		$final_results=array();
		foreach ($results as $result)
		{
			$final_results[]=explode(',',$result['field_value']);
		}
		$final = array();
		foreach ($final_results as $result)
		{
			$final = array_merge($final,$result);
		}
		foreach ($final as $key=>$result)
		{
			$final[$key]=trim($result);
		}
		return $final;
	}
}