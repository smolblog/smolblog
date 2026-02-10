<?php

namespace Smolblog\Core\Content\Extensions\License;

enum LicenseType: string {
	case FullCopyright = 'private';
	case NonCommercialNoDerivs = 'cc-by-nc-nd';
	case NonCommercialShareAlike = 'cc-by-nc-sa';
	case NonCommercial = 'cc-by-nc';
	case NoDerivs = 'cc-by-nd';
	case ShareAlike = 'cc-by-sa';
	case Attribution = 'cc-by';
	case Zero = 'cc0';
	case PublicDomain = 'pd';

	/**
	 * Get a display name for a LicenseType
	 *
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function displayName(): string {
		return match ($this) {
			self::FullCopyright => 'Full Copyright',
			self::NonCommercialNoDerivs => 'Creative Commons Attribution-NonCommercial-NoDerivatives',
			self::NonCommercialShareAlike => 'Creative Commons Attribution-NonCommercial-ShareAlike',
			self::NonCommercial => 'Creative Commons Attribution-NonCommercial',
			self::NoDerivs => 'Creative Commons Attribution-NoDerivatives',
			self::ShareAlike => 'Creative Commons Attribution-ShareAlike',
			self::Attribution => 'Creative Commons Attribution',
			self::Zero => 'CC0 Public Domain Dedication',
			self::PublicDomain => 'Public Domain',
		};
	}
}
