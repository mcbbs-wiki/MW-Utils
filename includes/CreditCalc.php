<?php

namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Extension\EditCount\EditCountQuery;
use User;

class CreditCalc
{
    const scores = [
        1, 7, 15, 20, 25, 30, 35, 40, 45, 50,
        60, 70, 80, 90, 100, 120, 140, 160, 180, 200,
        230, 260, 290, 320, 350, 380, 410, 440, 470, 500,
        550, 600, 650, 700, 750, 800, 850, 900, 950, 1000,
        1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000,
        2300, 2600, 2900, 3200, 3500, 3800, 4100, 4400, 4700, 5000,
        5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500, 10000,
        14000, 18000, 22000, 26000, 30000, 34000, 38000, 42000, 46000, 50000,
        55000, 60000, 65000, 70000, 75000, 80000, 85000, 90000, 95000, 100000,
        190000, 280000, 370000, 460000, 550000, 640000, 730000, 820000, 910000, 1000000,
        2147483647
    ];
    // 等级积分上限 => 等级 class 名
    const levels = [
        1 => 'lv00',
        50 => 'lv01',
        200 => 'lv01-5',
        500 => 'lv02',
        1000 => 'lv03',
        2000 => 'lv04',
        5000 => 'lv05',
        10000 => 'lv06',
        50000 => 'lv07',
        100000 => 'lv08',
        1000000 => 'lv09',
        2147483647 => 'lv10'
    ];
    const calcCredit = '';
    public static function calcUserLevel(User $user)
    {
        $ns = EditCountQuery::queryAllNamespaces($user);
        @$credit = $ns[0] * 3 + $ns[10] * 2.5 + $ns[12] * 2 + $ns[14] + $ns[4] + floor($ns[6] / 4) + floor(($ns[1] + $ns[5] + $ns[11]) / 8);
        return $credit;
    }
    public static function calcLevel(int $credit)
    {
        $lvl = 0;
        foreach (self::scores as $index => $score) {
            if ($credit < $score) {
                $lvl = $index;
                break;
            }
        }
        $class = '';
        foreach (self::levels as $least => $level) {
            if ($credit < $least) {
                $class = $level;
                break;
            }
        }
        return ['level' => $lvl, 'class' => $class];
    }
}
