<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(PATH_THIRD."/hubspot_forms/config.php");

$lang = array(
	'hubspot_forms_module_name'        => HUBSPOT_FORMS_NAME,
	'hubspot_forms_module_description' =>'Provides integration with HubSpot forms',
	'module_home'                      => 'Hubspot Forms Home',

	// Start inserting custom language keys/values here
	'no_key_portal_id' => 'You must set an API Key and Portal ID',
	'select_form'      => '-- Select a Form --',
	'refresh_forms'    => 'Refresh Form List',

	// Errors
	'invalid_guid'  => 'The GUID of the form  you submitted was not valid',
	'server_error'  => 'A server error has occured, please try again later',
	'unknown_error' => 'An unknown error has occured, please try again later',
);

/* End of file lang.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/language/english/lang.hubspot_forms.php */
