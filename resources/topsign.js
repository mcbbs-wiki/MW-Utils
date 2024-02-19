( () => {
	// widget/TopSign/widget.ts
	topsign( { query: '.topsign', interval: 6e3, animationTime: 300 } );
	function topsign( props ) {
		let index = 0;
		const { query, interval = 5e3, animationTime = 300 } = props;
		const initView = () => {
			const container = document.querySelector( query );
			if ( !( container instanceof HTMLElement ) ) {
				return;
			}
			if ( !container.classList.contains( 'topsign-ok' ) ) {
				container.classList.add( 'topsign-ok' );
			}
			const list = Array.from( container.children ).filter( ( el ) => el instanceof HTMLElement );
			if ( list.length < 2 ) {
				return;
			}
			const { offsetHeight: height } = container;
			const current = index;
			index++;
			if ( index >= list.length ) {
				index = 0;
			}
			const next = index;
			list.forEach( ( el, i ) => {
				el.style.lineHeight = `${height}px`;
				el.style.height = `${height}px`;
				el.style.transform = `translateY(${i === current ? 0 : height}px)`;
			} );
			return { list, height, current: list[ current ], next: list[ next ] };
		};
		const animation = ( props2 ) => {
			const { startTime, height, current, next } = props2;
			const now = Date.now();
			if ( now >= startTime + animationTime ) {
				current.style.transform = `translateY(${height}px)`;
				next.style.transform = 'translateY(0px)';
			} else {
				const percent = ( now - startTime ) / animationTime;
				current.style.transform = `translateY(${-height * percent}px)`;
				next.style.transform = `translateY(${height * ( 1 - percent )}px)`;
				requestAnimationFrame( () => animation( { startTime, height, current, next } ) );
			}
		};
		const setView = () => {
			const init = initView();
			if ( !init ) {
				return;
			}
			const { height, current, next } = init;
			requestAnimationFrame( () => animation( { startTime: Date.now(), height, current, next } ) );
			setTimeout(() => setView(), interval);
		};
		setTimeout( () => setView(), interval );
		initView();
		index = 0;
	}
} )();
