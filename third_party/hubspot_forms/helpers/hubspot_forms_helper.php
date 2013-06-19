<?php

require_once(PATH_THIRD."/hubspot_forms/config.php");

if ( ! function_exists('hubspot_forms_base_url'))
{
	function hubspot_forms_base_url()
	{
		if ( ! defined('BASE'))
		{
			$s = (ee()->config->item('admin_session_type') != 'c') ? ee()->session->userdata('session_id') : 0;
			define('BASE', SELF.'?S='.$s.'&amp;D=cp');
		}

		return BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HUBSPOT_FORMS_PACKAGE;
	}
}

if ( ! function_exists('hubspot_forms_base_action'))
{
	function hubspot_forms_base_action()
	{
		return 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.HUBSPOT_FORMS_PACKAGE;
	}
}

if ( ! function_exists('hubspot_forms_action_id_url'))
{
	function hubspot_forms_action_id_url($class, $method)
	{
		$action_id = ee()->db->where(array('class' => $class, 'method' => $method))->get('actions')->row('action_id');

		if ( ! $action_id)
		{
			return NULL;
		}

		$index = ee()->functions->fetch_site_index(0, 0);

		if ( ! preg_match('/^(https:|http:)/', $index))
		{
			$index = ltrim($index, '/');

			$index = ((empty($_SERVER['HTTPS'])) ? 'http://' : 'https://') . $index;
		}


		return $index.QUERY_MARKER.'ACT='.$action_id;
	}
}