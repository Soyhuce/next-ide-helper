# Changelog

All notable changes to `soyhuce/next-ide-helper` will be documented in this file

## [Next release] - YYYY-MM-DD

## [0.7.1] - 2022-01-21

### Fixed

- Model factory resolution for Model::factory() docblock

## [0.7.0] - 2022-01-07

### Added

- Laravel 9 support
- Support for attribute accessor and mutators via \Illuminate\Database\Eloquent\Casts\Attribute

### Fixed

- Delete where* query helper from mutated attributes which does not exist in database 

### Changed

- Laravel 8.77+ required
- Soyhuce\NextIdeHelper\Console\Bootstrapper moved to Soyhuce\NextIdeHelper\Contracts\Bootstrapper
- Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver moved to Soyhuce\NextIdeHelper\Contracts\ModelResolver
- Soyhuce\NextIdeHelper\Domain\Factories\Actions\FactoryResolver moved to Soyhuce\NextIdeHelper\Contracts\FactoryResolver

### Removed

- All the built-in Model extensions

## [0.6.3] - 2021-12-07

### Added

- Support for Enum casting

## [0.6.2] - 2021-10-25

### Fixed

- Property type is mixed when getPropertyAttribute does not define its return type.

## [0.6.1] - 2021-10-15

### Changed

- Allow union and pure intersection types in model overrides

## [0.6.0] - 2021-09-13

### Changed

- Prefix generated classes with `__` in order to make them less searchable by the ide.

### Fixed

- Don't document magic methods on factories which already exist.

## [0.5.4] - 2021-09-03

### Added

- Add support for doctrine/dbal:^3.0

## [0.5.3] - 2021-04-26

### Added

- Add `extras` in QueryBuilder in order to add more information in query builder doc block.

## [0.5.2] - 2021-03-25

### Changed

- Fake relation classes in _ide_model do not extend Relation anymore but are marked with @mixin Relation.

## [0.5.1] - 2021-03-19

### Changed

- Add model constructor in _ide_models file
- Better export of default parameters in methods

### Fixed

- Do not instantiate the model factory to get its class but use Factory::resolveFactoryName() directly.
- Revert 0.5.0 : In _ide_models file, models do not overwrite real model anymore
- Revert 0.5.0 : Add @mixin to model docblock referencing models in _ide_models file

## [0.5.0] - 2021-03-12

### Changed

- In _ide_models file, models do not overwrite real model anymore
- Add @mixin to model docblock referencing models in _ide_models file
- Add constructor in _ide_macros files for macroable classes to prevent the ide complaining about constructor
  arguments (#28)

### Fixed

- Relation throwing an exception do not make the commands to fail anymore but are just ignored

## [0.4.3] - 2021-03-03

### Fixed

- Correction of the way overrides are exported in docblock when they are an interface or a trait
- Remove not working tags for MorphTo

## [0.4.2] - 2021-02-25

### Added

- Add ability to override type for model relations

## [0.4.1] - 2021-02-16

### Added

- Query builder completion for models using `SoftDeletes`

## [0.4.0] - 2021-02-10

### Added

- Attributes with CastsInboundAttribues cast have database defined type.
- Return type for `sole` method
- Add ability to override type for model attributes

### Fixed

- Add static for macros when declared static

## [0.3.3] - 2021-01-28

### Added

- Support for Castable interface

### Fixed

- Nullable type for custom casts (#15)

### Changed

- Laravel minimum version is now 8.0

## [0.3.2] - 2020-11-23

### Fixed

- add missing command registration

## [0.3.1] - 2020-11-23

### Added

- next-ide-helper:all command to run all commands

## [0.3.0] - 2020-11-09

### Added

- Support PHP 8

## [0.2.5] - 2020-09-29

### Fixed

- Fix self return type in macros
- Remove extra space after ... in macros

## [0.2.4] - 2020-09-29

### Added

- Ability to add docblock methods for Laravel factories

## [0.2.3] - 2020-09-28

### Added

- SpatieModelStateResolver to resolve model states from `spatie/laravel-model-states`
- Ability to add extensions for Laravel factories

## [0.2.2] - 2020-09-11

### Fixed

- ParameterReflection::asString when the parameter is variadic

## [0.2.1] - 2020-08-31

### Added

- Allow installing composer/composer ^2.0

## [0.2.0] - 2020-08-27

### Added

- Support Laravel 8

## [0.1.5] - 2020-08-05

### Added

- command `next-ide-helper:aliases` to generate a file helping the ide to understand aliases

## [0.1.4] - 2020-07-16

### Added

- Option to generate larastan-friendly doc blocks for models
- Typed `factory` method on model using `HasFactory`

### Changed

- HasOne relations are documented with `has` in factories docblocks

## [0.1.3] - 2020-07-02

### Fixed

- nullable parameter syntax for factory docblock

## [0.1.2] - 2020-06-29

### Changed

- relations methods are now defined in docblock instead of actual methods in _ide_models.php file

### Fixed

- add static for `Model::query()` method in docblocks in _ide_models.php file

## [0.1.1] - 2020-05-18

### Added

- command `next-ide-helper:factories` to generate Laravel 8 factories docblock :
    - method types
    - magic relation methods

## [0.1.0] - 2020-05-15

### Added

- Models :
    - attributes
    - attributes from accessors
    - attribute casting
    - custom collection
    - custom query builder
    - relations
    - query builder :
        - where clauses from attributes
        - where clauses from scopes
        - type of result methods
    - relations :
        - mixin with related's query builder
    - custom resolvers

- Macros :
    - generation of file with macros to provide auto-completion

- Phpstorm Meta :
    - generation of file with meta to provide auto-completion

- Misc :
    - custom bootstrapper
