<?php
/**
 * This is a benchmark set up to test the efficiency of a static constant versus
 * a static function returning a new object. While the constant is consistently
 * about 1/3rd faster than the function, the function allows the use of value
 * objects and therefore includes type and null checking by virtue of allowing
 * the config schema to be stored _in code_. If this becomes a long-term issue
 * we can come back to this, or maybe PHP will have evolved another way to
 * accomplish it that is more performant.
 * 
 * TL;DR: Constants are faster, but we're using functions because type checking.
 */

namespace Smolblog\Test\Benchmarks;

// phpcs:disable

class ThingRegistrar {
	private $registry = [];

	public function register_with_const(string $class) {
		$key = $class::CONFIG['slug'];
		$registry[$key] = new $class();
	}

	public function register_with_func(string $class) {
		$config = $class::Config();
		$registry[$config->slug] = new $class();
	}
}

class ThingConfig {
	public function __construct(public readonly string $slug) {}
}

class ThingOne {
	const CONFIG = ['slug' => 'thing-one'];
	public static function Config() { return new ThingConfig(slug: 'thing-one'); }
}

class ThingTwo {
	const CONFIG = ['slug' => 'thing-two'];
	public static function Config() { return new ThingConfig(slug: 'thing-two'); }
}

class ThingThree {
	const CONFIG = ['slug' => 'thing-three'];
	public static function Config() { return new ThingConfig(slug: 'thing-three'); }
}

class ThingFour {
	const CONFIG = ['slug' => 'thing-four'];
	public static function Config() { return new ThingConfig(slug: 'thing-four'); }
}

class ThingFive {
	const CONFIG = ['slug' => 'thing-five'];
	public static function Config() { return new ThingConfig(slug: 'thing-five'); }
}

echo "Timing use of const...";
$const_start = microtime(true);
$const_registrar = new ThingRegistrar();
for ($i=0; $i<1000000; $i++) {
	$const_registrar->register_with_const(ThingOne::class);
	$const_registrar->register_with_const(ThingTwo::class);
	$const_registrar->register_with_const(ThingThree::class);
	$const_registrar->register_with_const(ThingFour::class);
	$const_registrar->register_with_const(ThingFive::class);
}
$const_end = microtime(true);
echo " done.\n";

echo "Timing use of func...";
$func_start = microtime(true);
$func_registrar = new ThingRegistrar();
for ($j=0; $j<1000000; $j++) {
	$func_registrar->register_with_func(ThingOne::class);
	$func_registrar->register_with_func(ThingTwo::class);
	$func_registrar->register_with_func(ThingThree::class);
	$func_registrar->register_with_func(ThingFour::class);
	$func_registrar->register_with_func(ThingFive::class);
}
$func_end = microtime(true);
echo " done.\n";

echo ($const_end-$const_start) . " for const.\n";
echo ($func_end-$func_start) . " for function.\n";
