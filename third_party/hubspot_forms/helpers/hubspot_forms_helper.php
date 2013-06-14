<?php

require_once(PATH_THIRD."/hubspot_forms/config.php");

if ( ! function_exists('hubspot_forms_base_url'))
{
	function hubspot_forms_base_url()
	{
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