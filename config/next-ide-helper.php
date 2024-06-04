<?php declare(strict_types=1);

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
        'file_name' => '_ide_aliases.php',
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
        'file_name' => '_ide_models.php',

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
         * Always use nullable for timestamps defined in the model. For example: created_at, updated_at.
         */
        'nullable_timestamps' => true,
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
        'file_name' => '_ide_macros.php',
    ],

    /**
     * Configure meta command.
     */
    'meta' => [
        /**
         * Name of the generated file.
         */
        'file_name' => '.phpstorm.meta.php',
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
