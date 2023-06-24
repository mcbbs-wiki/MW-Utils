/* eslint-disable compat/compat */
( function () {
	const highcharts = require( 'highcharts' );
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
		Array.from( document.getElementsByClassName( 'userpie' ) ).forEach( ( element ) => {
			const user = element.getAttribute( 'data-user' );
			getPIE( element, user );
		} );
	}
	function getPIE( node, user ) {
		const creditObj = JSON.parse( user ),
			creditsObj = creditObj.credits,
			activites = creditObj.activities,
			nickname = creditObj.nickname,
			updateat = creditObj.update;
		mw.track( 'bbswiki.userpie.get', creditObj.uid );
		const post = activites.post,
			thread = activites.thread,
			digiest = activites.digiest,
			group = activites.currentGroupText;
		const credit = creditsObj.credit,
			popular = creditsObj.popularity,
			contrib = creditsObj.contribute,
			heart = creditsObj.heart,
			diamond = creditsObj.diamond;
		let subtxt = `UID: ${creditObj.uid}; 积分: ${credit}; 用户组: ${group}；更新于: ${updateat}`;
		if ( creditObj.fallback ) {
			subtxt += '\n正在显示历史数据';
		}
		const json = Object.assign( {}, defaultOption, {
			title: { text: `${nickname} 积分构成` },
			subtitle: {
				text: subtxt
			},
			series: [
				{
					type: 'pie',
					name: '积分占比',
					data: [
						{
							name: `发帖数/${( post + thread )}帖`,
							y: Math.round( ( post + thread ) / 3 )
						},
						{
							name: `主题数/${thread}帖`,
							y: thread * 2
						},
						{
							name: `精华帖/${digiest}帖`,
							y: digiest * 45
						},
						{ name: `人气/${popular}点`, y: popular * 3 },
						{ name: `贡献/${contrib}点`, y: contrib * 10 },
						{ name: `爱心/${heart}颗`, y: heart * 4 },
						{ name: `钻石/${diamond}颗`, y: diamond * 2 }
					]
				}
			]
		} );
		highcharts.chart( node, json );
	}
	$( main );
}() );
