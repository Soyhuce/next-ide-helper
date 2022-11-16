<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
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
            [$getter, $setter] = $this->getAttributeGetterAndSetter($model, $attributeMethod->getName());
            $name = Str::snake($attributeMethod->getName());
            $attribute = $model->attributes->findByName($name);

            if ($attribute === null) {
                $attribute = new Attribute($name, '');
                $attribute->readOnly = true;
                $attribute->writeOnly = true;
                $model->addAttribute($attribute);
            }

            $type = null;

            if ($getter !== null) {
                [$getterType, $getterNullable] = $getter->getReturnType() === null
                    ? ['mixed', false]
                    : [TypeReflection::asString($getter->getReturnType()), $getter->getReturnType()->allowsNull()];
                $attribute->writeOnly = false;

                $type = $getterType;
                $attribute->nullable = $attribute->nullable || $getterNullable;
            }

            if ($setter !== null) {
                $firstParameter = $setter->getParameters()[0] ?? null;
                [$setterType, $setterNullable] = $firstParameter?->getType() === null
                    ? ['mixed', false]
                    : [TypeReflection::asString($firstParameter->getType()), $firstParameter->getType()->allowsNull()];
                $attribute->readOnly = false;

                if ($type === null) {
                    $type = $setterType;
                } elseif ($type !== $setterType) {
                    $type = 'mixed';
                }
                $attribute->nullable = $attribute->nullable || $setterNullable;
            }

            $attribute->setType($type ?? 'mixed');
        }
    }

    /**
     * @return array<ReflectionMethod>
     */
    private function findAttributeMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED))
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

    /**
     * @return array<?ReflectionFunction>
     */
    private function getAttributeGetterAndSetter(Model $model, string $attributeMethod): array
    {
        $attribute = (fn () => $this->{$attributeMethod}())->call($model->instance());

        return [
            $attribute->get !== null ? new ReflectionFunction($attribute->get) : null,
            $attribute->set !== null ? new ReflectionFunction($attribute->set) : null,
        ];
    }
}
