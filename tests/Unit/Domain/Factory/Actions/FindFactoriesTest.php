<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Domain\Factories\Actions\FindFactories;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;

it('finds all factories', function (): void {
    $finder = new FindFactories();

    $factories = $finder->execute($this->fixturePath('Factories'));

    expect($factories)->toHaveCount(3);
});

test('factory name and paths are correct', function (): void {
    $finder = new FindFactories();

    /** @var Factory|null $factory */
    $factory = $finder->execute($this->fixturePath('Factories'))->first(
        fn (Factory $factory) => $factory->fqcn === '\\Soyhuce\\NextIdeHelper\\Tests\\Fixtures\\Factories\\PostFactory'
    );

    expect($factory)->not->toBeNull();
    expect($factory->filePath)->toEqual($this->fixturePath('Factories/PostFactory.php'));
});
