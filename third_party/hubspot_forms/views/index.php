<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading('Setting', 'Value');

	$this->table->add_row(array(
		form_label('API Key', 'hubspot_forms_api_key'),
		form_input('api_key', $config->api_key, 'id="hubspot_forms_api_key"')
	));

	$this->table->add_row(array(
		form_label('Portal ID', 'hubspot_forms_portal_id'),
		form_input('portal_id', $config->portal_id, 'id="hubspot_forms_portal_id"')
	));

	echo form_open($action_url);
	echo $this->table->generate();

	echo form_submit(array(
		'name'	=> 'save_settings',
		'value'	=> 'Save Settings',
		'class'	=> 'submit'
	));

	echo form_close();