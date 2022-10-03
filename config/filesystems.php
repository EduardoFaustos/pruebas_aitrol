<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
     */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
     */

    'cloud'   => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
     */

    'disks'   => [

        'local'                  => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'public'                 => [
            'driver'     => 'local',
            'root'       => storage_path('app/avatars'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'agenda_scan'            => [
            'driver'     => 'local',
            'root'       => storage_path('app/agenda_scan'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        //filesystem
        'logo'                   => [
            'driver'     => 'local',
            'root'       => storage_path('app/logo'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'hc_agenda'              => [
            'driver'     => 'local',
            'root'       => storage_path('app/hc_agenda'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        //filesystem
        'hc'                     => [
            'driver'     => 'local',
            'root'       => storage_path('app/hc'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'hc_ima'                 => [
            'driver'     => 'local',
            'root'       => storage_path('app/hc_ima'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'imagenes_sci'           => [
            'driver'     => 'local',
            'root'       => 'Z:/Imagenes/',
            'url'        => 'file:///Z:/Imagenes/',
            'visibility' => 'public',
        ],

        's3'                     => [
            'driver' => 's3',
            'key'    => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
//manuales
        'manual'                 => [
            'driver'     => 'local',
            'root'       => storage_path('app/manual'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'procedimiento_completo' => [
            'driver'     => 'local',
            'root'       => storage_path('app/procedimiento_completo'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'biopsias'               => [
            'driver'     => 'local',
            'root'       => storage_path('app/biopsias'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'archivos_nomina'        => [
            'driver'     => 'local',
            'root'       => storage_path('app/archivos_nomina'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'videos'                 => [
            'driver' => 'local',
            'root'   => public_path('uploads'),
        ],

    ],

];
