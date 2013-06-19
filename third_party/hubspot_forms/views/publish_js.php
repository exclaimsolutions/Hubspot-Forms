$(".hubspot_forms_dropdown").chosen();

$(".hubspot_forms_refresh").on("click", function(){
	var request = $.get("<?php echo $action_url ?>", function(data) {
		var $dropdowns = $(".hubspot_forms_dropdown");

		// Store selected values for each dropdown
		$dropdowns.each(function() {
			$(this).data('selected', $(this).val());
		});

		// Remove all but the first option
		$dropdowns.children().slice(1).remove();

		if (data.success != false) {
			$.each(data.forms, function(key, form) {
				var $option = $(document.createElement('option'));

				$option.text(form.name);
				$option.val(form.guid);

				$dropdowns.append($option);

				// Let Chosen know the dropdown has been updated
				$(".hubspot_forms_dropdown").trigger("liszt:updated");
			});

			$.ee_notice("Form list has been refreshed", {type: "success"});
		} else {
			$.ee_notice(data.error, {type: "error"});
		}

		// Restore selected values
		$dropdowns.each(function() {
			$(this).val($(this).data('selected')).trigger("liszt:updated");
		});
	}, "json");

	// Handle Server Errors
	request.fail(function(data) {
		if (data.responseText) {
			var response = $.parseJSON(data.responseText);

			$.ee_notice(response.error, {type: "error"});
		} else {
			$.ee_notice(data.statusText, {type: "error"});
		}
	})
});