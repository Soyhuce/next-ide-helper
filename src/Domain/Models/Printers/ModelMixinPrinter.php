<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Printers;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Soyhuce\NextIdeHelper\Contracts\Amender;
use Soyhuce\NextIdeHelper\Contracts\Renderer;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Output\ModelDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderHelperFile;
use Soyhuce\NextIdeHelper\Domain\Models\Output\RelationsHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class ModelMixinPrinter implements ModelPrinter
{
    public function print(ModelCollection $models): void
    {
        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.models.file_name'));
        $metaFile = new PhpstormMetaFile(config('next-ide-helper.models.mixin_meta'));

        foreach ($models as $model) {
            foreach ($this->renderers($model) as $renderer) {
                $renderer->render();
            }
            foreach ($this->amenders($model) as $amender) {
                $amender->amend($ideHelperFile);
            }
            $this->defineMeta($model, $metaFile);
        }

        $ideHelperFile->render();
        $metaFile->render();
    }

    /**
     * @return array<int, Renderer>
     */
    private function renderers(Model $model): array
    {
        return [
            new ModelDocBlock(
                $model,
                ModelDocBlock::MODEL_MIXIN
                | (config('next-ide-helper.models.mixin_attributes', false) ? ModelDocBlock::PROPERTIES : 0)
            ),
            new QueryBuilderDocBlock($model),
        ];
    }

    /**
     * @return array<int, Amender>
     */
    private function amenders(Model $model): array
    {
        return [
            new ModelDocBlock($model, ModelDocBlock::FULL),
            new QueryBuilderHelperFile($model, IdeHelperClass::model($model->fqcn)),
            new RelationsHelperFile($model, IdeHelperClass::model($model->fqcn)),
        ];
    }

    private function defineMeta(Model $model, PhpstormMetaFile $metaFile): void
    {
        if ($model->queryBuilder->isBuiltIn()) {
            $metaFile->addSimpleOverride(
                new MetaCallable([$model->fqcn, 'query']),
                IdeHelperClass::eloquentBuilder($model->fqcn) . '::class'
            );
        }

        $factory = $model->factory();
        if ($factory !== null) {
            $metaFile->addSimpleOverride(
                new MetaCallable([$model->fqcn, 'factory']),
                "\\{$factory}::class"
            );
        }

        foreach ($model->relations as $relation) {
            if ($relation->eloquentRelation() instanceof MorphTo) {
                continue;
            }

            $metaFile->addSimpleOverride(
                new MetaCallable([$model->fqcn, $relation->name]),
                IdeHelperClass::relation($model->fqcn, $relation->name) . '::class'
            );
        }
    }
}
