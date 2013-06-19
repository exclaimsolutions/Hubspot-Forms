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

#### Available Tags
{name} - The name of the form on HubSpot  
{submit} - The value of the submit button  
{error_count} - The number of errors for the form  
{errors}{/errors} - Contains all validation errors for the form  
{fields}{/fields} - The loop to display the form fields  

**Field Tags**
These are used inside the {fields} tag pair  
{field:label}  
{field:name}  
{field:type} - The field type text/textarea/select/etc  
{field:value} - Contains the default value or the value a user entered before a validation error  
{field:required} - TRUE if a field is required, useful for conditionals  
{field:unselected} - The option text for the unselected value of a select element eg. <option value="">{field:unselected}</option>  
{field:error} - The first validation error for a field  
{field:error_count} - The number of errors for the field  
{field:errors}{/field:errors} - All the validation errors for a field  

**Errors**
These tags are used inside the {errors} and {field:errors} tag pairs  
{error} - The error message  

#### Example
```html
{exp:channel:entries}
	{hubspot_form return='thanks'}
		<h1>{name}</h1>

		{!-- Error Method 1: Show all the errors for the form --}
		{if error_count > 0}
			<p>You have {error_count} errors, please fix them and re-submit the form</p>
			<ul class="errors">
				{errors}
					<li class="error">{error}</li>
				{/errors}
			</ul>
		{/if}

		{fields}
			{!-- Error Method 2: Shows only the first error message for the field --}
			{if field:error}
				<p class="error">{field:error}</p>
			{/if}

			{!-- Error Method 3: Show all the errors for the field --}
			{if field:error_count > 0}
				<ul class="errors">
					{field:errors}
						<li class="error">{error}</li>
					{/field:errors}
				</ul>
			{/if}

			<label>{field:label} {if field:required}*{/if}</label>

			{if field:type == "text"}
				<input type="text" name="{field:name}" value="{field:value}">
			{/if}

			{if field:type == "textarea"}
				<textarea name="{field:name}">{field:value}</textarea>
			{/if}

			{if field:type == "select"}
				<select name="{field:name}">
					<option value="{field:value}">{field:unselected}</option>
					{field:options}
						<option {option:selected} value="{option:value}">{option:label}</option>
					{/field:options}
				</select>
			{/if}

			{if field:type == "radio"}
				{field:options}
					<label><input {option:selected} type="radio" name="{field:name}" value="{option:value}">{option:label}</label>
				{/field:options}
			{/if}
		{/fields}

		<input type="submit" value="{submit}">
	{/hubspot_form}
{/exp:channel:entries}
```