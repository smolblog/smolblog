<?php
use Smolblog\Framework\Foundation\DomainModel;

describe('DomainModel::getDependencyMap', function () {
	it('provides the SERVICES constant by default', function () {
		$model = new class() extends DomainModel {
			public const SERVICES = ['one', 'two'];
		};

		expect(get_class($model)::getDependencyMap())->toBe(['one', 'two']);
	});

	it('can be overridden with runtime values', function() {
		$model = new class() extends DomainModel {
			public const SERVICES = ['one', 'two'];
			public static function getDependencyMap(): array { return ['three', ['four']]; }
		};

		expect(get_class($model)::getDependencyMap())->toBe(['three', ['four']]);
	});
});
