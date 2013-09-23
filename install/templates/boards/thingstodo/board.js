function updateBoardHook() {
	var maxHeight = 0;
	$('#board').children('.boardcolumn:not(#notes)').css('height', 'auto');
	$('#board').children('.boardcolumn:not(#notes)').each(function() {
		var height = $(this).outerHeight();
		if ( height > maxHeight ) {
			maxHeight = height;
		}
	});
	$('.boardcolumn:not(#notes)').css('height', maxHeight);
}