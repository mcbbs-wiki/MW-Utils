/* eslint-disable compat/compat */
( function () {
	const skinview3d = require( 'skinview3d' );
	function init() {
		Array.from( document.getElementsByClassName( 'skinview' ) ).forEach( ( element ) => {
			// const user = element.getAttribute( 'data-user' );
			const skincanvas = element.getElementsByClassName( 'skinview-canvas' )[ 0 ];
			const skincontroller = element.getElementsByClassName( 'skinview-controller' )[ 0 ];
			const url = getSkinURL( skincanvas );
			const viewer = setSkin( skincanvas, url );
			setSkinController( skincontroller, viewer );
		} );
	}
	function getSkinURL( node ) {
		const link = node.firstElementChild;
		if ( link.classList.contains( 'external' ) ) {
			return link.getAttribute( 'href' );
		} else if ( link.classList.contains( 'image' ) ) {
			return link.firstElementChild.getAttribute( 'src' );
		}
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
				label: mw.msg( 'skinview-help' ),
				$content: $( '<p>' ).append( mw.message( 'skinview-help-content' ).parseDom() ),
				padded: true,
				align: 'backwards',
				autoFlip: false
			}
		} );
		const pauseButton = new OO.ui.ButtonOptionWidget( { icon: 'pause', data: 'stop', title: mw.msg( 'skinview-pause' ) } );
		const slowButton = new OO.ui.ButtonOptionWidget( { icon: 'next', data: 'slow', title: mw.msg( 'skinview-slow' ) } );
		const fastButton = new OO.ui.ButtonOptionWidget( { icon: 'doubleChevronEnd', data: 'fast', title: mw.msg( 'skinview-fast' ) } );
		const speedSelect = new OO.ui.ButtonSelectWidget( {
			items: [ pauseButton, slowButton, fastButton ] }
		);
		const resetButton = new OO.ui.ButtonWidget( { icon: 'reload', title: mw.msg( 'skinview-reset' ) } );
		const controller = new OO.ui.HorizontalLayout( {
			items: [ speedSelect, resetButton, popup ] }
		);
		if ( parent.dataset.speed === 'slow' ) {
			speedSelect.selectItem( slowButton );
		} else if ( parent.dataset.speed === 'fast' ) {
			speedSelect.selectItem( fastButton );
		} else if ( parent.dataset.speed === 'stop' ) {
			speedSelect.selectItem( pauseButton );
		}
		resetButton.on( 'click', () => {
			viewer.resetCameraPose();
		} );
		speedSelect.on( 'choose', ( item ) => {
			parent.dataset.speed = item.data;
			if ( parent.dataset.speed === 'slow' ) {
				setViewerAction( viewer, 'walk', 0.5 );
			} else if ( parent.dataset.speed === 'fast' ) {
				setViewerAction( viewer, 'walk', 1 );
			} else if ( parent.dataset.speed === 'stop' ) {
				setViewerAction( viewer, 'idle', 1 );
			}
		} );
		$( node ).append( controller.$element );
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
			setViewerAction( viewer, 'walk', 1 );
		}
		parent.classList.remove( 'skinview-loading' );
		return viewer;
	}
	$( init );
}() );
