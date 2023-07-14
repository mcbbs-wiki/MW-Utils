/* eslint-disable compat/compat */
( () => {
	function h( name, attrs = [], contents = '' ) {
		return $.parseHTML( mw.html.element( name, attrs, contents ) )[ 0 ];
	}
	function handleChildren( props ) {
		const { queryContainer, queryElement, callback } = props;
		const containers = Array.from( document.body.querySelectorAll( queryContainer ) );
		containers.forEach( ( container ) => {
			container.classList.add( 'salt-done' );
			const elems = Array.from( container.querySelectorAll( queryElement ) )
				.filter( ( el ) => el instanceof HTMLElement );
			const res = elems.map( ( elem ) => callback( elem ) );
			container.innerHTML = '';
			res.forEach( ( item ) => container.appendChild( item ) );
		} );
	}

	function mouseMoveDetailHandler( ev ) {
		const { offsetWidth, offsetHeight } = this;
		const { offsetX, offsetY } = ev;
		const posX = offsetX / offsetWidth * 2 - 1;
		const posY = offsetY / offsetHeight * 2 - 1;
		const bright = 1 - posY * 0.18 - posX * 0.02;
		const rotateX = 15 * posX;
		const rotateY = 15 * posY;
		this.style.transform = `rotateX(${-rotateY}deg) rotateY(${rotateX}deg)`;
		this.style.filter = `brightness(${bright})`;
	}
	function mouseLeaveDetailHandler() {
		this.style.transform = 'rotateX(0deg) rotateY(0deg)';
		this.style.filter = 'brightness(1)';
	}

	if ( window.parent !== window ) {
		return;
	} else {
		$( () => {
			handleChildren( {
				queryContainer: '.salt-card-effect-detail:not(.salt-done)',
				queryElement: 'img',
				callback: ( img ) => {
					img.classList.add( 'salt-card-img' );
					const container = h( 'div', {
						class: 'salt-card-container salt-card-container-detail'
					} );
					const layer = h( 'div', { class: 'salt-card-layer' } );
					container.appendChild( layer );
					layer.appendChild( img );
					img.addEventListener( 'mousemove', mouseMoveDetailHandler );
					container.addEventListener( 'mouseleave', mouseLeaveDetailHandler.bind( img ) );
					return container;
				}
			} );
			handleChildren( {
				queryContainer: '.salt-card-effect-overview:not(.salt-done)',
				queryElement: 'img',
				callback: ( img ) => {
					img.classList.add( 'salt-card-img' );
					const container = h( 'div', {
						class: 'salt-card-container salt-card-container-overview'
					} );
					const layer = h( 'div', { class: 'salt-card-layer' } );
					container.appendChild( layer );
					layer.appendChild( img );
					return container;
				}
			} );
		} );
	}
} )();
