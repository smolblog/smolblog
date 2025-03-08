<?php

namespace Smolblog\WP;

class Plugin {
	public static function BootstrapMain(): void {
		add_filter('admin_footer_text', array(self::class, 'adminFooterText'), 2000);
	}

	public static function adminFooterText($originalText): string {
		return 'Smolblog 0.4.0';
	}
}
