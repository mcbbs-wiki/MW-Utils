/* eslint-disable compat/compat */
( function () {
	const skinview3d = require( 'skinview3d' );
	function init() {
		Array.from( document.getElementsByClassName( 'skinview-lite' ) ).forEach( ( element ) => {
			// const user = element.getAttribute( 'data-user' );
			const skincanvas = element.getElementsByClassName( 'skinview-canvas' )[ 0 ];
			const skincontroller = element.getElementsByClassName( 'skinview-controller-lite' )[ 0 ];
			const url = element.dataset.src;
			const interval = setInterval( function () {
				if ( skincanvas.offsetWidth > 0 ) {
					initSkin( skincanvas, skincontroller, url );
					clearInterval( interval );
				}
			} );
		} );
	}
	function initSkin( skincanvas, skincontroller, url ) {
		mw.track( 'bbswiki.skinviewlite.get', url );
		const viewer = setSkin( skincanvas, url );
		setSkinController( skincontroller, viewer );
	}

	function setSkinController( node, viewer ) {
		const parent = node.parentElement;
		const popup = new OO.ui.PopupButtonWidget( {
			icon: 'info',
			label: mw.msg( 'skinview-help' ),
			framed: false,
			invisibleLabel: true,
			popup: {
				head: true,
				label: mw.message( 'skinview-help' ).text(),
				$content: $( '<p>' ).append( mw.message( 'skinview-help-content-lite' ).parseDom() ),
				padded: true,
				align: 'backwards',
				autoFlip: false
			}
		} );
		const pos = { x: 0, y: 0 };
		viewer.canvas.addEventListener( 'mousedown', ( ev ) => {
			pos.x = ev.x;
			pos.y = ev.y;
			ev.preventDefault();
		} );
		viewer.canvas.addEventListener( 'mouseup', ( ev ) => {
			if ( Math.abs( pos.x - ev.x ) > 2 || Math.abs( pos.y - ev.y ) > 2 ) {
				return;
			}
			if ( ev.button === 0 ) {
				if ( parent.dataset.speed === 'slow' ) {
					setViewerAction( viewer, 'walk', 1 );
					parent.dataset.speed = 'fast';
				} else if ( parent.dataset.speed === 'fast' ) {
					setViewerAction( viewer, 'idle', 1 );
					parent.dataset.speed = 'stop';
				} else if ( parent.dataset.speed === 'stop' ) {
					setViewerAction( viewer, 'walk', 0.5 );
					parent.dataset.speed = 'slow';
				}
			} else if ( ev.button === 2 ) {
				viewer.resetCameraPose();
			}
			ev.preventDefault();

		} );
		$( node ).append( popup.$element );
	}
	function setViewerAction( viewer, action, speed ) {
		if ( action === 'idle' ) {
			viewer.animation = viewer.mbwAnimation.idle;
			viewer.animation.speed = speed;
		} else if ( action === 'walk' ) {
			viewer.animation = viewer.mbwAnimation.walk;
			viewer.animation.speed = speed;
		}
	}
	function setSkin( node, url ) {
		const parent = node.parentElement;
		node.innerHTML = '';
		const canvas = document.createElement( 'canvas' );
		const option = {
			canvas,
			width: node.offsetWidth,
			height: node.offsetHeight,
			skin: url,
			fov: 50,
			zoom: 0.9
		};
		const viewer = new skinview3d.SkinViewer( option );
		viewer.mbwAnimation = {
			idle: new skinview3d.IdleAnimation(),
			walk: new skinview3d.WalkingAnimation()
		};
		viewer.globalLight.intensity = 0.5;
		viewer.cameraLight.intensity = 0.5;
		node.appendChild( canvas );
		if ( parent.dataset.speed === 'slow' ) {
			setViewerAction( viewer, 'walk', 0.5 );
		} else if ( parent.dataset.speed === 'fast' ) {
			setViewerAction( viewer, 'walk', 1 );
		} else if ( parent.dataset.speed === 'stop' ) {
			setViewerAction( viewer, 'idle', 1 );
		}
		parent.classList.remove( 'skinview-loading' );
		return viewer;
	}
	$( init );
}() );
