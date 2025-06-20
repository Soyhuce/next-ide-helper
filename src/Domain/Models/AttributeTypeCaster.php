<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\AsHtmlString;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Output\Generics;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;
use function in_array;

class AttributeTypeCaster
{
    public function __construct(
        private Model $model,
    ) {}

    public function resolve(Attribute $attribute): Attribute
    {
        $type = match (true) {
            $this->hasCast($attribute) && $this->isNotInboundCast($attribute) => $this->resolveFromCast($attribute),
            $this->isTimestamps($attribute) => $this->dateClass(),
            default => $this->resolveFromDatabaseType($attribute),
        };

        $attribute->setType($type);

        return $attribute;
    }

    private function isTimestamps(Attribute $attribute): bool
    {
        return in_array($attribute->name, $this->model->instance()->getDates(), true);
    }

    private function hasCast(Attribute $attribute): bool
    {
        return $this->model->instance()->hasCast($attribute->name);
    }

    private function isNotInboundCast(Attribute $attribute): bool
    {
        return !is_subclass_of($this->model->instance()->getCasts()[$attribute->name], CastsInboundAttributes::class);
    }

    private function resolveFromCast(Attribute $attribute): string
    {
        $cast = $this->model->instance()->getCasts()[$attribute->name];

        if (Str::contains($cast, ':')) {
            [$castType, $arguments] = explode(':', $cast, 2);
        } else {
            [$castType, $arguments] = [$cast, null];
        }

        if ($castType === 'encrypted') {
            [$castType, $arguments] = [$arguments ?? 'mixed', null];
        }

        if ($castType === 'hashed') {
            $castType = 'string';
        }

        switch ($castType) {
            case 'int':
            case 'integer':
            case 'timestamp':
                return 'int';
            case 'real':
            case 'float':
            case 'double':
                return 'float';
            case 'decimal':
            case 'string':
                return 'string';
            case 'bool':
            case 'boolean':
                return 'bool';
            case 'object':
                return 'object';
            case 'array':
            case 'json':
                return Generics::get('array', '<array-key, mixed>');
            case 'collection':
                return Generics::get('\\' . Collection::class, '<array-key, mixed>');
            case 'date':
            case 'datetime':
            case 'custom_datetime':
                return $this->dateClass();
            case 'immutable_date':
            case 'immutable_datetime':
            case 'immutable_custom_datetime':
                return $this->immutableDateClass();
        }

        if ($this->isCustomCast($castType)) {
            return $this->resolveCustomCast($castType);
        }

        if ($this->isCastable($castType)) {
            return $this->resolveCastable($castType, $arguments);
        }

        if (enum_exists($castType)) {
            return '\\' . $castType;
        }

        return 'mixed';
    }

    private function resolveFromDatabaseType(Attribute $attribute): string
    {
        return match (Str::lower($attribute->type)) {
            'char',
            'varchar',
            'string',
            'text',
            'mediumtext',
            'longtext',
            'enum',
            'set',
            'json',
            'jsonb',
            'date',
            'datetime',
            'datetimetz',
            'time',
            'timetz',
            'timestamp',
            'timestamptz',
            'year',
            'binary',
            'uuid',
            'ipaddress',
            'macaddress' => 'string',
            'integer',
            'int1',
            'tinyinteger',
            'tinyint',
            'int2',
            'smallinteger',
            'smallint',
            'int4',
            'mediuminteger',
            'mediumint',
            'int8',
            'biginteger',
            'bigint' => 'int',
            'float',
            'double',
            'decimal' => 'float',
            'boolean',
            'bool' => 'bool',
            default => 'mixed',
        };
    }

    private function dateClass(): string
    {
        return '\\' . Date::now()::class;
    }

    private function immutableDateClass(): string
    {
        return '\\' . Date::now()->toImmutable()::class;
    }

    private function isCustomCast(string $castType): bool
    {
        return class_exists($castType)
            && class_implements($castType) !== false
            && in_array(CastsAttributes::class, class_implements($castType), true);
    }

    /**
     * @param class-string<CastsAttributes<mixed, mixed>> $caster
     */
    private function resolveCustomCast(string $caster): string
    {
        $method = (new ReflectionClass($caster))->getMethod('get');

        return FunctionReflection::returnType($method) ?? 'mixed';
    }

    private function isCastable(string $castType): bool
    {
        return class_exists($castType)
            && class_implements($castType)
            && in_array(Castable::class, class_implements($castType), true);
    }

    private function resolveCastable(string $castType, ?string $arguments): string
    {
        $arguments = $arguments === null ? [] : explode(',', $arguments);

        return match ($castType) {
            AsArrayObject::class,
            AsEncryptedArrayObject::class => Generics::get('\\' . ArrayObject::class, '<array-key, mixed>'),
            AsCollection::class,
            AsEncryptedCollection::class => Generics::get(
                '\\' . (filled($arguments[0] ?? null) ? $arguments[0] : Collection::class),
                'array-key, ' . (filled($arguments[1] ?? null) ? '\\' . $arguments[1] : 'mixed')
            ),
            AsEnumArrayObject::class => Generics::get(
                '\\' . ArrayObject::class,
                "<array-key, \\{$arguments[0]}>"
            ),
            AsEnumCollection::class => Generics::get(
                '\\' . Collection::class,
                "<array-key, \\{$arguments[0]}>"
            ),
            AsHtmlString::class => HtmlString::class,
            AsStringable::class => Stringable::class,
            default => "\\{$castType}",
        };
    }
}
