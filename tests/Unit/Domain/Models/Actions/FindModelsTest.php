<?php declare(strict_types=1);

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
    public function itFindsAllModels(): void
    {
        $finder = new FindModels();

        $models = $finder->execute($this->fixturePath());

        $this->assertCount(3, $models);
    }

    /**
     * @test
     */
    public function modelNameAndPathsAreCorrect(): void
    {
        $finder = new FindModels();

        /** @var Model $model */
        $model = $finder->execute($this->fixturePath('Blog'))->first();

        $this->assertEquals('\\Soyhuce\\NextIdeHelper\\Tests\\Fixtures\\Blog\\Post', $model->fqcn);
        $this->assertEquals($this->fixturePath('Blog/Post.php'), $model->filePath);
    }
}
