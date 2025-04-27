<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Smolblog\FeatureTest\Context\{ConnectionContext};

$defaultProfile = new Profile('default')
	->withSuite(
		new Suite('default')
			->withContexts(ConnectionContext::class)
	)
;

return new Config()
	->withProfile($defaultProfile)
;
