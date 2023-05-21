'use strict';
// @author Salt
// @license CC BY-NC-SA
( function () {
	const ore = {
		chance: [
			100, 100, 100, 100, 100,
			55, 55, 55, 55, 55, 55, 55, 55,
			10, 10, 10, 10, 10,
			3, 1, 3, 1, 2
		],
		reward: [
			'nugget50.gif|common', 'diamond1.gif|common', 'diamond2.gif|common',
			'unlock3.gif|common', 'notification2.gif|common',
			'nugget100.gif|rare', 'diamond4.gif|rare', 'serverbump2.gif|rare',
			'bump4.gif|rare', 'highlight.gif|rare', 'unlock5.gif|rare',
			'rename.gif|rare', 'anonymouspost2.gif|rare',
			'nugget500.gif|epic', 'diamond8.gif|epic', '20off.gif|epic',
			'bump8.gif|epic', 'serverbump4.gif|epic',
			'nugget999.gif|legend', 'emerald1.gif|legend', 'diamond20.gif|legend',
			'40off.gif|legend', 'piglin.gif|legend'
		]
	};
	function mineSim() {
		const elems = document.querySelectorAll( '.salt-miner-simulator:not([done])' );
		if ( elems.length < 1 ) {
			return;
		}
		for ( let i = 0; i < elems.length; i++ ) {
			mineSimulator( elems[ i ] );
		}
	}
	function mineSimulator( el ) {
		const simbtn = el.querySelector( '.s1' );
		const s10btn = el.querySelector( '.s10' );
		const resul = el.querySelector( '.resul' );
		let res = [];
		simbtn.addEventListener( 'click', () => {
			res = [ ore.reward[ randReward( ore.chance ) || 0 ] ];
			showOre();
		} );
		s10btn.addEventListener( 'click', () => {
			res = [];
			for ( let i = 0; i < 9; i++ ) {
				res.push( ore.reward[ randReward( ore.chance ) || 0 ] );
			}
			if ( checkOre() ) { // 保底机制
				res.push( ore.reward[ randReward( ore.chance ) || 0 ] );
			} else {
				res.push( ore.reward[ randReward( ore.chance.slice( 13 ) ) + 13 || 13 ] );
			}
			showOre();
		} );
		function getMsgname( name ) {
			return name.slice( 0, Math.max( 0, name.lastIndexOf( '.' ) ) );
		}
		function checkOre() {
			for ( const r of res ) {
				if ( r.indexOf( '|epic' ) > 0 || r.indexOf( '|legend' ) > 0 ) {
					return true;
				}
			}
			return false;
		}
		function showOre() {
			resul.innerHTML = '';
			for ( let i = 0; i < res.length; i++ ) {
				const x = res[ i ].split( '|' );
				const src = mw.config.get( 'wgExtensionAssetsPath' ) + '/MCBBSWikiUtils/resources/imgs/' + x[ 0 ];
				// eslint-disable-next-line mediawiki/msg-doc
				const alt = mw.msg( 'salttoolbox-miner-reward-' + getMsgname( x[ 0 ] ) );

				const li = document.createElement( 'li' ), img = document.createElement( 'img' ), span = document.createElement( 'span' );
				img.src = src;
				img.alt = alt;
				li.appendChild( img );
				span.textContent = alt;
				li.appendChild( span );
				// eslint-disable-next-line mediawiki/class-doc
				li.classList.add( x[ 1 ] );
				resul.appendChild( li );
			}
		}
		el.setAttribute( 'done', '' );
	}
	function randReward( arr ) {
		let leng = 1;
		for ( let i = 0; i < arr.length; i++ ) {
			leng += arr[ i ]; // 获取总数
		}
		for ( let i = 0; i < arr.length; i++ ) {
			const random = Math.floor( Math.random() * leng ); // 获取 0-总数 之间的一个随随机整数
			if ( random < arr[ i ] ) {
				return i; // 如果在当前的概率范围内,得到的就是当前概率
			} else {
				leng -= arr[ i ]; // 否则减去当前的概率范围,进入下一轮循环
			}
		}
	}
	$( mineSim );
}() );
