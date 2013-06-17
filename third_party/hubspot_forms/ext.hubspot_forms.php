<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hubspot Forms Extension
 *
 * @package    HubSpot Forms
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author     Joseph Wensley <joseph@exclaimsolutions.com>
 * @copyright  Copyright (c) 2013 Exclaim Solutions
 * @link       http://exclaimsolutions.com/
 */

require_once(PATH_THIRD."/hubspot_forms/config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/sdk/class.forms.php");

class Hubspot_forms_ext {

	public $settings        = array();
	public $description     = 'Provides integration with HubSpot forms';
	public $docs_url        = '';
	public $name            = HUBSPOT_FORMS_NAME;
	public $settings_exist  = 'n';
	public $version         = HUBSPOT_FORMS_VERSION;

	private $EE;

	/**
	 * Constructor
	 *
	 * @param   mixed   Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	// ----------------------------------------------------------------------

	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();

		$data = array(
			'class'     => __CLASS__,
			'method'    => 'sessions_end',
			'hook'      => 'sessions_end',
			'settings'  => serialize($this->settings),
			'version'   => $this->version,
			'enabled'   => 'y'
		);

		$this->EE->db->insert('extensions', $data);

	}

	// ----------------------------------------------------------------------

	/**
	 * sessions_end
	 *
	 * @param
	 * @return
	 */
	public function sessions_end()
	{
		// Add Code for the sessions_end hook here.
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return  mixed   void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}

	// ----------------------------------------------------------------------
}

/* End of file ext.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/ext.hubspot_forms.php */