<?php

namespace Soyhuce\NextIdeHelper\Domain\Models;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;

class AttributeTypeCaster
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function resolve(Attribute $attribute): Attribute
    {
        switch (true) {
            case $this->isTimestamps($attribute):
                $type = $this->dateClass();

                break;
            case $this->hasCast($attribute):
                $type = $this->resolveFromCast($attribute);

                break;
            default:
                $type = $this->resolveFromDatabaseType($attribute);

                break;
        }

        $attribute->type = $type;

        return $attribute;
    }

    private function isTimestamps(Attribute $attribute): bool
    {
        return in_array($attribute->name, $this->model->instance()->getDates());
    }

    private function hasCast(Attribute $attribute): bool
    {
        return $this->model->instance()->hasCast($attribute->name);
    }

    private function resolveFromCast(Attribute $attribute)
    {
        $castType = $this->model->instance()->getCasts()[$attribute->name];

        switch (Str::lower($castType)) {
            case 'int':
            case 'integer':
            case 'timestamp':
                return 'int';
            case 'real':
            case 'float':
            case 'double':
            case 'decimal':
                return 'float';
            case 'string':
                return 'string';
            case 'bool':
            case 'boolean':
                return 'bool';
            case 'object':
                return 'object';
            case 'array':
            case 'json':
                return 'array';
            case 'collection':
                return '\\' . Collection::class;
            case 'date':
            case 'datetime':
            case 'custom_datetime':
                return $this->dateClass();
        }

        if ($this->isCustomCast($castType)) {
            return $this->resolveCustomCast($castType);
        }

        return 'mixed';
    }

    private function resolveFromDatabaseType(Attribute $attribute)
    {
        switch (Str::lower($attribute->type)) {
            case 'char':
            case 'string':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'enum':
            case 'set':
            case 'json':
            case 'jsonb':
            case 'date':
            case 'datetime':
            case 'datetimetz':
            case 'time':
            case 'timetz':
            case 'timestamp':
            case 'timestamptz':
            case 'year':
            case 'binary':
            case 'uuid':
            case 'ipaddress':
            case 'macaddress':
                return 'string';
            case 'integer':
            case 'tinyinteger':
            case 'tinyint':
            case 'smallinteger':
            case 'smallint':
            case 'mediuminteger':
            case 'mediumint':
            case 'biginteger':
            case 'bigint':
                return 'int';
            case 'float':
            case 'double':
            case 'decimal':
                return 'float';
            case 'boolean':
                return 'bool';
            default:
                return 'mixed';
        }
    }

    private function dateClass(): string
    {
        return '\\' . get_class(Date::now());
    }

    private function isCustomCast(string $castType): bool
    {
        return class_exists($castType) && in_array(CastsAttributes::class, class_implements($castType));
    }

    private function resolveCustomCast(string $caster): string
    {
        $method = (new ReflectionClass($caster))->getMethod('get');

        return FunctionReflection::returnType($method) ?? 'mixed';
    }
}
