<?php

/*
 * Set specific configuration variables here
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    | Avatar use Intervention Image library to process image.
    | Meanwhile, Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */
    'driver' => env('IMAGE_DRIVER', 'gd'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    | Control caching behavior for avatars
    |
    */
    'cache' => [
        'enabled' => env('AVATAR_CACHE_ENABLED', true),
        'key_prefix' => 'avatar_',
        'duration' => env('AVATAR_CACHE_DURATION', 86400),
    ],

    // Initial generator class
    'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,

    // Whether all characters supplied must be replaced with their closest ASCII counterparts
    'ascii' => false,

    // Image shape: circle or square
    'shape' => 'circle',

    // Image width, in pixel (tasarımda yaklaşık 60-70px görünüyor)
    'width' => 70,

    // Image height, in pixel
    'height' => 70,

    // Responsive SVG
    'responsive' => false,

    // Number of characters used as initials
    'chars' => 2,

    // font size (oran olarak küçülttüm)
    'fontSize' => 32,

    // convert initial letter in uppercase (tasarımda büyük harfler var)
    'uppercase' => true,

    // Right to Left (RTL)
    'rtl' => false,

    // Fonts used to render text
    'fonts' => [__DIR__.'/../fonts/OpenSans-Bold.ttf'],

    // List of foreground colors (beyaz text)
    'foregrounds' => [
        '#FFFFFF',
    ],

    // List of background colors (tasarımdaki gibi canlı renkler)
    'backgrounds' => [
        '#FF5722', // Turuncu-kırmızı (Mi için)
        '#3F51B5', // Mavi-mor (Ro için)
        '#E91E63', // Pembe
        '#9C27B0', // Mor
        '#673AB7', // Koyu mor
        '#2196F3', // Mavi
        '#03A9F4', // Açık mavi
        '#00BCD4', // Cyan
        '#009688', // Teal
        '#4CAF50', // Yeşil
        '#8BC34A', // Açık yeşil
        '#FFC107', // Sarı
        '#FF9800', // Turuncu
        '#795548', // Kahverengi
        '#607D8B', // Blue grey
    ],

    'border' => [
        'size' => 0, // Border yok
        'color' => 'background',
        'radius' => 0,
    ],

    // Theme
    'theme' => ['modern'],

    // Predefined themes
    'themes' => [
        'modern' => [
            'backgrounds' => [
                '#FF5722',
                '#3F51B5',
                '#E91E63',
                '#9C27B0',
                '#673AB7',
                '#2196F3',
                '#03A9F4',
                '#00BCD4',
                '#009688',
                '#4CAF50',
                '#8BC34A',
                '#FFC107',
                '#FF9800',
                '#795548',
                '#607D8B',
            ],
            'foregrounds' => ['#FFFFFF'],
            'fontSize' => 32,
            'width' => 70,
            'height' => 70,
            'shape' => 'circle',
            'chars' => 2,
            'uppercase' => true,
        ],
        'grayscale-light' => [
            'backgrounds' => ['#edf2f7', '#e2e8f0', '#cbd5e0'],
            'foregrounds' => ['#a0aec0'],
        ],
        'grayscale-dark' => [
            'backgrounds' => ['#2d3748', '#4a5568', '#718096'],
            'foregrounds' => ['#e2e8f0'],
        ],
        'colorful' => [
            'backgrounds' => [
                '#f44336',
                '#E91E63',
                '#9C27B0',
                '#673AB7',
                '#3F51B5',
                '#2196F3',
                '#03A9F4',
                '#00BCD4',
                '#009688',
                '#4CAF50',
                '#8BC34A',
                '#CDDC39',
                '#FFC107',
                '#FF9800',
                '#FF5722',
            ],
            'foregrounds' => ['#FFFFFF'],
        ],
        'pastel' => [
            'backgrounds' => [
                '#ef9a9a',
                '#F48FB1',
                '#CE93D8',
                '#B39DDB',
                '#9FA8DA',
                '#90CAF9',
                '#81D4FA',
                '#80DEEA',
                '#80CBC4',
                '#A5D6A7',
                '#E6EE9C',
                '#FFAB91',
                '#FFCCBC',
                '#D7CCC8',
            ],
            'foregrounds' => [
                '#FFF',
            ],
        ],
    ],
];
