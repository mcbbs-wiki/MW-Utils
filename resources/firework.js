/**
 * 搬运须知: 您必须在**显眼处**标识来源“MCBBS Wiki”与作者“Salt_lovely”, **不**接受任何形式的简称或不署名。
 * Notice: You have to mark origin "MCBBS Wiki" and author "Salt_lovely" in CONSPICUOS PLACE, abbreviation or omissions are NOT allowed.
 * 许可证: CC BY-NC-SA 4.0
 * License: CC BY-NC-SA 4.0
 * 灵感来源: https://codepen.io/jackrugile/pen/acAgx 作者 Jack Rugile
 * Inspired By: https://codepen.io/jackrugile/pen/acAgx Author Jack Rugile
 */
'use strict';
( () => {
	function randomChoice( arr ) {
		if ( arr.length < 1 ) {
			return null;
		}
		return arr[ Math.floor( Math.random() * arr.length ) ];
	}

	// widget/SaltFirework/widget.ts
	if ( document.getElementById( 'saltFireWorkCanvas' ) ) {
		throw new Error( '同一页面中只能有一个烟花' );
	}
	// ! 颜色范围
	let hueRange;
	// ! 颜色变化区间
	let hueDiff;
	// ! 粒子效果数量
	let count;
	const baseRange = [ 1, 4 ];
	const baseSpeed = [ 0.3, 2, 3 ];
	const fallSpeed = 1.1 / 60;
	const fadeSpeed = 0.65;
	const tail = 15;
	const canvas = document.createElement( 'canvas' );
	const context = canvas.getContext( '2d' );
	let particles = [];
	let lastLength = 0;
	let zeroFrame = 0;
	$( init );
	function init() {
		hueRange = mw.config.get( 'wgFireworkHueRange' );
		hueDiff = mw.config.get( 'wgFireworkHueDiff' );
		count = mw.config.get( 'wgFireworkCount' );
		canvas.id = 'saltFireWorkCanvas';
		canvas.style.left = '0';
		canvas.style.top = '0';
		canvas.style.position = 'fixed';
		canvas.style.pointerEvents = 'none';
		canvas.style.zIndex = '99999';
		document.body.appendChild( canvas );
		resizeCanvas();
		window.addEventListener( 'resize', resizeCanvas, false );
		tick();
		document.addEventListener( 'mousedown', function ( e ) {
			createFireworks( e.clientX, e.clientY );
		} );
		mw.track( 'bbswiki.salttool', 'firework' );
	}
	function resizeCanvas() {
		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;
	}
	function rightRandom( base, size ) {
		return base + ( Math.random() * size - Math.random() * size ) / 2;
	}
	function createFireworks( x, y ) {
		const hue = randomChoice( hueRange );
		for ( let i = 0; i < count; i++ ) {
			const spd = rightRandom( ( baseSpeed[ 1 ] + baseSpeed[ 0 ] ) / 2, baseSpeed[ 1 ] - baseSpeed[ 0 ] );
			const rad = Math.random() * 2 * Math.PI;
			particles.push( {
				x,
				y,
				spdX: Math.cos( rad ) * spd,
				spdY: Math.sin( rad ) * spd,
				spdFall: baseSpeed[ 2 ],
				size: rightRandom( ( baseRange[ 1 ] + baseRange[ 0 ] ) / 2, baseRange[ 1 ] - baseRange[ 0 ] ),
				hue: hueRandom(),
				bright: rightRandom( 72, 16 ),
				alpha: rightRandom( 75, 30 )
			} );
		}
		function hueRandom() {
			let h = Math.floor( rightRandom( hue, hueDiff ) );
			if ( h > 360 ) {
				h -= 360;
			} else if ( h < 0 ) {
				h += 360;
			}
			return h;
		}
	}
	function drawParticles() {
		if ( !particles.length ) {
			return;
		}
		context.globalCompositeOperation = 'lighter';
		for ( let i = 0; i < particles.length; i++ ) {
			const p = particles[ i ];
			if ( !p ) {
				continue;
			}
			p.x += p.spdX * p.spdFall;
			p.y += p.spdY * p.spdFall;
			p.spdY += fallSpeed;
			p.spdFall *= 0.978;
			p.alpha -= fadeSpeed;
			context.beginPath();
			context.arc( p.x, p.y, p.size, 0, Math.PI * 2, false );
			context.closePath();
			context.fillStyle = `hsla(${p.hue},100%,${p.bright}%,${p.alpha / 100})`;
			context.fill();
			// ! 标记已经透明到看不见的粒子
			if ( p.alpha < fadeSpeed ) {
				particles[ i ] = null;
			}
		}
		if ( lastLength === 0 && particles.length === 0 ) {
			zeroFrame += 1;
			if ( zeroFrame === 30 ) {
				canvas.height = window.innerHeight;
			}
		} else {
			zeroFrame = 0;
		}
		lastLength = particles.length;
	}
	function drawTail() {
		if ( zeroFrame >= 30 ) {
			return;
		}
		// ! 保留前一刻的图案作为尾迹
		context.globalCompositeOperation = 'destination-out';
		context.fillStyle = `rgba(0,0,0,${1 / tail})`;
		context.fillRect( 0, 0, canvas.width, canvas.height );
	}
	function clearParticles() {
		if ( !particles.length ) {
			return;
		}
		const cp = [];
		for ( const p of particles ) {
			if ( p ) {
				cp.push( p );
			}
		}
		if ( cp.length !== particles.length ) {
			particles = cp;
		}
	}
	function tick() {
		// ! 画尾迹 -> 画这一帧的粒子 -> 删除运算完毕的粒子
		drawTail();
		drawParticles();
		clearParticles();
		requestAnimationFrame( tick );
	}
} )();
