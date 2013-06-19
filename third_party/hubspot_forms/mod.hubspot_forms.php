<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hubspot Forms Module Front End File
 *
 * @package    HubSpot Forms
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author     Joseph Wensley <joseph@exclaimsolutions.com>
 * @copyright  Copyright (c) 2013 Exclaim Solutions
 * @link       http://exclaimsolutions.com/
 */

require_once(PATH_THIRD."/hubspot_forms/config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_forms.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/sdk/class.forms.php");

class Hubspot_forms {

	public $return_data;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	// ----------------------------------------------------------------

	public function submit_form()
	{
		$guid      = ee()->input->post('guid');
		$portal_id = ee()->input->post('portal_id');

		if ( ! $guid OR ! $portal_id)
		{
			exit("NO GUID OR PORTAL ID");
		}

		$config = new Hubspot_forms_config();
		$forms  = new Hubspot_forms_forms();
		$api    = new HubSpot\HubSpot_Forms($config->api_key);

		$form = $forms->get_form($guid);

		$data = array();

		$context['redirectUrl'] = ee()->input->post('redirectUrl');

		foreach ($form->fields AS $field)
		{
			$data[$field->name] = ee()->input->post($field->name);
		}

		$api->submit_form($portal_id, $guid, $data, $context);
	}

}
/* End of file mod.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/mod.hubspot_forms.php */