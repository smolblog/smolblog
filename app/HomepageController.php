<?php

namespace Smolblog\App;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\view;

final class HomepageController {
	#[Get(uri: '/')]
	public function __invoke(): View {
		return view('dummy-view');
	}
}
