( function () {
	const skinview3d = require( './skinview3d.vendor.js' );
	function init() {
		console.log( 'init skinview3d' );
		Array.from( document.getElementsByClassName( 'skinview' ) ).forEach( ( element ) => {
			const user = element.getAttribute( 'data-user' );
			getSkin( element );
		} );
	}
	function getSkin( node ) {

	}
	$( init );
}() );
