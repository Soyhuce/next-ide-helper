<?php

return [

    /**
     * The bootstrapper use to boot your application before running analyses.
     * You may want, for exemple, to setup a database for your tenant model in
     * case of multitenant application.
     * The class must implement \Soyhuce\IdeHelper\Console\Bootstrapper
     */
    'bootstrapper' => null,

    /**
     * Configure models command
     */
    'models' => [

        /**
         * Which directories to scan models
         */
        'directories' => ['app'],

        /**
         * Name of the generated file in addition to the php docblocks
         */
        'file_name' => '_ide_models.php',

        /**
         * List of the extensions you want to use to tweak the way models are resolved
         * The extensions must implement \Soyhuce\IdeHelper\Domain\Actions\ModelResolver
         *
         * Some extensions are already available :
         * - Soyhuce\IdeHelper\Extensions\SpatieEnumResolver
         * - Soyhuce\IdeHelper\Extensions\VirtualAttributeResolver
         */
        'extensions' => [],
    ],

    /**
     * Configure macros command
     */
    'macros' => [

        /**
         * Which directories to scan macroable classes
         */
        'directories' => ['app', 'vendor'],

        /**
         * Name of the generated file
         */
        'file_name' => '_ide_macros.php',
    ],

    /**
     * Configure meta command
     */
    'meta' => [

        /**
         * Name of the generated file
         */
        'file_name' => '.phpstorm.meta.php',
    ]
];
