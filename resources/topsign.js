( function () {
	function topsign( ul, height ) {
		const $pFirst = ul.find( 'p' ).first();
		ul.animate( { top: height } ).animate( { top: 0 }, 0, function () {
			const $clone = $pFirst.clone();
			ul.append( $clone );
			$pFirst.remove();
		} );
	}
	const $ul = $( '.topsign .topsignitem' );
	const $par = $ul.parent();
	const hei = $par.attr( 'data-height' );
	const delay = parseInt( $par.attr( 'data-delay' ) );
	setInterval( () => {
		topsign( $ul, hei );
	}, delay );
}() );
