<?php

namespace Smolblog\ContentProvenance;

use Smolblog\Framework\Objects\Value;

/**
 * A C2PA Manifest, used to track changes.
 */
class ProvenanceManifest extends Value {
	/**
	 * Construct the Manifest.
	 *
	 * @throws InvalidManifestException If valid parameters are not given.
	 *
	 * @param Action[] $actions Actions taken at this time; at least 1 is required.
	 */
	public function __construct(
		public readonly array $actions,
	) {
		if (empty($actions)) {
			throw new InvalidManifestException('At least one Action is required.');
		}
		if (!empty(array_filter($actions, fn($a) => !is_a($a, Action::class)))) {
			throw new InvalidManifestException('$actions should only contain Actions.');
		}
	}

	/**
	 * Serialize the Manifest.
	 *
	 * @return array
	 */
	public function toArray(): array {
	$arr = [
			'ta_url' => 'http://timestamp.digicert.com',
			'claim_generator' => 'Smolblog/0.1',
			'assertions' => [
				[
					'label' => 'c2pa.actions',
					'data' => [
						'actions' => array_map(fn($a) => $a->toArray(), $this->actions),
					],
				],
			],
		];

		return $arr;
	}
}
