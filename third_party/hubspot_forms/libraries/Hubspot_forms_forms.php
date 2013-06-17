<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package    HubSpot Forms
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author     Joseph Wensley <joseph@exclaimsolutions.com>
 * @copyright  Copyright (c) 2013 Exclaim Solutions
 * @link       http://exclaimsolutions.com/
 */

require_once(PATH_THIRD."/hubspot_forms/config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/sdk/class.forms.php");

class Hubspot_forms_forms {

	/**
	 * Get a list of forms from the database
	 *
	 * @param  boolean  $refresh  Should we refresh the list from the API first?
	 * @return array              An array containing the forms
	 */
	public function list_forms($refresh = FALSE, $sort = TRUE)
	{
		if ($refresh)
		{
			$this->refresh_forms();
		}

		$forms = ee()->db->get('hubspot_forms')
			->result();

		$return = array();

		// Unserialize the data for each form
		foreach ($forms AS $form)
		{
			$return[] = unserialize($form->data);
		}

		if ($sort)
		{
			usort($return, function($a, $b) {
				return strcasecmp($a->name, $b->name);
			});
		}

		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Retrieve forms from the HubSpot API and cache them
	 * in our DB
	 *
	 * @return mixed
	 */
	private function refresh_forms()
	{
		ee()->load->library('logger');
		$config = new Hubspot_forms_config();

		if ( ! $config->api_key)
		{
			return 'You must set an API key!';
		}

		$api = new HubSpot_Forms($config->api_key);

		if (isset($forms->status) AND $forms->status == 'error')
		{
			ee()->logger->developer(HUBSPOT_FORMS_NAME.': '.$form->message, TRUE);
			return;
		}

		// Empty the forms currently in the table
		ee()->db->empty_table('hubspot_forms');

		$forms = $api->get_forms();

		foreach ($forms AS $form)
		{
			$this->save_form($form);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get a form from the database or from the API if
	 * it is not in the DB or is too old
	 *
	 * @param  string $guid The guid of the form from the HubSpot API
	 * @return mixed        An object containing the form or false on error
	 */
	public function get_form($guid)
	{
		$api_error = FALSE;

		ee()->load->library('logger');

		$config = new Hubspot_forms_config();

		if ( ! $config->api_key)
		{
			ee()->logger->developer(HUBSPOT_FORMS_NAME.': You must set an API Key');
			return FALSE;
		}

		// Get the form from the DB
		$dbform = ee()->db->where('guid', $guid)
			->get('hubspot_forms')
			->row();

		// Set vars for age comparison
		$age     = isset($dbform->last_update) ? strtotime($dbform->last_update.' GMT') : 0;
		$max_age = time() - 3600; // Refresh forms more than an hour old

		// If the form wasn't in the DB or is too old then retrieve it from the API
		if ( ! $dbform OR $max_age > $age)
		{
			$api = new HubSpot_Forms($config->api_key);

			$apiform = $api->get_form_by_id($guid, TRUE);

			// Check for an API error
			if (isset($apiform->status) AND $apiform->status == 'error')
			{
				ee()->logger->developer(HUBSPOT_FORMS_NAME.': '.$apiform->message);
				$api_error = TRUE;
			}

			if ( ! $api_error)
			{
				// Save form to the DB
				$this->remove_form($apiform->guid);
				$this->save_form($apiform);

				return $apiform;
			}
			elseif ( ! $dbform AND $api_error)
			{
				// No form in the database and we couldn't get it from the API
				return FALSE;
			}
		}

		return unserialize($dbform->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Save forms retrieved from the HubSpot API
	 *
	 * @param  object $forms
	 * @return void
	 */
	public function save_form($form)
	{
		$data = array(
			'guid'         => $form->guid,
			'data'         => serialize($form),
			'last_update'  => gmdate("Y-m-d H:i:s"),
		);

		ee()->db->insert('hubspot_forms', $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Remove a form from the database
	 *
	 * @param  string $guid
	 * @return void
	 */
	private function remove_form($guid)
	{
		ee()->db->where('guid', $guid)
			->delete('hubspot_forms');
	}
}

/* End of file hubspot_forms_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/libraries/hubspot_forms_forms.php */