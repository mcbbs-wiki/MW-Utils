'use strict';

/**
 * converted by babel
 * from mcbbs-wiki-widget-repo
 */

( function () {
	const highcharts = require( './highcharts.vendor.js' );
	const defaultOption = {
		chart: {
			backgroundColor:
        document.body.classList.contains( 'skin-vector-legacy' ) ||
        document.body.classList.contains( 'skin-minerva' ) ? '#fbf2da' : '#ffffff',
			plotShadow: false
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		credits: {
			href: 'https://mcbbs.wiki/wiki/MCBBS_Wiki:API#%E7%A7%AF%E5%88%86%E6%9F%A5%E8%AF%A2',
			text: '小工具由Salt_lovely制作，使用了Litwak.913的论坛用户信息API和highcharts开源库'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				colors: [
					'#7ccade',
					'#cae07b',
					'#e37bf9',
					'#fce37c',
					'#ff9800',
					'#fd957e',
					'#9ba8f3'
				],
				dataLabels: {
					enabled: true,
					format: '{point.name}: {point.y}分, 占{point.percentage:.1f} %'
				},
				showInLegend: true
			}
		},
		navigation: {
			buttonOptions: {
				enabled: true
			}
		}
	};
	function main() {
		let defaultuid = 1;
		const el = document.getElementsByClassName( 'ucenter-avatar' );
		if ( el.length !== 0 ) {
			defaultuid = el[ 0 ].getAttribute( 'data-uid' );
		}
		Array.from( document.getElementsByClassName( 'userpie' ) ).forEach( ( element ) => {
			let uid = parseInt( element.getAttribute( 'data-uid' ) );
			if ( isNaN( uid ) ) {
				uid = defaultuid;
			}
			getPIE( element, uid );
		} );
	}
	async function getPIE( node, uid ) {
		const $url = 'https://mcbbs.wiki/913-api/users/' + uid;
		const res = await fetch( $url );
		if(res.status===404){
			node.textContent=mw.message( 'mcbbscredit-notfound' ).plain();
			node.style.color='#d33';
			node.style.fontWeight='bold';
			return;
		}
		const creditObj = await res.json(),
			creditsObj = creditObj.credits,
			activites = creditObj.activites,
			nickname = creditObj.nickname;

		const post = activites.post,
			thread = activites.thread,
			digiest = activites.digiest,
			group = activites.currentGroupText;
		const credit = creditsObj.credit,
			popular = creditsObj.popularity,
			contrib = creditsObj.contribute,
			heart = creditsObj.heart,
			diamond = creditsObj.diamond;

		const json = Object.assign( {}, defaultOption, {
			title: { text: nickname + ' \u79EF\u5206\u6784\u6210' },
			subtitle: {
				text:
            'UID: ' +
            uid +
            '; \u79EF\u5206: ' +
            credit +
            '; \u7528\u6237\u7EC4: ' +
            group
			},
			series: [
				{
					type: 'pie',
					name: '积分占比',
					data: [
						{
							name: '\u53D1\u5E16\u6570/' + ( post + thread ) + '\u5E16',
							y: Math.round( ( post + thread ) / 3 )
						},
						{
							name: '\u4E3B\u9898\u6570/' + thread + '\u5E16',
							y: thread * 2
						},
						{
							name: '\u7CBE\u534E\u5E16/' + digiest + '\u5E16',
							y: digiest * 45
						},
						{ name: '\u4EBA\u6C14/' + popular + '\u70B9', y: popular * 3 },
						{ name: '\u8D21\u732E/' + contrib + '\u70B9', y: contrib * 10 },
						{ name: '\u7231\u5FC3/' + heart + '\u9897', y: heart * 4 },
						{ name: '\u94BB\u77F3/' + diamond + '\u9897', y: diamond * 2 }
					]
				}
			]
		} );
		highcharts.chart( node, json );

	}
	$( main() );
}() );
