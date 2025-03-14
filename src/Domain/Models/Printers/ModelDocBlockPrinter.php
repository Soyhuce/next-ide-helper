<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Printers;

use Soyhuce\NextIdeHelper\Contracts\Amender;
use Soyhuce\NextIdeHelper\Contracts\Renderer;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Output\ModelDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderHelperFile;
use Soyhuce\NextIdeHelper\Domain\Models\Output\RelationsHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class ModelDocBlockPrinter implements ModelPrinter
{
    public function print(ModelCollection $models): void
    {
        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.models.file_name'));

        foreach ($models as $model) {
            foreach ($this->renderers($model) as $renderer) {
                $renderer->render();
            }
            foreach ($this->amenders($model) as $amender) {
                $amender->amend($ideHelperFile);
            }
        }

        $ideHelperFile->render();
    }

    /**
     * @return array<Renderer>
     */
    private function renderers(Model $model): array
    {
        return [
            new ModelDocBlock($model, ModelDocBlock::FULL),
            new QueryBuilderDocBlock($model),
        ];
    }

    /**
     * @return array<Amender>
     */
    private function amenders(Model $model): array
    {
        return [
            new QueryBuilderHelperFile($model, $model->fqcn),
            new RelationsHelperFile($model, $model->fqcn),
        ];
    }
}
