<?php

namespace Smolblog\ContentProvenance;

use Smolblog\Framework\Objects\Value;

/**
 * Describes an action recorded in a ProvinanceManifest.
 */
class Action extends Value {
	/**
	 * Create the Action.
	 *
	 * @param string      $type        Type of action taken.
	 * @param string|null $description Optional description of the action taken.
	 */
	public function __construct(
		public readonly string $type,
		public readonly ?string $description = null,
	) {
	}

	/**
	 * Serialize this action.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$arr = ['action' => $this->type];
		if (isset($this->description)) {
			$arr['description'] = $this->description;
		}

		return $arr;
	}
}
