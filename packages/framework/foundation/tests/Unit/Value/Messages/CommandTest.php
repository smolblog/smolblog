<?php
use Smolblog\Foundation\Value\Messages\Command;

readonly class CommandTest extends Command {
	public function __construct(public string $name) {
		parent::__construct();
	}
}

it('can be instantiated', function() {
	$command = new CommandTest('test');
	expect($command)->toBeInstanceOf(Command::class);
});

it('can have message metadata', function() {
	$command = new CommandTest('test');
	$command->setMetaValue('one', 'two');
	expect($command->getMetaValue('one'))->toBe('two');
});
