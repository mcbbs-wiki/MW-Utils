<?php
namespace MediaWiki\Extension\MCBBSWiki;

use OutputPage;

interface ISaltTool {
	public function outHead( OutputPage $out, $par );

	public function outBody( OutputPage $out, $par );
}
