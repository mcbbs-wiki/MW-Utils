// eslint-disable-next-line compat/compat
Array.from( document.querySelectorAll( 'span.custom-audio-click' ) ).forEach( function ( element ) {
	const audio = element.getElementsByTagName( 'audio' )[ 0 ];
	element.addEventListener( 'click', () => {
		audio.play();
	} );
} );
