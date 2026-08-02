<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'exclude' => [
        'build',
        'stubs',
        'vendor',
        'src/Console/ValidateCommand.php',
    ],
    'remove' => [
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
    ],
    'config' => [
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 120,
        ],
    ],
    'requirements' => [
        'min-quality' => 80,
        'min-complexity' => 75,
        'min-architecture' => 80,
        'min-style' => 90,
    ],
];
