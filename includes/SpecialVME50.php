<?php

namespace MediaWiki\Extension\MCBBSWiki;

use MWException;
use SpecialPage;

class SpecialVME50 extends SpecialPage
{
    function __construct()
    {
        parent::__construct('VME50', '', false);
    }
    function execute($par)
    {
        throw new MWException('KFC Crazy Thursday need ￥50');
    }
}
