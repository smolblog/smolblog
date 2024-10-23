<?php

namespace Smolblog\Foundation\Value\Jobs;

use Smolblog\Foundation\Value\Traits\SerializableSupertypeBackupKit;

/**
 * A basic Job that has a service, method, and props. Unknown Job classes will deserialize to this.
 *
 * For general use, the idea is `$container->get($service)->$method(...$props)`.
 */
readonly class BasicJob extends Job {
	use SerializableSupertypeBackupKit;

	/**
	 * Construct the job.
	 *
	 * @param string $service Service to instantiate.
	 * @param string $method  Method on $service to call.
	 * @param array  $props   Props to be passed to $method.
	 */
	public function __construct(
		string $service,
		string $method,
		public array $props,
	) {
		parent::__construct(service: $service, method: $method);
	}

	/**
	 * Return $this->props as the method parameters.
	 *
	 * @return array
	 */
	public function getParameters(): array {
		return $this->props;
	}

	/**
	 * Note that extra properties go in the $props field.
	 *
	 * @return string
	 */
	private static function extraPropsField(): string {
		return 'props';
	}
}
