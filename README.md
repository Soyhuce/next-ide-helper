# Laravel ide helper rebuilt under steroids

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/next-ide-helper.svg?style=flat-square)](https://packagist.org/packages/soyhuce/next-ide-helper)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/next-ide-helper/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/soyhuce/next-ide-helper/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/next-ide-helper/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/soyhuce/next-ide-helper/actions/workflows/phpstan.yml)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/next-ide-helper/php-cs-fixer.yml?branch=main&label=php-cs-fixer&style=flat-square)](https://github.com/soyhuce/next-ide-helper/actions/workflows/php-cs-fixer.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/next-ide-helper.svg?style=flat-square)](https://packagist.org/packages/soyhuce/next-ide-helper)

This package aims to be an easy extendable ide-helper generator.

It was inspired by the great work of [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper).

It provides completion for Eloquent magic (model attributes, scopes, relations, ...), registered macros of Macroable
classes, container instances, ...

- [Installation](#installation)
- [Usage](#usage)
    - [Models](#models)
        - [Attributes](#attributes)
        - [Custom Collection](#custom-collection)
        - [Query Builder](#query-builder)
        - [Relations](#relations)
        - [Mixin style helper](#mixin-style-helper)
        - [Eloquent Builders](#eloquent-builders)
        - [Extensions](#extensions)
    - [Macros](#macros)
    - [Phpstorm meta](#phpstorm-meta)
    - [Factories](#factories)
    - [Aliases](#aliases)
    - [Generate all](#generate-all)
    - [Custom application bootstrap](#custom-application-bootstrap)
    - [Custom content in docblock](#custom-content-in-docblock)
- [Contributing](#contributing)
- [License](#license)

# Installation

You should install this package using composer :

```shell script
composer require --dev soyhuce/next-ide-helper
``` 

You may want to publish configuration file :

```shell script
php artisan vendor:publish --tag=next-ide-helper-config
```

You're done !

# Usage

## Models

The command `php artisan next-ide-helper:models` will generate multiple elements to help your ide understand what you
are doing. This package needs you to have access to a migrated database.

It will add docblock to your classes and will create an `_ide_models.php` file. This file **must not** be included but
only analyzed by your ide.

### Attributes

The command resolves model attributes from the database. They are added to your model class docblock. If the attribute
has a cast, the package will cast properly the attribute.

```php
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class User extends \Illuminate\Database\Eloquent\Model
{
    // ...
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

Attribute casting will also work with custom casts :

```php
use App\Email;

class EmailCast implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes
{
    public function get($model, $key, $value, $attributes): Email
    {
        return new Email($value);
    }
    
    // ...
}

class User extends Model
{
    protected $casts = [
        'email' => EmailCast::class,
    ];
}
```

This will produce `@property \App\Email $email`

Note that the type must be defined as return type or in docblock's `@return` of the `get` method.

The command also adds attributes from accessors as read-only properties :

```php
/**
 * @property-read string $upper_name
 */
class User extends Model
{
    public function getUpperNameAttribute(): string
    {
        return Str::upper($this->name);
    }   
}
```

### Custom collection

In case your model defines a custom collection, the command will add `all` method on the model's docblock to re-define
return type :

```php
use \App\Collections\UserCollection;

/**
 * @method static \App\Collections\UserCollection all(array|mixed $columns = ['*'])
 */
class User extends Model
{
    public function newCollection(array $models = []): UserCollection
    {
        return new UserCollection($models);
    }
}
```

### Query Builder

If your model defines a custom Eloquent builder, the command will add some tags on the model docblock.

```php
use App\Builder\UserBuilder;
/**
 * @method static \App\Builder\UserBuilder query()
 * @mixin \App\Builder\UserBuilder
 */
class User extends Model
{
    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }
}
```

It will also add some tags on the builder to help your ide :

- where clauses based on model attributes
- return values for result values

```php
use Illuminate\Database\Eloquent\Builder;

/**
 * @method \App\Builder\UserBuilder whereId(int|string $value)
 * @method \App\Builder\UserBuilder whereName(string $value)
 * @method \App\Builder\UserBuilder whereEmail(string $value)
 * @method \App\Builder\UserBuilder whereEmailVerifiedAt(\Illuminate\Support\Carbon|string|null $value)
 * @method \App\Builder\UserBuilder wherePassword(string $value)
 * @method \App\Builder\UserBuilder whereRememberToken(string|null $value)
 * @method \App\Builder\UserBuilder whereCreatedAt(\Illuminate\Support\Carbon|string $value)
 * @method \App\Builder\UserBuilder whereUpdatedAt(\Illuminate\Support\Carbon|string $value)
 * @method \App\User create(array $attributes = [])
 * @method \Illuminate\Database\Eloquent\Collection|\App\User|null find($id, array $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Collection findMany($id, array $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Collection|\App\User findOrFail($id, array $columns = ['*'])
 * @method \App\User findOrNew($id, array $columns = ['*'])
 * @method \App\User|null first(array|string $columns = ['*'])
 * @method \App\User firstOrCreate(array $attributes, array $values = [])
 * @method \App\User firstOrFail(array $columns = ['*'])
 * @method \App\User firstOrNew(array $attributes = [], array $values = [])
 * @method \App\User forceCreate(array $attributes = [])
 * @method \Illuminate\Database\Eloquent\Collection get(array|string $columns = ['*'])
 * @method \App\User getModel()
 * @method \Illuminate\Database\Eloquent\Collection getModels(array|string $columns = ['*'])
 * @method \App\User newModelInstance(array $attributes = [])
 * @method \App\User updateOrCreate(array $attributes, array $values = [])
 * @template TModelClass
 * @extends \Illuminate\Database\Eloquent\Builder<\App\User>
 */
class UserBuilder extends Builder
{
}
```

If your model does not define a custom builder, `next-ide-helper:models` will create fake classes in `_ide_models.php`
with the docblocks to provides auto-completion.

### Scopes

All scopes of your models will be added as method of their builder (in the custom query builder or in `_ide_models.php`)
.

```php
class User extends Model
{
    public function scopeWhereVerified($query, bool $verified = true): void
    {
        $query->whereNull('email_verified_at', 'and', !$verified);
    }
}
```

![](docs/model_scope_autocomplete.png)

This will produce `@method \App\Builder\UserBuilder whereVerified(bool $verified = true)` on your custom builder.

Note that your ide can complain
with `Non-static method 'whereVerified' should not be called statically, but the class has the '__magic' method.` if you
just call `User::whereVerified()`. That's why we advise you to use `User::query()->...`.

### Relations

The models command will also resolve relations of your model and provide a lot of completion helpers.

```php
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Post> $posts
 */
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

class Post extends Model
{
    public function scopeWherePublished($query): void
    {
        return $query->whereNotNull('published_at');
    }
}
``` 

![](docs/scope_for_relation_autocomplete.png)

Custom builders and custom collections are also correctly resolved by the ide :

![](docs/collection_for_relation_autocomplete.png)

### Larastan friendly tags

In case you use PHPStan or Larastan, you can have more information about the collections
defining `models.larastan_friendly` config to `true`.

With this config, you will get the extra tags in you models

```php
/**
 * @phpstan-method static \App\Collections\UserCollection<int, \App\Models\User> all(array|mixed $columns = ['*'])
 */
class User extends Model {}
```

and in your custom builders

```php
/**
 * @phpstan-method \Illuminate\Database\Eloquent\Collection<int, \App\User>|\App\User|null find($id, array $columns = ['*'])
 * @phpstan-method \Illuminate\Database\Eloquent\Collection<int, \App\User> findMany($id, array $columns = ['*'])
 * @phpstan-method \Illuminate\Database\Eloquent\Collection<int, \App\User>|\App\User findOrFail($id, array $columns = ['*'])
 * @phpstan-method \Illuminate\Database\Eloquent\Collection<int, \App\User> get(array|string $columns = ['*'])
 * @phpstan-method \Illuminate\Database\Eloquent\Collection<int, \App\User> getModels(array|string $columns = ['*'])
 * @template TModelClass
 * @extends \Illuminate\Database\Eloquent\Builder<\App\User>
 */
class UserBuilder extends Builder {}
```

### Mixin style helper

Instead of adding every tag in the model, this package can add a `@mixin` tag in the model docblock. The mixin will be located in the configured ide helper file.

```php
/**
 * @mixin \IdeHelper\App\Models\__User
 */
class User extends \Illuminate\Database\Eloquent\Model
{
    // ...
}
```

To enable this feature, you need to set `models.use_mixin` to `true` in your config file.

You can still have model's attributes added to your model docblock by setting `models.mixin_attributes` to `true`.

```php
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @mixin \IdeHelper\App\Models\__User
 */
class User extends \Illuminate\Database\Eloquent\Model
{
    // ...
}
```

#### PHPStan gotchas

While this feature is convenient, it has some drawbacks. We strongly encourage you to define `models.mixin_attributes` to `true` as PHPStan does not read properties from the mixin class.

You may also have analysis errors like `PHPDoc tag @mixin contains unknown class \IdeHelper\App\Models\__User`. To solve this, you can add the following to your `phpstan.neon` file:

```neon
parameters:
  scanFiles:
    - ide_helper/models.php
```

### Eloquent builders

This package tries to automate as much as possible, but sometimes it cannot guess everything. 

When using custom Eloquent builders for your model, it cannot guess if the builder is a specific one (used only for one model) or a generic one (used for multiple models).
For the package to understand which builders are generic, you will have to define them in `models.generic_builder` config array.

### Extensions

Sometimes, the command cannot resolve or anticipate every way everything are resolved.

That's why this package provides a way to customize some resolution logic adding your custom resolver
in `next-ide-helper.models.extensions` config.

## Macros

This package provides a `next-ide-helper:macros`. The command resolves all registered macros and generates
a `_ide_macros.php` file which provides auto-completion for `Macroable` macros.

For example :

```php
use Illuminate\Support\Collection;

Collection::macro('mapToUpper', function(): Collection {
    return $this->map(fn(string $item) => \Illuminate\Support\Str::upper($item));
});
```

Thanks to `_ide_macros.php` file, we have auto-completion for the `mapToUpper` method :

![](docs/macro_autocomplete.png)

Just like `_ide_models.php`, the `_ide_macros.php` file must not be included but only analyzed by your ide.

## Phpstorm meta

The command `php artisan next-ide-helper:meta` will generate a `.phpstorm.meta.php` file. It will provide completion for
container bindings and some laravel helpers

![](docs/optional_autocomplete.png)
![](docs/app_autocomplete.png)

## Factories

The command `php artisan next-ide-helper:factories` will add docblocks to your factories in order to correctly type some
methods. It will also explicit magic methods for model relations.

For example, if you have

```php
class User extends Model                        
{
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    
    public function newCollection(array $models = [])
    {
        return new UserCollection($models);
    }
}
``` 

this command will generate the docblock in `UserFactory`:

```php
/**
 * @method \App\User createOne($attributes = [])
 * @method \App\User|\App\Collections\UserCollection create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
 * @method \App\User makeOne($attributes = [])
 * @method \App\User|\App\Collections\UserCollection make($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
 * @method \App\User newModel(array $attributes = [])
 * @method \Database\Factories\UserFactory forRole($attributes = [])
 * @method \Database\Factories\UserFactory hasPosts($count = 1, $attributes = [])
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\User>
 */
class UserFactory extends Factory
{
    //    
}
```

## Aliases

Sometimes we don't want to use fully qualified class names but prefer to use Laravel aliases.

The command `php artisan next-ide-helper:aliases` will create a file which can be understood by your ide.

It will then provide auto-completion, syntax hightlight, ... for the aliases defined in your `config/app.php` file as
well as the ones defined by the package you use.

## Generate all

You can generate all next-ide-helper files using `next-ide-helper:all`.

It will generate for you :

- Models
- Macros
- Phpstorm meta
- Aliases
- Factories (if you are using Laravel 8 class based model factories)

## Custom application bootstrap

Sometimes you may want to bootstrap the environment before the command is executed. For example in a multi-tenant
multi-database application, you need to bootstrap your tenant connection in order to let this package resolve table
columns.

In that case, you just have to create your own bootstrapper and configure the package to use it :

```php
class MultitenantBootstrapper implements \Soyhuce\NextIdeHelper\Contracts\Bootstrapper 
{
    private Tenancy $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;    
    }

    public function bootstrap() : void
    {
        $tenant = \App\Tenant::firstOrFail();
        
        $this->tenancy->connect($tenant);
    }
}
// Note that this code is completely fictive.
```

Now, you just have to add it in you `next-ide-helper.php` config file :

```php
'bootstrapper' => \App\Support\MultitenantBootstrapper::class,
```

Your bootstrapper benefits from laravel dependency injection in its constructor.

## Custom content in docblock

Some command will reset your docblock. If you want some content not to be erased, you must add a `@generated` tag in the docblock
to tell nest-ide-helper where to insert its content.

For exemple, 
```php
/**
 * Comment that will not be overwritten after docblock generation
 *  
 * @author John Doe
 * @package Foo Bar
 * @deprecated since 1.0.0
 * @api 
 * 
 * @generated
 * [the content generated by next-ide-helper will be inserted here]
 */
class SomeModel extends Model {}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bastien Philippe](https://github.com/bastien-phi)
- [Laravel team and all contributors for laravel/vs-code-extension](https://github.com/laravel/vs-code-extension)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
