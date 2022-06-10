<?php

namespace Smolblog\Core;

interface ExternalAuthenticationProvider {
	public function slug(): string;
}
