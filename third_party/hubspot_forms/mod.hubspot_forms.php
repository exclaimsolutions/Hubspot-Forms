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

	/**
	 * Start on your custom code here...
	 */

}
/* End of file mod.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/mod.hubspot_forms.php */