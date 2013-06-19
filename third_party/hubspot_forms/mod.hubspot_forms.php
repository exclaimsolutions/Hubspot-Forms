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

	private $validation_errors = array();

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

		$context['ipAddress']   = $_SERVER['REMOTE_ADDR'];

		if (isset($_COOKIE['hutk']))
		{
			$context['hutk'] = $_COOKIE['hutk'];
		}

		$return = ee()->input->post('return') ?: ee()->functions->form_backtrack(2);

		foreach ($form->fields AS $field)
		{
			$data[$field->name] = ee()->input->post($field->name);
		}

		if ($this->validate_submission($data, $form->fields))
		{
			$api->submit_form($portal_id, $guid.'111', $data, $context);
			ee()->functions->redirect($return);
		}

		ee()->session->set_flashdata('form_values', $data);
		ee()->session->set_flashdata('validation_errors', $this->validation_errors);

		ee()->functions->redirect(ee()->functions->form_backtrack(2));
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate the form submission
	 * @param  array $data   An array containing the post data
	 * @param  array $fields An array containing the field objects
	 * @return bool          Did the validation succeed?
	 */
	private function validate_submission($data, $fields)
	{
		$errors = array();

		foreach ($fields AS $field)
		{
			$name = &$field->name;

			if ($field->required AND empty($data[$name]))
			{
				$errors[$name][] = $field->label.' is required.';
			}
		}

		if (count($errors) > 0)
		{
			$this->validation_errors = $errors;

			return FALSE;
		}

		return TRUE;
	}

}
/* End of file mod.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/mod.hubspot_forms.php */