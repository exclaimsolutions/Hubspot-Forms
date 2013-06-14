<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package     ExpressionEngine
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license     http://expressionengine.com/user_guide/license.html
 * @link        http://expressionengine.com
 * @since       Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Hubspot Forms Module Install/Update File
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Module
 * @author      Exclaim Solutions
 * @link        http://exclaimsolutions.com/
 */

require_once(PATH_THIRD."/hubspot_forms/config.php");
require_once(PATH_THIRD."/hubspot_forms/libraries/Hubspot_forms_config.php");

class Hubspot_forms_upd {

	public $version = HUBSPOT_FORMS_VERSION;

	// ----------------------------------------------------------------

	/**
	 * Installation Method
	 *
	 * @return  boolean     TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'        => 'Hubspot_forms',
			'module_version'     => $this->version,
			'has_cp_backend'     => "y",
			'has_publish_fields' => 'n'
		);

		ee()->db->insert('modules', $mod_data);

		$action = array(
			'class'  => 'Hubspot_forms_mcp',
			'method' => 'refresh_forms'
		);

		ee()->db->insert('actions', $action);

		ee()->load->dbforge();

		// Create the settings table
		$fields = array(
			'key'        => array('type' => 'VARCHAR', 'constraint' => '100'),
			'value'      => array('type' => 'TEXT'),
			'serialized' => array('type' => 'TINYINT', 'constraint' => '1')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('key', TRUE);

		ee()->dbforge->create_table('hubspot_forms_settings');

		// Create the forms table
		$fields = array(
			'form_id'     => array('type' => 'INT', 'auto_increment' => TRUE),
			'guid'        => array('type' => 'VARCHAR', 'constraint' => '36'),
			'data'        => array('type' => 'TEXT'),
			'last_update' => array('type' => 'DATETIME'),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('form_id', TRUE);
		ee()->dbforge->add_key('guid');

		ee()->dbforge->create_table('hubspot_forms');

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Uninstall
	 *
	 * @return  boolean     TRUE
	 */
	public function uninstall()
	{
		$mod_id = ee()->db->select('module_id')
			->get_where('modules', array(
				'module_name'   => 'Hubspot_forms'
			))->row('module_id');

		ee()->db->where('module_id', $mod_id)
			->delete('module_member_groups');

		ee()->db->where('module_name', 'Hubspot_forms')
			->delete('modules');

		ee()->db->where('class', 'Hubspot_forms')
			->or_where('class', 'Hubspot_forms_mcp')
			->delete('actions');

		ee()->load->dbforge();

		ee()->dbforge->drop_table('hubspot_forms_settings');
		ee()->dbforge->drop_table('hubspot_forms');

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @return  boolean     TRUE
	 */
	public function update($current = '')
	{
		/**
		 * Are we already on the current version?
		 */
		if ($current == '' OR version_compare($current, HUBSPOT_FORMS_VERSION) === 0)
		{
			return FALSE;
		}

		/**
		 * Update to 1.0.1
		 */
		if (version_compare($current, '1.0.1', '<'))
		{
			$this->v101();
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function v101()
	{
		$old_action = array(
			'class'  => 'Hubspot_forms',
			'method' => 'refresh_forms'
		);

		$new_action = array(
			'class'  => 'Hubspot_forms_mcp',
			'method' => 'refresh_forms'
		);

		ee()->db->where($old_action)
			->update('actions', $new_action);
	}


}
/* End of file upd.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/upd.hubspot_forms.php */