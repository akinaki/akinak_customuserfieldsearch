<?php

/**
 * Model for custom user fields.
 */
class Akinak_CustomUserFieldSearch_Model_UserField extends XFCP_Akinak_CustomUserFieldSearch_Model_UserField
{
	public function getUserFields(array $conditions = array(), array $fetchOptions = array())
	{
		$whereClause = $this->prepareUserFieldConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareUserFieldFetchOptions($fetchOptions);

		$fields =
			$this->fetchAllKeyed('
			SELECT user_field.*
				' . $joinOptions['selectFields'] . '
			FROM xf_user_field AS user_field
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause . '
			ORDER BY user_field.display_group, user_field.display_order
		', 'field_id');
		//Правка!!!
		foreach ($fields as $key=>$field)
		{
			if (isset($field['field_value']) AND $field['display_group']='personal')
			{
			$fields[$key]['extra_fields']= explode(',',$field['field_value']);
			}
		}
		//Конец правки
		return $fields;
	}
}