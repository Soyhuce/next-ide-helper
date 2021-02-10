# Changelog

All notable changes to `soyhuce/next-ide-helper` will be documented in this file

## [Next release] - YYYY-MM-DD

### Added

- Attributes with CastsInboundAttribues cast have database defined type.
- Return type for `sole` method

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
