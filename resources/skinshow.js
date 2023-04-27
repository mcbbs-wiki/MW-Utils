( function () {
	const skinview3d = require( './skinview3d.vendor.js' );
	function init() {
		console.log( 'init skinview3d' );
		$( '.skinview' ).each( ( i, node ) => {
			let skinimgurl;
			const $skinsrcnode = $( node );
			let $skinimg = $skinsrcnode.find( '.image img' );
			if ( $skinimg.length === 0 ) {
				$skinimg = $skinsrcnode.find( '.external' );
				skinimgurl = $skinimg.attr('href')
				$skinimg.remove()
			} else {
				skinimgurl = $skinimg.attr('src')
				$skinimg.remove()
			}
			setSkin( node, skinimgurl );
		} );
	}
	function setSkin( node, url ) {
		console.log(node,url)
		const canvas = document.createElement("canvas");
		const option = {
		  canvas,
		  width: node.offsetWidth,
		  height: node.offsetHeight,
		  skin: url,
		  fov: 50,
		  zoom: 0.9
		};
		const viewer = new skinview3d.SkinViewer(option);
		viewer.globalLight.intensity = 0.5;
		viewer.cameraLight.intensity = 0.5;
		node.appendChild(canvas);
	}
	$( init );
}() );
