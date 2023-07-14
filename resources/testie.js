if ( $.client.profile().name === 'msie' ) {
	const $warntext = $.parseHTML( mw.message( 'iewarning' ).parse() );
	const $warn = $( '<div>' ).attr( 'class', 'ie-warning' );
	$warn.append( $warntext );
	$( '#firstHeading' ).append( $warn );
	mw.track( 'bbswiki.ieuser' );
}
