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
			'nugget50.gif|金粒*50|common', 'diamond1.gif|钻石*1|common', 'diamond2.gif|钻石*2|common',
			'unlock3.gif|挖掘卡*3|common', 'notification2.gif|召集卡*2|common',
			'nugget100.gif|金粒*100|rare', 'diamond4.gif|钻石*4|rare', 'serverbump2.gif|服务器提升卡*2|rare',
			'bump4.gif|提升卡*4|rare', 'highlight.gif|变色卡*1|rare', 'unlock5.gif|挖掘卡*5|rare',
			'rename.gif|改名卡*1|rare', 'anonymouspost2.gif|匿名卡*2|rare',
			'nugget500.gif|金粒*500|epic', 'diamond8.gif|钻石*8|epic', '20off.gif|-20%优惠券|epic',
			'bump8.gif|提升卡*8|epic', 'serverbump4.gif|服务器提升卡*4|epic',
			'nugget999.gif|金粒*999|legend', 'emerald1.gif|绿宝石*1|legend', 'diamond20.gif|钻石*20|legend',
			'40off.gif|-40%优惠券|legend', 'piglin.gif|猪灵勋章|legend'
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
				const src = mw.config.get( 'wgExtensionAssetsPath' ) + '/MCBBSWikiUtils/resources/imgs/' + x[ 0 ], alt = x[ 1 ];
				const li = document.createElement( 'li' ), img = document.createElement( 'img' ), span = document.createElement( 'span' );
				img.src = src;
				img.alt = alt;
				li.appendChild( img );
				span.textContent = alt;
				li.appendChild( span );
				// eslint-disable-next-line mediawiki/class-doc
				li.classList.add( x[ 2 ] );
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
