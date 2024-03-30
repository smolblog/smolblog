<?php
use Psr\Container\ContainerInterface;
use Smolblog\Framework\Foundation\Service\Registry;
use Smolblog\Framework\Foundation\Service\RegistryKit;

final class TestRegistry implements Registry {
	use RegistryKit;
	public function __construct(ContainerInterface $container) { $this->container = $container; }
	public static function getInterfaceToRegister(): string { return 'Interface'; }
	private function getKeyForClass(string $class): string { return $class . '_key'; }
	public function getLibrary(): array { return $this->library; }
}

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
	/**
	 * Store (mock) dependencies for the service.
	 *
	 * @var array
	 */
	protected array $deps = [];

	/**
	 * Store a
	 *
	 * @var mixed
	 */
	protected mixed $service;

	/**
	 * Build the given service using $this->deps.
	 *
	 * @param string $class Fully-qualified class name of service to instantiate.
	 * @return mixed
	 */
	protected function buildService(string $class): mixed {
		return new $class(...$this->deps);
	}
}

beforeEach(function() {
	$this->deps['container'] = Mockery::mock(ContainerInterface::class);
	$this->service = $this->buildService(TestRegistry::class);
	$this->service->configure(['ServiceOne', 'ServiceTwo']);
});

describe('RegistryKit::configure', function() {
	it('will configure the Registry', fn() =>
		expect($this->service->getLibrary())->
		toBe(['ServiceOne_key' => 'ServiceOne', 'ServiceTwo_key' => 'ServiceTwo'])
	);
});

describe('RegistryKit::has', function() {
	it('will return true if the given key is present and the class is in the container', function() {
		$this->deps['container']->shouldReceive('has')->andReturn(true);

		expect($this->service->has('ServiceOne_key'))->toBeTrue();
	});

	it('will return false if the given key is not present', function() {
		$this->deps['container']->shouldReceive('has')->zeroOrMoreTimes()->andReturn(true);

		expect($this->service->has('ServiceOne'))->toBeFalse();
	});

	it('will return false if the given key is present but the class is not in the container', function() {
		$this->deps['container']->shouldReceive('has')->andReturn(false);

		expect($this->service->has('ServiceOne_key'))->toBeFalse();
	});
});

describe('RegistryKit::get', function() {
	it('will return an instance of the class if the given key is present and the class is in the container', function() {
		$this->deps['container']->shouldReceive('has')->andReturn(true);
		$this->deps['container']->shouldReceive('get')->andReturn('ServiceOne_class_instance');

		expect($this->service->get('ServiceOne_key'))->toBe('ServiceOne_class_instance');
	});

	it('will return null if the given key is not present', function() {
		$this->deps['container']->shouldReceive('has')->zeroOrMoreTimes()->andReturn(true);
		$this->deps['container']->shouldReceive('get')->zeroOrMoreTimes()->andReturn('ServiceOne_class_instance');

		expect($this->service->get('ServiceOne'))->toBeNull();
	});

	it('will return false if the given key is present but the class is not in the container', function() {
		$this->deps['container']->shouldReceive('has')->andReturn(false);
		$this->deps['container']->shouldNotReceive('get');

		expect($this->service->get('ServiceOne_key'))->toBeNull();
	});
});
