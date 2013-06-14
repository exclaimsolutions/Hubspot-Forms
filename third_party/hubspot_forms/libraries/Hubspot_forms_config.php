<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hubspot_forms_config {
	private static $config = array();

	/**
	 * Set the config setting
	 *
	 * @param string $key   The key for the setting
	 * @param mixed  $value  The value to set to
	 */
	public function __set($key, $value)
	{
		// Store the setting so we don't need to access the database
		// to retrieve it later
		self::$config[$key] = $value;

		$serialized = FALSE;

		// If the value is an array or object we need to serialize
		// it for storage
		if (is_array($value) OR is_object($value))
		{
			$serialized = TRUE;
			$value      = serialize($value);
		}

		// Check to see if the setting is in the DB already
		$count = ee()->db->where('key', $key)
			->count_all_results('hubspot_forms_settings');

		if ($count == 0)
		{
			ee()->db->set('key', $key)
				->set('value', $value)
				->set('serialized', $serialized)
				->insert('hubspot_forms_settings');
		}
		else
		{
			ee()->db->where('key', $key)
				->set('value', $value)
				->set('serialized', $serialized)
				->update('hubspot_forms_settings');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get the config setting
	 *
	 * @param  string $key The key of the setting to get
	 * @return mixed       The value of the setting
	 */
	public function __get($key)
	{
		// Check to see if the setting is cached in the config array
		if (isset(self::$config[$key]))
		{
			return self::$config[$key];
		}

		// Get the setting from the DB
		$setting = ee()->db->where('key', $key)
			->get('hubspot_forms_settings')
			->row();

		if ( ! $setting)
		{
			self::$config[$key] = NULL;
		}
		else if ($setting->serialized == TRUE)
		{
			// If the value was serialized we need to unserialize it
			self::$config[$key] = unserialize($setting->value);
		}
		else
		{
			self::$config[$key] = $setting->value;
		}

		return self::$config[$key];
	}
}

/* End of file hubspot_forms_config.php */
/* Location: /system/expressionengine/third_party/hubspot_forms/libraries/hubspot_forms_config.php */