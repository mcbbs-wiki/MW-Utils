/* eslint-disable compat/compat */
// /////////////////////////////////////////////////////////////
// 作者：Salt_lovely
// 许可证：AGPL v3
// typescript 4.2
// /////////////////////////////////////////////////////////////
// 借物表：
// 快速Levenshtein距离算法根据如下思路编写：
// - https://www.codeproject.com/Articles/13525/Fast-memory-efficient-Levenshtein-algorithm-2
// - 2012年3月26日
// - 作者：Sten Hjelmqvist
// - 许可证：CPOL
// JSDiff快速字符串匹配：
// - https://github.com/kpdecker/jsdiff
// - 作者：kpdecker
// - 许可证：BSD
// /////////////////////////////////////////////////////////////
'use strict';
( function () {
	const diff = require( 'diff' );
	function main() {
		const elements = Array.from( document.querySelectorAll( '.salt-textDiffTool:not(.done)' ) );
		for ( const div of elements ) {
			div.classList.add( 'done' );
			const subtitle = div.querySelector( '.subtitle' );
			const msg = div.querySelector( '.message' );
			const input1 = div.querySelector( '.origin' );
			const input2 = div.querySelector( '.edited' );
			const btn = div.querySelector( 'button' );
			const result = div.querySelector( '.result' );
			const changeEventLevenshteinDistance = function () {
				const str1 = input1.value, str2 = input2.value,
					LevenshteinDistance = fastLevenshteinDistance( str1, str2 );
				subtitle.textContent = 'Levenshtein距离: ' + LevenshteinDistance +
                    ' 字符, 占比: ' + round( LevenshteinDistance / str1.length * 100, 2 ) + '%(对比原文), ' +
                    round( LevenshteinDistance / str2.length * 100, 2 ) + '%(对比后文), 快速Levenshtein距离算法, 可能有误';
			};
			const changeEventDiffColor = function () {
				return new Promise( function () {
					const str1 = input1.value, str2 = input2.value;
					if ( input1.value.length > 1024 || input2.value.length > 1024 ) {
						result.textContent = mw.msg( 'salttoolbox-textdiff-processing' );
					}
					const res = textDiffColor( str1, str2 );
					result.textContent = '';
					result.appendChild( res );
				} );
			};
			const trigger = function () {
				msg.textContent = '';
				if ( input1.value.length < 8192 && input2.value.length < 8192 ) {
					changeEventLevenshteinDistance();
				} else {
					msg.textContent += mw.msg( 'salttoolbox-textdiff-stoplevenshtein' ) + '\n';
				}
				if ( input1.value.length < 1024 && input2.value.length < 1024 ) {
					changeEventDiffColor();
				} else {
					msg.textContent += mw.msg( 'salttoolbox-textdiff-stoppalette' ) + '\n';
				}
			};
			input1.oninput = trigger;
			input2.oninput = trigger;
			btn.onclick = function () {
				msg.textContent = mw.msg( 'salttoolbox-textdiff-processing' );
				input1.disabled = true;
				input2.disabled = true;
				changeEventLevenshteinDistance();
				changeEventDiffColor();
				input1.disabled = false;
				input2.disabled = false;
				msg.textContent = mw.msg( 'salttoolbox-textdiff-done' );
			};
			result.ondblclick = function () {
				if ( result.classList.contains( 'anticopy' ) ) {
					result.classList.remove( 'anticopy' );
				} else {
					result.classList.add( 'anticopy' );
				}
			};
		}
		mw.track( 'bbswiki.salttool', 'textdiff' );
	}
	function fastLevenshteinDistance( str1 = '', str2 = '' ) {
		let res;
		if ( typeof str1 !== 'string' || typeof str2 !== 'string' ) {
			return 0;
		}
		str1 = str1.toLocaleLowerCase().trim();
		str2 = str2.toLocaleLowerCase().trim();
		let n = str1.length, m = str2.length;
		if ( n > m ) {
			const temp = str1;
			str1 = str2;
			str2 = temp;
			n = str1.length;
			m = str2.length;
		}
		if ( m === 0 ) {
			return n;
		} else if ( n === 0 ) {
			return m;
		}
		let v0 = [];
		const v1 = new Array( n + 1 );
		let cost;
		for ( let i = 0; i <= m; i++ ) {
			v0[ i ] = i;
		}
		for ( let i = 0; i < n; i++ ) {
			if ( i > 0 ) {
				v0 = v1.slice( 0 );
			}
			v1[ 0 ] = i + 1;
			for ( let j = 0; j < m; j++ ) {
				if ( str1[ i ] === str2[ j ] ) {
					cost = 0;
				} else {
					cost = 1;
				}
				v1[ j + 1 ] = Math.min( v1[ j ] + 1, v0[ j + 1 ] + 1, v0[ j ] + cost );
			}
		}
		return ( res = v1.pop() ) !== null && res !== undefined ? res : 0;
	}
	function textDiffColor( str1 = '', str2 = '' ) {
		const res = diff.diffChars( str1, str2 );
		const frag = document.createDocumentFragment();
		for ( let i = 0; i < res.length; i++ ) {
			const text = document.createElement( 'span' );
			if ( res[ i ].added && res[ i + 1 ] && res[ i + 1 ].removed ) {
				const temp = res[ i ];
				res[ i ] = res[ i + 1 ];
				res[ i + 1 ] = temp;
			}
			text.textContent = res[ i ].value;
			if ( res[ i ].removed ) {
				text.className = 'delete';
			} else if ( res[ i ].added ) {
				text.className = 'insert';
			} else {
				text.className = 'normal';
			}
			frag.appendChild( text );
		}
		return frag;
	}
	function round( n, p = 0 ) {
		const e = Math.pow( 10, p );
		return Math.round.call( null, n * e ) / e;
	}
	$( main );
}() );
