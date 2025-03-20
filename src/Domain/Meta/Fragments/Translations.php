<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\LaravelVsCodeLoader;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class Translations implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $translations = $this->resolveTranslations();

        $metaFile->registerArgumentSet('translations', $translations);

        foreach ($this->methods() as $method) {
            $metaFile->expectedArgumentsFromSet($method, 'translations');
        }
    }

    /**
     * @return array<int, MetaCallable>
     */
    private function methods(): array
    {
        return [
            new MetaCallable([Translator::class, 'has'], 0),
            new MetaCallable([Translator::class, 'hasForLocale'], 0),
            new MetaCallable([Translator::class, 'get'], 0),
            new MetaCallable([Translator::class, 'choice'], 0),
            new MetaCallable([TranslatorContract::class, 'get'], 0),
            new MetaCallable([TranslatorContract::class, 'choice'], 0),
            new MetaCallable('__', 0),
            new MetaCallable('trans', 0),
            new MetaCallable('trans_choice', 0),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveTranslations(): Collection
    {
        return Collection::make(LaravelVsCodeLoader::load('translations')->get('translations'))
            ->keys()
            ->sort()
            ->values();
    }
}
