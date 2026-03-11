<?php

namespace Smolblog\CoreDataFiles;

use Cavatappi\Foundation\Module;
use Cavatappi\Foundation\Module\FileDiscoveryKit;
use Cavatappi\Foundation\Module\ModuleKit;

class Model implements Module {
	use FileDiscoveryKit;
	use ModuleKit;

	protected static function serviceMapOverrides(): array {
		return [];
	}
}
