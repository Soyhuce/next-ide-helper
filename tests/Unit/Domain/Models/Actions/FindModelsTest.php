<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use PHPUnit\Framework\TestCase;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\UsesFixtures;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels
 */
class FindModelsTest extends TestCase
{
    use UsesFixtures;

    /**
     * @test
     */
    public function it_finds_all_models(): void
    {
        $finder = new FindModels();

        $models = $finder->execute($this->fixturePath());

        $this->assertCount(2, $models);
    }

    /**
     * @test
     */
    public function model_name_and_paths_are_correct(): void
    {
        $finder = new FindModels();

        /** @var Model $model */
        $model = $finder->execute($this->fixturePath('Blog'))->first();

        $this->assertEquals('\Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post', $model->fqcn);
        $this->assertEquals($this->fixturePath('Blog/Post.php'), $model->filePath);
    }
}
