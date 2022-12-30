<?php declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();
    $ecsConfig->paths([__DIR__ . '/src']);

    $ecsConfig->rules([
        \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class
    ]);

    $ecsConfig->skip([
        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class,
        \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class
    ]);

    $ecsConfig->sets([
        SetList::SPACES,
        SetList::CLEAN_CODE,
        SetList::COMMENTS,
        SetList::COMMON,
        SetList::CONTROL_STRUCTURES,
        SetList::NAMESPACES,
        SetList::STRICT,
        SetList::SYMPLIFY,
        SetList::ARRAY,
        SetList::DOCBLOCK,
        SetList::PSR_12,
    ]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class, [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
        ]
    ]);

    $ecsConfig->ruleWithConfiguration(\PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class, [
        'lineLimit' => 100,
        'absoluteLineLimit' => 120
    ]);
};
