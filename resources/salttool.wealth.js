'use strict';
// @author Salt
// @license CC BY-NC-SA
( function () {
	function main() {
		wealthSim();
	}
	function wealthSim() {
		const elems = document.querySelectorAll( '.salt-acquire-wealth-simulator:not([done])' );
		if ( elems.length < 1 ) {
			return;
		}
		for ( let i = 0; i < elems.length; i++ ) {
			wealthSimulator( elems[ i ] );
		}
	}
	function wealthSimulator( el ) {
		const simbtn = document.createElement( 'div' );
		simbtn.textContent = '模拟致富';
		simbtn.classList.add( 'sim' );
		const clsbtn = document.createElement( 'div' );
		clsbtn.textContent = '清空';
		clsbtn.classList.add( 'cls' );
		const resshow = document.createElement( 'div' );
		resshow.textContent = '统计数据:';
		resshow.classList.add( 'resshow' );
		const simipt = document.createElement( 'input' );
		simipt.placeholder = '请输入模拟次数，最低为1';
		simipt.classList.add( 'input' );
		const resul = document.createElement( 'ul' );
		resul.classList.add( 'resul' );
		const simres = new wsr( resul );
		let count = 1;
		simipt.addEventListener( 'change', () => {
			const s = simipt.value;
			if ( s.length < 1 ) {
				count = 1;
				return;
			}
			count = parseInt( s );
			if ( isNaN( count ) || count < 1 ) {
				count = 1;
			}
		} );
		simbtn.addEventListener( 'click', () => {
			simres.sim( count );
			resshow.textContent = `统计数据:
致富卡: ${simres.res.length}张
花费金粒: ${simres.res.length * 500}粒
获得金粒: ${simres.totalRes}粒
总计盈亏: ${simres.totalWin}粒
获利比率: ${Math.round( simres.totalWinChance * 10000 ) / 100}%
平局比率: ${Math.round( simres.totalDrawChance * 10000 ) / 100}%
损失比率: ${Math.round( simres.totalLoseChance * 10000 ) / 100}%
`;
		} );
		clsbtn.addEventListener( 'click', () => {
			simres.clear();
			resshow.textContent = '统计数据:';
		} );
		el.innerHTML = '';
		el.appendChild( resul );
		el.appendChild( simipt );
		el.appendChild( simbtn );
		el.appendChild( clsbtn );
		el.appendChild( resshow );
		el.setAttribute( 'done', '' );
	}

	function randInt( max = 750, min = 1 ) {
		if ( min > max ) {
			const temp = max;
			max = min;
			min = temp;
		}
		return Math.floor( Math.random() * ( max - min + 1 ) + min );
	}
	class wsr {
		constructor( el, winStandard = 500, bottom = 1, top = 750 ) {
			this.res = []; // 存放结果
			this.totalRes = 0;
			this.totalWin = 0;
			this.totalWinChance = 0;
			this.totalDrawChance = 0;
			this.totalLoseChance = 0;
			this.bindEl = el;
			this.winStandard = winStandard;
			this.range = [ bottom, top ];

			this.safeRange = Math.floor(
				// eslint-disable-next-line compat/compat
				Number.MAX_SAFE_INTEGER /
                Math.max( ( bottom + top / 2 ), winStandard )
			) - 1;
		}
		cls() {
			this.clear();
		}
		clear() {
			this.res = [];
			this.totalRes = 0;
			this.totalWin = 0;
			this.totalWinChance = 0;
			this.totalDrawChance = 0;
			this.totalLoseChance = 0;
			this.resultShow();
		}
		resultCalc() {
			if ( this.res.length === 0 ) {
				this.cls();
			}
			let r = 0, w = 0, wc = 0, dc = 0;
			const len = this.res.length;
			for ( let i = 0; i < len; i++ ) {
				const x = this.res[ i ];
				r += x.res;
				w += x.win;
				if ( x.win > 0 ) { // 获利次数
					wc += 1;
				} else if ( x.win === 0 ) { // 平局次数
					dc += 1;
				}
			}
			this.totalRes = r;
			this.totalWin = w;
			this.totalWinChance = wc / len;
			this.totalDrawChance = dc / len;
			this.totalLoseChance = 1 - wc / len - dc / len;
		}
		resultShow() {
			if ( this.res.length === 0 ) {
				this.bindEl.innerHTML = '';
			}
			const len = this.res.length, childlen = this.bindEl.children.length;
			let html;
			for ( let i = childlen; i < len; i++ ) {
				const x = this.res[ i ];
				html = document.createElement( 'li' );
				html.textContent = `模拟结果: ${x.res}; 盈亏: ${x.win}`;
				this.bindEl.appendChild( html );
			}
		}
		sim( times = 1 ) {
			if ( times < 1 ) {
				return;
			}
			if ( times > 65536 ) {
				times = 65536;
			}
			let temp;
			for ( let i = 0; i < times; i++ ) {
				temp = randInt( this.range[ 1 ], this.range[ 0 ] );
				this.res.push( { res: temp, win: temp - this.winStandard } );
				if ( this.res.length >= this.safeRange ) {
					break;
				} // 安全区间控制
			}
			this.resultCalc();
			this.resultShow();
		}
	}
	main();
}() );
