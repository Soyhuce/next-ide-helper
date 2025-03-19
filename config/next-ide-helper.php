<?php declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

return [
    /**
     * The bootstrapper use to boot your application before running analyses.
     * You may want, for exemple, to setup a database for your tenant model in
     * case of multitenant application.
     * The class must implement \Soyhuce\NextIdeHelper\Contracts\Bootstrapper.
     */
    'bootstrapper' => null,

    /**
     * Configure aliases command.
     */
    'aliases' => [
        /**
         * Name of the generated file.
         */
        'file_name' => 'ide_helper/aliases.php',
    ],

    /**
     * Configure models command.
     */
    'models' => [
        /**
         * Which directories to scan models.
         */
        'directories' => ['app'],

        /**
         * Name of the generated file in addition to the php docblocks.
         */
        'file_name' => 'ide_helper/models.php',

        /**
         * Insert a mixin annotation to the model class instead of writing the full docblock.
         */
        'use_mixin' => false,

        /**
         *  When using the mixin annotation, you may still want to add model attributes to the docblock.
         *  Setting this to true will add the attributes to the model docblock.
         */
        'mixin_attributes' => false,

        /**
         * When using the mixin annotation, some completions may not be available. We can help PHPStorm to understand
         * what's going on by generating a meta file.
         */
        'mixin_meta' => 'ide_helper/.phpstorm.meta.php/models.php',

        /**
         * List of the extensions you want to use to tweak the way models are resolved
         * The extensions must implement \Soyhuce\NextIdeHelper\Contracts\ModelResolver.
         */
        'extensions' => [],

        /**
         * Add override to define manually attribute type for some models.
         *
         * Overrides should be declared as follow :
         * MyModel::class => [
         *      'attribute' => 'type'
         * ]
         *
         * Prepend the type with '?' to mark it as nullable.
         */
        'overrides' => [],

        /**
         * Use Larastan friendly docblock when possible.
         */
        'larastan_friendly' => false,

        /**
         * For convenience, model timestamps are by default considered as non-nullable even if they are nullable in the database.
         * Setting this to true will force the timestamps to be documented as nullable.
         */
        'nullable_timestamps' => false,

        'generic_builder' => [
            EloquentBuilder::class,
        ],
    ],

    /**
     * Configure macros command.
     */
    'macros' => [
        /**
         * Which directories to scan macroable classes.
         */
        'directories' => ['app', 'vendor'],

        /**
         * Name of the generated file.
         */
        'file_name' => 'ide_helper/macros.php',
    ],

    /**
     * Configure meta command.
     */
    'meta' => [
        /**
         * Name of the generated file.
         * Be sure the file is within a .phpstorm.meta.php directory.
         */
        'file_name' => 'ide_helper/.phpstorm.meta.php/helpers.php',
    ],

    /**
     * Configure factories command.
     */
    'factories' => [
        /**
         * Which directories to scan factories.
         */
        'directories' => ['database/factories'],

        /**
         * List of the extensions you want to use to tweak the way models are resolved
         * The extensions must implement \Soyhuce\NextIdeHelper\Contracts\FactoryResolver.
         */
        'extensions' => [],
    ],
];
