/* eslint-disable no-bitwise */
( function () {
	const answer = {
		veryBad: [
			'对比度小于 3:1 , 难以阅读, 反对使用这种配色, 建议立即更改',
			'文字ABCD 请勿使用于正文部分 ✗',
			'文字AB 请勿使用于大号字体 ✗'
		],
		bad: [
			'对比度大于 3:1 但是小于 4.5:1 , 不适合阅读, 不推荐在任何情况下使用',
			'文字ABCD 请勿使用于正文 ✗',
			'文字AB 不推荐使用于大号字体 ✗'
		],
		pass: [
			'(AA级) 对比度大于 4.5:1 但是小于 7:1 , 较为适合阅读, 但仅建议用于大号字体部分',
			'文字ABCD 不推荐使用于正文 ✗',
			'文字AB 可以使用于大号字体 ✓'
		],
		good: [
			'(AAA级) 对比度大于 7:1 , 适合阅读, 此配色可以用于正文部分',
			'文字ABCD 可以使用于正文 ✓',
			'文字AB 可以使用于大号字体 ✓'
		]
	};
	/** rgb(255,255,255) 格式 */
	const RGBExpr = /rgba?\((\d+)[,;](\d+)[,;](\d+)[,;]?\d*\)/i;
	/** #ffffff 格式 */
	const HexExpr = /#([0-9a-f]{1,6})/i;
	/** #fff 格式 */
	const Hex3Expr = /#([0-9a-f]{3,4})([^0-9a-f]|$)/i;
	/**
	 * 将文字转为RGB8bit对象
	 *
	 * @param expr
	 */
	const calcColor = function ( expr ) {
		// 预处理
		expr = expr.replace( /\s+/g, '' );
		if ( Hex3Expr.test( expr ) ) {
			// #fff 格式
			const hex = +( '0x' + expr.match( Hex3Expr )[ 1 ].slice( 0, 3 ) );
			return {
				R8bit: ( ( hex >> 8 ) % 16 ) * 17,
				G8bit: ( ( hex >> 4 ) % 16 ) * 17,
				B8bit: ( hex % 16 ) * 17
			};
		} else if ( HexExpr.test( expr ) ) {
			// #ffffff 格式
			const hex = +( '0x' + expr.match( HexExpr )[ 1 ] );
			return {
				R8bit: ( hex >> 16 ) % 256,
				G8bit: ( hex >> 8 ) % 256,
				B8bit: hex % 256
			};
		} else if ( RGBExpr.test( expr ) ) {
			// rgb(255,255,255) 格式
			const rgbColor = expr
					.match( RGBExpr )
					.splice( 1, 3 )
					.map( function ( v ) { return +v; } ),
				sR = rgbColor[ 0 ],
				sG = rgbColor[ 1 ],
				sB = rgbColor[ 2 ];
			return {
				R8bit: numFormat( sR ),
				G8bit: numFormat( sG ),
				B8bit: numFormat( sB )
			};
		}
		return {
			R8bit: 0,
			G8bit: 0,
			B8bit: 0
		};
		function numFormat( num ) {
			if ( typeof num !== 'number' || isNaN( num ) || num < 0 ) {
				return 0;
			} else if ( num > 255 ) {
				return 255;
			}
			return num;
		}
	};
	/**
	 * 用于计算相对明度
	 *
	 * @param c
	 */
	const calcsRGB = function ( c ) {
		if ( c < 0 || typeof c !== 'number' || isNaN( c ) ) {
			return 0;
		} else if ( c > 255 ) {
			return 1;
		}
		const temp = c / 255;
		if ( c <= 0.03928 ) {
			return temp / 12.92;
		} else {
			return Math.pow( ( ( temp + 0.055 ) / 1.055 ), 2.4 );
		}
	};
	/**
	 * 计算相对明度
	 *
	 * @param color 颜色
	 * @return 明度
	 */
	const relativeLuminance = function ( color ) {
		if ( typeof color !== 'object' ) {
			return 0;
		}
		const R = calcsRGB( color.R8bit ), G = calcsRGB( color.G8bit ), B = calcsRGB( color.B8bit );
		return 0.2126 * R + 0.7152 * G + 0.0722 * B;
	};
	/**
	 * 返回两颜色的对比度
	 *
	 * @param l1 较亮的颜色的明度
	 * @param l2 较暗的颜色的明度
	 * @return 对比度
	 */
	const contrastRatio = function ( l1, l2 ) {
		if ( typeof l1 !== 'number' || isNaN( l1 ) || !isFinite( l1 ) || l1 < 0 ) {
			l1 = 0;
		}
		if ( typeof l2 !== 'number' || isNaN( l2 ) || !isFinite( l2 ) || l2 < 0 ) {
			l2 = 0;
		}
		if ( l2 > l1 ) {
			const temp = l1;
			l1 = l2;
			l2 = temp;
		}
		return ( l1 + 0.05 ) / ( l2 + 0.05 );
	};
	/**
	 * 封装完毕，输入两个字符串格式的颜色代码
	 *
	 * 返回数字格式的对比度比值
	 *
	 * @param color1
	 * @param color2
	 */
	const calcContrast = function ( color1, color2 ) {
		return contrastRatio(
			relativeLuminance( calcColor( color1 ) ),
			relativeLuminance( calcColor( color2 ) )
		);
	};
	const formatRGB = function ( rgb ) {
		return '#' + hex( rgb.R8bit ) + hex( rgb.G8bit ) + hex( rgb.B8bit );
		function hex( c ) {
			let h = c.toString( 16 );
			if ( h.length < 2 ) {
				h = '0' + h;
			}
			return h;
		}
	};
	// 主过程
	function main() {
		const div = document.getElementById( 'saltContrastCalculator' );
		if ( !div ) {
			return;
		}
		// 添加两个输入框和其对应的预览
		// 文字色
		const color1Input = div.querySelector( '.color.left input' );
		const color2Input = div.querySelector( '.color.right input' );
		const color1Show = div.querySelector( '.color.left .color-show' );
		const color2Show = div.querySelector( '.color.right .color-show' );
		const res = div.querySelector( '.res' );
		const resShow = div.querySelector( '.res-show' );
		const resShowBig = div.querySelector( '.res-show.big' );
		const resText = div.querySelector( '.res-text' );
		const renderColor = function () {
			const v1 = color1Input.value, v2 = color2Input.value;
			if ( v1 && v2 ) {
				const temp = Math.round( calcContrast( v1, v2 ) * 100 ) / 100, text = '\u5BF9\u6BD4\u5EA6: ' + temp + ', \u8BC4\u4EF7: ';
				const tc = formatRGB( calcColor( v1 ) ), bc = formatRGB( calcColor( v2 ) );
				let evaluate;
				res.style.setProperty( '--color', tc );
				res.style.setProperty( '--back-color', bc );
				resShow.title = '\u6587\u5B57\u989C\u8272: ' + tc + '; \u80CC\u666F\u989C\u8272: ' + bc;
				resShowBig.title = '\u6587\u5B57\u989C\u8272: ' + tc + '; \u80CC\u666F\u989C\u8272: ' + bc;
				if ( temp < 3 ) {
					evaluate = answer.veryBad;
				} else if ( temp < 4.5 ) {
					evaluate = answer.bad;
				} else if ( temp < 7 ) {
					evaluate = answer.pass;
				} else {
					evaluate = answer.good;
				}
				resText.textContent = text + evaluate[ 0 ];
				resShow.textContent = evaluate[ 1 ];
				resShowBig.textContent = evaluate[ 2 ];
			}
		};
		const renderColor1 = function () {
			const v = color1Input.value;
			if ( v && v.length > 1 ) {
				const temp = formatRGB( calcColor( v ) );
				color1Show.style.backgroundColor = temp;
				color1Show.title = '颜色: ' + temp;
				renderColor();
			}
		};
		const renderColor2 = function () {
			const v = color2Input.value;
			if ( v && v.length > 1 ) {
				const temp = formatRGB( calcColor( v ) );
				color2Show.style.backgroundColor = temp;
				color2Show.title = '颜色: ' + temp;
				renderColor();
			}
		};
		color1Input.addEventListener( 'change', renderColor1 );
		color2Input.addEventListener( 'change', renderColor2 );
	}
	main();
}() );
