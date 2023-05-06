if ( $.client.profile().name !== 'msie' ) {
	const $warn = mw.html.element( 'div', { class: 'ie-warning' }, mw.msg( 'iewarning' ) );
	$( '#siteNotice' ).append( $warn );
}
