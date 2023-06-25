( function () {
	const UTCOffset = new Date().getTimezoneOffset() * 60 * 1e3;
	const cmdAttr = 'data-salt-time-diff-command';
	const startAttr = 'data-salt-time-diff-start';
	const endAttr = 'data-salt-time-diff-end';
	const i18n = {
		year: '年',
		month: '个月',
		day: '天',
		hour: '小时',
		minute: '分钟',
		second: '秒',
		ms: '毫秒'
	};
	function getDate( time, utc = false ) {
		if ( !time ) {
			return new Date();
		}
		if ( /^\d+$/.test( time.trim() ) ) {
			const d2 = new Date( +time );
			if ( !isNaN( d2.valueOf() ) ) {
				if ( utc ) {
					d2.setTime( d2.getTime() + UTCOffset );
				}
				return d2;
			} else { return new Date(); }
		}
		const d = new Date( time );
		if ( !isNaN( d.valueOf() ) ) {
			return d;
		} else {
			return new Date();
		}
	}
	function timeDiff( {
		t1,
		t2,
		cmd = 'd',
		cpx = false,
		simple = false
	} ) {
		// eslint-disable-next-line no-underscore-dangle
		const _ms = t1.valueOf() - t2.valueOf();
		const isBefore = t1.valueOf() - t2.valueOf() > 0;
		const d1 = new Date( isBefore ? t2 : t1 );
		const d2 = new Date( isBefore ? t1 : t2 );
		let ms = Math.abs( _ms );
		const diff = { isBefore };
		if ( cmd.indexOf( 'y' ) !== -1 ) {
			let years = d2.getFullYear() - d1.getFullYear();
			d1.setFullYear( d2.getFullYear() );
			if ( d1.getTime() > d2.getTime() ) {
				years -= 1;
				d1.setFullYear( d1.getFullYear() - 1 );
			}
			ms = d2.getTime() - d1.getTime();
			if ( !simple || years ) {
				diff.year = years;
			}
		}
		if ( cmd.indexOf( 'o' ) !== -1 ) {
			let years = d2.getFullYear() - d1.getFullYear();
			d1.setFullYear( d2.getFullYear() );
			if ( d1.getTime() > d2.getTime() ) {
				years -= 1;
				d1.setFullYear( d1.getFullYear() - 1 );
			}
			let months = d2.getMonth() - d1.getMonth();
			if ( d2.getFullYear() - d1.getFullYear() ) {
				months += 12;
			}
			d1.setFullYear( d2.getFullYear() );
			d1.setMonth( d2.getMonth() );
			if ( d1.getTime() > d2.getTime() ) {
				months -= 1;
				d1.setMonth( d1.getMonth() - 1 );
			}
			ms = d2.getTime() - d1.getTime();
			if ( !simple || months || 'year' in diff ) {
				diff.month = years * 12 + months;
			}
		}
		if ( cmd.indexOf( 'd' ) !== -1 ) {
			const days = Math.floor( ms / ( 24 * 3600 * 1e3 ) );
			ms = ms % ( 24 * 3600 * 1e3 );
			if ( !simple || days || 'year' in diff || 'month' in diff ) {
				diff.day = days;
			}
		}
		if ( cmd.indexOf( 'h' ) !== -1 ) {
			const hours = Math.floor( ms / ( 3600 * 1e3 ) );
			ms = ms % ( 3600 * 1e3 );
			if ( !simple || hours || 'day' in diff ) {
				diff.hour = hours;
			}
		}
		if ( cmd.indexOf( 'm' ) !== -1 ) {
			const minutes = Math.floor( ms / ( 60 * 1e3 ) );
			ms = ms % ( 60 * 1e3 );
			if ( !simple || minutes || 'hour' in diff ) {
				diff.minute = minutes;
			}
		}
		if ( cmd.indexOf( 's' ) !== -1 ) {
			const seconds = Math.floor( ms / 1e3 );
			ms = ms % 1e3;
			if ( !simple || seconds || 'minute' in diff ) {
				diff.second = seconds;
			}
		}
		if ( cmd.indexOf( 'M' ) !== -1 || ms === Math.abs( _ms ) ) {
			diff.ms = ms;
		}
		return readableTimeTxt( diff, cpx );
	}
	function readableTimeTxt( timeObj, cpx = false ) {
		let res = cpx ? timeObj.isBefore ? '<span class="salt-time-diff-txt salt-time-diff-before">还有</span>' : '<span class="salt-time-diff-txt salt-time-diff-after">已过去</span>' : '';
		const loop = [
			'year',
			'month',
			'day',
			'hour',
			'minute',
			'second',
			'ms'
		];
		for ( let i = 0; i < loop.length; i++ ) {
			const t = loop[ i ];
			if ( t in timeObj ) {
				res += cpx ? `<span class="salt-time-diff-res salt-time-diff-res-${t}">${timeObj[ t ]}</span><span class="salt-time-diff-txt salt-time-diff-txt-${t}">${i18n[ t ]}</span>` : `${timeObj[ t ]}${i18n[ t ]}`;
			}
		}
		return res;
	}
	function timeDiffHandler() {
		const elems = document.querySelectorAll( '.salt-time-diff' );
		for ( let i = 0; i < elems.length; i++ ) {
			const el = elems[ i ];
			el.classList.remove( 'salt-time-diff' );
			el.classList.add( 'salt-time-diff-done' );
			handleElement( el );
		}
	}
	function handleElement( el ) {
		const t1 = getDate( el.getAttribute( startAttr ) );
		let t2;
		if ( el.getAttribute( endAttr ) === 'REALTIME' ) {
			t2 = getDate();
		} else {
			t2 = getDate( el.getAttribute( endAttr ) );
		}
		const cmd = el.getAttribute( cmdAttr ) || 'd';
		const simple = el.classList.contains( 'simple' );
		const cpx = el.classList.contains( 'complex' );
		const t = timeDiff( { t1, t2, cmd, cpx, simple } );
		if ( !cpx ) {
			if ( el.textContent !== t ) {
				el.textContent = t;
			}
		} else {
			if ( el.innerHTML !== t ) {
				el.innerHTML = t;
			}
		}
	}
	function init() {
		timeDiffHandler();
		const elems = document.querySelectorAll( '.salt-time-diff-done.real-time' );
		const update = () => {
			for ( let i = 0; i < elems.length; i++ ) {
				handleElement( elems[ i ] );
			}
			window.requestAnimationFrame( update );
		};
		window.requestAnimationFrame( update );
	}
	$( init );
}() );
