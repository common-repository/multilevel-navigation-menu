jQuery( document ).ready( function( $ ) {
	$( '.mnmwp-menu-color' ).wpColorPicker();

	$.fn.swichColorButton = function( thisObj ) { 
		if( thisObj.is( ':checked' ) ) {
			thisObj.parents().siblings().children().children( 'button' ).removeAttr( 'disabled' );
		} else {
			thisObj.parents().siblings().children().children( 'button' ).attr( 'disabled', 'disabled' );
		}
	}
} );
