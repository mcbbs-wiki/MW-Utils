<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Exception;
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
        throw new KFCCrazyThursdayVME50Exception();
    }
}
class KFCCrazyThursdayVME50Exception extends Exception
{
}
