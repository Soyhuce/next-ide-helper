# Changelog

All notable changes to `soyhuce/next-ide-helper` will be documented in this file

## [Next release] - YYYY-MM-DD

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
