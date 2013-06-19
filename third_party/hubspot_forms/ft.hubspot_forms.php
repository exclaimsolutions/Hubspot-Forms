<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hubspot Forms Fieldtype
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

class Hubspot_forms_ft extends EE_Fieldtype {
	public $info = array(
		'name'    => HUBSPOT_FORMS_NAME,
		'version' => HUBSPOT_FORMS_VERSION
	);

	public $has_array_data = TRUE;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		ee()->lang->loadfile('hubspot_forms');
		ee()->load->helper('hubspot_forms_helper');

		parent::__construct();
	}

	// ------------------------------------------------------------------------

	/**
	 * Display the field in the CP
	 *
	 * @param  string $data The value of the field (The GUID in our case)
	 * @return string       The HTML to show
	 */
	function display_field($data)
	{
		$config = new Hubspot_forms_config();
		$f      = new Hubspot_forms_forms();

		if ( ! $config->portal_id OR ! $config->api_key)
		{
			return '<p>'.lang('no_key_portal_id').'</p>';
		}

		$forms = $f->list_forms();

		if (defined('URL_THIRD_THEMES'))
		{
			$asset_url = URL_THIRD_THEMES.'hubspot_forms/';
		}
		else
		{
			$asset_url = ee()->config->item('theme_folder_url') . 'third_party/hubspot_forms/';
		}

		$form_names = array(
			'' => lang('select_form'),
		);

		foreach ($forms AS $form)
		{
			$form_names[$form->guid] = $form->name;
		}

		$js_data['action_url'] = hubspot_forms_action_id_url('Hubspot_forms_mcp', 'refresh_forms');

		// Add CSS and JS
		ee()->cp->add_to_head("<link rel='stylesheet' type='text/css' href='{$asset_url}chosen/chosen.css'>");
		ee()->cp->add_to_foot("<script type='text/javascript' src='{$asset_url}chosen/chosen.jquery.min.js'></script>");
		ee()->cp->add_to_head('<style type="text/css"> .hubspot_forms_refresh { margin-left: 10px; cursor: pointer }</style>');
		ee()->javascript->output(array(ee()->load->view('publish_js', $js_data, TRUE)));

		$dd_attr = 'class="chzn-select hubspot_forms_dropdown"'
			." id='{$this->field_name}'";

		// Add the dropdown an refresh button to the return
		$return = form_dropdown($this->field_name, $form_names, $data, $dd_attr);
		$return .= form_button($this->field_name.'_refresh', lang('refresh_forms'), 'class="submit hubspot_forms_refresh"');

		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Render the tag in the front-end templates
	 *
	 * @param  string  $data    The value of the field (The GUID in our case)
	 * @param  array   $params  The tag parameters
	 * @param  mixed   $tagdata The data between the tag pairs
	 * @return string           The text to render in the template
	 */
	public function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		ee()->load->helper('form');

		$config = new Hubspot_forms_config();
		$forms  = new Hubspot_forms_forms();
		$form   = $forms->get_form($data);

		if ( ! $form)
		{
			return;
		}

		// If there is no tagdata display the form embed code
		if ( ! $tagdata) {
			return $this->embed_code($form->guid, $config->portal_id, $params);
		}

		// Sort the fields by the display order
		usort($form->fields, function($a, $b) {
			return $a->displayOrder > $b->displayOrder;
		});

		$errors = ee()->session->flashdata('form_errors');
		$values = ee()->session->flashdata('form_values');

		// Build an array of field template tags to parse
		$fields = array();
		foreach ($form->fields AS $f)
		{
			$field['field:label']      = $f->label;
			$field['field:name']       = $f->name;
			$field['field:type']       = $f->fieldType;
			$field['field:value']      = isset($values[$f->name]) ? $values[$f->name] : $f->defaultValue;
			$field['field:required']   = $f->required;
			$field['field:unselected'] = $f->unselectedLabel;

			$field['field:error']  = '';
			$field['field:errors'] = array();

			// Build array of error template tags to parse
			if (isset($errors[$f->name]) AND is_array($errors[$f->name]))
			{
				$field_errors = $errors[$f->name];

				$field['field:error'] = $field_errors[0];

				$field['field:errors'] = array();
				foreach ($field_errors AS $error)
				{
					$field['field:errors'][] = array(
						'error' => $error
					);
				}
			}

			$field['field:error_count'] = count($field['field:errors']);

			// If the field has options build an array of tags
			// for them
			if (is_array($f->options) AND count($f->options) > 0)
			{
				foreach ($f->options AS $option)
				{
					$selected = ($field['field:value'] == $option->value) ? 'selected' : '';

					$field['field:options'][] = array(
						'option:label'    => $option->label,
						'option:value'    => $option->value,
						'option:selected' => $selected
					);
				}
			}

			$fields[] = $field;
		}

		$vars['errors'] = array();

		// Build array of error tags for the form
		if (is_array($errors) AND count($errors) > 0)
		{
			$flat_errors = $this->array_flatten($errors);

			foreach ($flat_errors AS $error)
			{
				$vars['errors'][] = array(
					'error' => $error
				);
			}
		}

		// Form tags to parse
		$vars['name']        = $form->name;
		$vars['submit']      = $form->submitText;
		$vars['error_count'] = count($vars['errors']);
		$vars['fields']      = $fields;

		$return = form_open(hubspot_forms_action_id_url('Hubspot_forms', 'submit_form'));
		$return .= form_hidden('guid', $form->guid);
		$return .= form_hidden('portal_id', $config->portal_id);
		$return .= form_hidden('return', $params['return'] ?: ee()->functions->fetch_current_uri());
		$return .= ee()->TMPL->parse_variables_row($tagdata, $vars);
		$return .= form_close();

		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Render the JS embed code for the form
	 *
	 * @param  string $guid      The GUID of the form
	 * @param  int    $portal_id The HubSpot portal ID
	 * @param  array  $params    The template tag parameters
	 * @return string            The embed code
	 */
	public function embed_code($guid, $portal_id, $params)
	{
		$options = array(
			'portalId' => $portal_id,
			'formId'   => $guid,
		);

		if (isset($params['return']))
		{
			$return = $params['return'];

			if ( ! preg_match('/^(http|\/\/)/', $return))
			{
				$return = ee()->functions->create_url($params['return']);
			}

			$options['redirectUrl'] = $return;
		}

		if (isset($params['css']))
		{
			$options['css'] = $params['css'];
		}

		$json_options = json_encode($options);

		return <<<CODE
<script charset="utf-8" src="http://js.hubspot.com/forms/current.js"></script>
<script>
	hbspt.forms.create({$json_options});
</script>
CODE;
	}

	// ------------------------------------------------------------------------

	private function array_flatten($array, $return = array())
	{
		foreach($array AS $value)
		{
			if(is_array($value))
			{
				$return = $this->array_flatten($value, $return);
			}
			else
			{
				if($value)
				{
					$return[] = $value;
				}
			}
		}

		return $return;
	}
}

/* End of file ft.hubspot_forms.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/ft.hubspot_forms.php */