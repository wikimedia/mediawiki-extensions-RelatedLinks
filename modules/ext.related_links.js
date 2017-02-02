$(document).ready(function(){
	$('#checkall').click(function () {
		if($(this).is(':checked')) {
			$('input.checkitem').prop('checked',true);
		} else {
			$('input.checkitem').prop('checked',false);
		}
	});
	
	$('#btn_delete-sidebar').click(function () {
		if(isSelected() == true ) {
			$('#submit_type').val('DELETE');
			$('#related_links_form' ).submit();
		} else {
			alert( 'Select items..' );
		}
	});
	
	$('#btn_insert-sidebar').click(function () {
		if($('#new_subject' ).val() && $('#new_url').val()) { 
			$('#submit_type' ).val('INSERT');
			$('#related_links_form').submit();
		} else {
			alert( 'Input data..' );
		}
	});
});
	
function isSelected () {
	var is_checked = false;
	
	$('input.checkitem').each(function() {
		if($(this).is(':checked'))
			is_checked=true;
	});
	return is_checked;
}
