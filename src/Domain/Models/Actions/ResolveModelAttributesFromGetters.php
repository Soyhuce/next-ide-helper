<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Reflection\TypeReflection;

class ResolveModelAttributesFromGetters implements ModelResolver
{
    public function execute(Model $model): void
    {
        $getters = $this->findGetterMethods($model);

        /** @var ReflectionMethod $getter */
        foreach ($getters as $getter) {
            $name = $this->extractAttribute($getter->getName());

            $returnType = $getter->getReturnType();
            [$type, $nullable] = $returnType === null
                ? ['mixed', false]
                : [TypeReflection::asString($returnType), $returnType->allowsNull()];

            $attribute = $model->attributes->findByName($name);
            if ($attribute === null) {
                $attribute = new Attribute($name, $type);
                $attribute->readOnly = true;
                $attribute->nullable = $nullable;
                $model->addAttribute($attribute);
            } else {
                $attribute->type = $type;
                $attribute->nullable = $nullable;
            }
        }
    }

    /**
     * @return array<ReflectionMethod>
     */
    private function findGetterMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isGetter($method->getName()))
            ->all();
    }

    private function isGetter(string $name): bool
    {
        return Str::startsWith($name, 'get')
            && Str::endsWith($name, 'Attribute')
            && $name !== 'getAttribute';
    }

    private function extractAttribute(string $getterName): string
    {
        return (string) Str::of($getterName)
            ->replaceFirst('get', '')
            ->replaceLast('Attribute', '')
            ->snake();
    }
}
