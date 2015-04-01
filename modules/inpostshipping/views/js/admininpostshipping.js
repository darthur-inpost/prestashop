///
// user_function
//
// @brief Update the machine select
//
function user_function(value)
{
	var address = value.split(';');

	jQuery('#parcel_target_machine_id option[value=\'' + address[0] + 
			'\'').attr('selected', 'selected');
}

