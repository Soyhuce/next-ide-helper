<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Factory\Actions;

use Soyhuce\NextIdeHelper\Domain\Factories\Actions\FindFactories;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
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
    public function it_finds_all_factories(): void
    {
        $finder = new FindFactories();

        $factories = $finder->execute($this->fixturePath('Factories'));

        $this->assertCount(2, $factories);
    }

    /**
     * @test
     */
    public function factory_name_and_paths_are_correct(): void
    {
        $finder = new FindFactories();

        /** @var \Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory $factory */
        $factory = $finder->execute($this->fixturePath('Factories'))->first();

        $this->assertEquals('\Soyhuce\NextIdeHelper\Tests\Fixtures\Factories\PostFactory', $factory->fqcn);
        $this->assertEquals($this->fixturePath('Factories/PostFactory.php'), $factory->filePath);
    }
}
