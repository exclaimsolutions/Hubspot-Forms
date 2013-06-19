<?php

if ( ! defined('HUBSPOT_FORMS_NAME'))
{
	define('HUBSPOT_FORMS_NAME',    'HubSpot Forms');
	define('HUBSPOT_FORMS_PACKAGE', 'hubspot_forms');
	define('HUBSPOT_FORMS_VERSION', '1.0.2');
}

/**
 * < EE 2.6.0 backward compat
 */
if ( ! function_exists('ee'))
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}