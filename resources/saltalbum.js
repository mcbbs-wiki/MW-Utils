/* eslint-disable no-void */
/* eslint-disable no-underscore-dangle */
'use strict';
// 仓库地址：https://github.com/mcbbs-wiki
/** 放在自执行函数中以防污染全局变量 */
( function () {
	/** 维护一个图片组 */
	const AlbumImgList = /** @class */ ( function () {
		function ImgList( p ) {
			this.index = 0;
			this.list = p;
		}
		/**
		 *获取当前/指定位置的图片
		 *
		 * @param index
		 */
		ImgList.prototype.get = function ( index ) {
			let _a;
			if ( index === void 0 ) {
				index = this.index;
			}
			if ( this.list.length === 0 ) {
				return { src: '', alt: '' };
			}
			index = this.fixIndex( index );
			// console.log(index);
			return ( _a = this.list[ index ] ) !== null && _a !== void 0 ? _a : { src: '', alt: '' };
		};
		/**
		 *获取当前/指定位置后一位置的图片
		 *
		 * @param index
		 */
		ImgList.prototype.next = function ( index ) {
			if ( index === void 0 ) {
				index = this.index;
			}
			return this.get( index + 1 );
		};
		/**
		 *获取当前/指定位置前一位置的图片
		 *
		 * @param index
		 */
		ImgList.prototype.prev = function ( index ) {
			if ( index === void 0 ) {
				index = this.index;
			}
			return this.get( index - 1 );
		};
		/**
		 *修正索引数字
		 *
		 * @param index
		 */
		ImgList.prototype.fixIndex = function ( index ) {
			if ( index === void 0 ) {
				index = this.index;
			}
			// console.log(index);
			if ( this.list.length === 0 ) {
				return 0;
			}
			while ( index > this.list.length - 1 ) {
				index -= this.list.length;
			}
			while ( index < 0 ) {
				index += this.list.length;
			}
			return index;
		};
		return ImgList;
	}() );
	const newDiv = function () {
		return document.createElement( 'div' );
	};
	const newImg = function () {
		return document.createElement( 'img' );
	};
	const createAlbum = function ( container ) {
		let index = 0;
		// 按钮
		/** 前一个 */
		const leftBtn = newDiv();
		/** 后一个 */
		const rightBtn = newDiv();
		// 三张图片
		/** 左图 */
		let imgL;
		/** 中图 */
		let imgC;
		/** 右图 */
		let imgR;
		// 获取数据并生成图片组对象
		/** 图片组对象 */
		const imgList = new AlbumImgList( getList() );
		// 初始化按钮和三张图片
		init();
		/** 将图片及其说明整合成`{ src: string; alt: string }[]` */
		function getList() {
			let _a, _b, _c, _d, _e;
			// 获取图片和显示文字
			const _list = [], elems = container.querySelectorAll( 'img, span.text' );
			let step = 0, _temp = { src: '', alt: '' };
			for ( let i = 0; i < elems.length; i++ ) {
				const el = elems[ i ];
				if ( !( el instanceof HTMLElement ) ) {
					continue;
				}
				if ( el instanceof HTMLImageElement ) {
					if ( step === 0 ) {
						_temp.src = ( _a = el.src ) !== null && _a !== void 0 ? _a : '';
						_temp.alt = ( _b = el.alt ) !== null && _b !== void 0 ? _b : '';
						step = 1;
					} else {
						// 没有找到文字说明直接到下一张图片
						_list.push( _temp );
						_temp = { src: ( _c = el.src ) !== null && _c !== void 0 ? _c : '', alt: ( _d = el.alt ) !== null && _d !== void 0 ? _d : '' };
						step = 1;
					}
				} else if ( el instanceof HTMLSpanElement && step === 1 ) {
					_temp.alt = ( _e = el.textContent ) !== null && _e !== void 0 ? _e : '';
					step = 0;
					// 图片ID和文字都收集齐了
					_list.push( _temp );
					_temp = { src: '', alt: '' };
				}
			}
			if ( step === 1 ) {
				_list.push( _temp );
			}
			return _list;
		}
		/** 初始化 */
		function init() {
			const frag = document.createDocumentFragment();
			// 初始化按钮
			leftBtn.textContent = '<';
			leftBtn.id = 'left-btn';
			leftBtn.onclick = function () {
				prevImg();
				index = imgList.fixIndex( index );
			};
			rightBtn.textContent = '>';
			rightBtn.onclick = function () {
				nextImg();
				index = imgList.fixIndex( index );
			};
			rightBtn.id = 'right-btn';
			// 清空容器，放入按钮
			container.innerHTML = '';
			frag.appendChild( leftBtn );
			frag.appendChild( rightBtn );
			container.style.display = 'block';
			// 初始化三张图片
			imgL = imgPack( imgList.prev() );
			imgC = imgPack( imgList.get() ); // 一开始都是0，所以懒得输入
			imgR = imgPack( imgList.next() );
			classManager();
			frag.appendChild( imgL );
			frag.appendChild( imgC );
			frag.appendChild( imgR );
			// 统一拼到容器上
			container.appendChild( frag );
		}
		/**
		 * 生成一个图片-文字组
		 *
		 * @param p
		 */
		function imgPack( p ) {
			// console.log(p);
			const img = newImg();
			img.src = p.src;
			img.alt = p.alt;
			const text = newDiv();
			text.textContent = p.alt;
			text.className = 'text';
			const c = newDiv();
			// c.className = 'img-pack';
			c.appendChild( img );
			c.appendChild( text );
			return c;
		}
		/** 统一的样式管理方法 */
		function classManager() {
			imgL.className = 'img-pack img-left';
			imgC.className = 'img-pack img-center';
			imgR.className = 'img-pack img-right';
		}
		/** 换图 */
		function nextImg() {
			index = imgList.fixIndex( ++index );
			const temp = imgL;
			imgL = imgC;
			imgC = imgR;
			imgR = imgPack( imgList.next( index ) );
			classManager();
			container.appendChild( imgR );
			temp.style.opacity = '0';
			setTimeout( function () {
				temp.remove();
			}, 400 );
		}
		function prevImg() {
			index = imgList.fixIndex( --index );
			const temp = imgR;
			imgR = imgC;
			imgC = imgL;
			imgL = imgPack( imgList.prev( index ) );
			classManager();
			container.appendChild( imgL );
			temp.style.opacity = '0';
			setTimeout( function () {
				temp.remove();
			}, 400 );
		}
	};
	function main() {
		const elems = document.body.querySelectorAll( '.salt-album:not(.done)' );
		if ( elems.length < 1 ) {
			return;
		}
		for ( let i = 0; i < elems.length; i++ ) {
			const el = elems[ i ];
			el.classList.add( 'done' );
			if ( el instanceof HTMLElement ) {
				createAlbum( el );
			}
		}
	}
	// 执行
	$( main );
}() );
