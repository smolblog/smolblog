<?php

namespace Smoltest\PluginStub;

use Smolblog\Core\App;
use Smolblog\Core\Plugin as SmolblogPlugin;

class Plugin implements SmolblogPlugin {
	public function __construct(App $smolblog) {}
}
