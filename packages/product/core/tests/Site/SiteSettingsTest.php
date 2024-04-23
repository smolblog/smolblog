<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;

class SiteSettingsTest extends TestCase {
	public function testTheSettingIdIsTheSiteId() {
		$siteId = $this->randomId();
		$settings = new SiteSettings(
			siteId: $siteId,
			title: 'Test site',
			tagline: 'Because you have to test',
		);

		$this->assertEquals($siteId, $settings->id);
	}
}
