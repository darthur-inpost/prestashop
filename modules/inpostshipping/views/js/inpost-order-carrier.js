jQuery(document).ready(function(){

	jQuery('input[type="checkbox"][name="show_all_machines"]').click(function(){
		var machines_list_type = jQuery(this).is(':checked');

		if(machines_list_type == true)
		{
			//alert('all machines');
			jQuery('#inpostlocker').show('fast');
			jQuery('#shortinpostlocker').hide('fast');
		}
		else
		{
			//alert('criteria machines');
			jQuery('#inpostlocker').hide('fast');
			jQuery('#shortinpostlocker').show('fast');
		}
	});

	jQuery(document).on('submit', 'form[name=carrier_area]', function(){
		return inpost_check_extra_fields();
	});
});

///
// inpost_check_extra_fields
//
// @brief Make sure that the user has entered a mobile number and locker
//
function inpost_check_extra_fields()
{
	var test_key      = jQuery('#inpost_key').val();
	var radio_select  = jQuery('input[name="delivery_option[5]"]:checked').val();
	var short_machine = jQuery('#shortinpostlocker').val();
	var machine       = jQuery('#inpostlocker').val();
	var phone         = jQuery('#inpost_phone').val();

//	alert('key = ' + test_key + ' radio = ' + radio_select +
//			' short machine ' + short_machine +
//			' machine ' + machine +
//			' phone ' + phone);

	if (test_key == radio_select &&
		(phone.length < 9 ||
		 short_machine.length == 0 &&
		 machine.lnegth == 0))
	{
		if (!!$.prototype.fancybox)
		    $.fancybox.open([
		{
			type: 'inline',
			autoScale: true,
			minHeight: 30,
			content: '<p class="fancybox-error">' + msg_inpost_check_extra_fields + '</p>'
	        }],
			{
			padding: 0
		    });
		else
			alert(msg_inpost_check_extra_fields);

		return false;
	}

	return true;
}
