HubSpot Forms
=============

HubSpot Forms addon for ExpressionEngine 2

Requirements
------------
* ExpressionEngine 2+
* PHP 5.3+
* A HubSpot Account

Setup
-----
1. Copy the addon and theme files to the proper locations
2. Install the module/fieldtype in EE
3. Set the API Key and Portal ID in the module settings
4. Add a custom field to your channel using the HubSpot Forms field type

Template Usage
--------------

### Single Tag

You can use a single template tag to get a form to display.

```
{exp:channel:entries}
	{hubspot_forms_field}
{/exp:channel:entries}
```

That will output some JavaScript like below which will render your form.

```html
<script charset="utf-8" src="http://js.hubspot.com/forms/current.js"></script>
<script>
	hbspt.forms.create({"portalId":"YOUR_PORTAL_ID","formId":"YOUR_FORM_GUID"});
</script>
```

### Tag Pair

Work in progress