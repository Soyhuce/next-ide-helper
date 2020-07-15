<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Factory\Actions;

use Soyhuce\NextIdeHelper\Domain\Factories\Actions\FindFactories;
use Soyhuce\NextIdeHelper\Tests\TestCase;
use Soyhuce\NextIdeHelper\Tests\UsesFixtures;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Factories\Actions\FindFactories
 */
class FindFactoriesTest extends TestCase
{
    use UsesFixtures;

    /**
     * @test
     */
    public function itFindsAllFactories(): void
    {
        $finder = new FindFactories();

        $factories = $finder->execute($this->fixturePath('Factories'));

        $this->assertCount(2, $factories);
    }

    /**
     * @test
     */
    public function factoryNameAndPathsAreCorrect(): void
    {
        $finder = new FindFactories();

        /** @var \Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory $factory */
        $factory = $finder->execute($this->fixturePath('Factories'))->first();

        $this->assertEquals('\Soyhuce\NextIdeHelper\Tests\Fixtures\Factories\PostFactory', $factory->fqcn);
        $this->assertEquals($this->fixturePath('Factories/PostFactory.php'), $factory->filePath);
    }
}
