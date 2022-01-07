<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Reflection\TypeReflection;

class ResolveModelAttributesFromAttributes implements ModelResolver
{
    public function execute(Model $model): void
    {
        $attributeMethods = $this->findAttributeMethods($model);

        /** @var ReflectionMethod $attributeMethod */
        foreach ($attributeMethods as $attributeMethod) {
            $name = $attributeMethod->getName();

            $getter = $this->getAttributeGet($model, $name);

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
    private function findAttributeMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isAttributeMethod($method))
            ->all();
    }

    private function isAttributeMethod(ReflectionMethod $method): bool
    {
        $returnType = $method->getReturnType();

        return $returnType instanceof ReflectionNamedType
            && $returnType->getName() === \Illuminate\Database\Eloquent\Casts\Attribute::class;
    }

    private function getAttributeGet(Model $model, string $attributeMethod): ReflectionFunction
    {
        $attribute = $model->instance()->{$attributeMethod}();

        return new ReflectionFunction($attribute->get);
    }
}
