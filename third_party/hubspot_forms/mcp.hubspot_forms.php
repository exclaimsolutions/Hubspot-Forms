<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Hubspot Forms Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Exclaim Solutions
 * @link		http://exclaimsolutions.com/
 */

require_once(PATH_THIRD."/hubspot_forms/config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_forms.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/sdk/class.forms.php");

class Hubspot_forms_mcp {

	public $return_data;

	private $base_url;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		ee()->load->helper('hubspot_forms_helper');

		$this->base_url    = hubspot_forms_base_url();
		$this->base_action = hubspot_forms_base_action();

		ee()->cp->set_right_nav(array(
			'module_home'	=> $this->base_url,
		));
	}

	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
		ee()->load->library('table');
		ee()->load->helper('form');

		$config = new Hubspot_forms_config();

		ee()->cp->set_variable('cp_page_title',
			lang('hubspot_forms_module_name'));

		$data['action_url'] = $this->base_action.AMP.'method=save_settings';
		$data['config']     = $config;

		return ee()->load->view('index', $data, TRUE);
	}

	// ------------------------------------------------------------------------

	/**
	 * Save the settings passed from the settings form
	 *
	 * @return void
	 */
	public function save_settings()
	{
		$config = new Hubspot_forms_config();

		// Save the settings
		$config->api_key   = trim(ee()->input->post('api_key'));
		$config->portal_id = trim(ee()->input->post('portal_id'));

		// Set a success message and redirect
		ee()->session->set_flashdata('message_success', 'Settings Saved');
		ee()->functions->redirect($this->base_url);
	}

	// ------------------------------------------------------------------------

	/**
	 * Refresh the forms from the API and output
	 * a json response
	 *
	 * @return void
	 */
	public function refresh_forms()
	{
		$f     = new Hubspot_forms_forms();
		$forms = $f->list_forms(TRUE);

		$data = array();

		foreach ($forms AS $form)
		{
			$data['forms'][] = array(
				'guid' => $form->guid,
				'name' => $form->name
			);
		}

		if ( ! $forms OR count($forms) == 0)
		{
			$data['success'] = FALSE;
			$data['error']   = 'No forms found.';
		}

		echo json_encode($data);
		exit;
	}
}

/* End of file mcp.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/mcp.hubspot_forms.php */