<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Tests\ResetsFixtures;
use Soyhuce\NextIdeHelper\Tests\TestCase;
use Soyhuce\NextIdeHelper\Tests\UsesFixtures;

uses(
    TestCase::class,
    ResetsFixtures::class,
)->in(__DIR__ . '/Feature');

uses(
    TestCase::class,
    UsesFixtures::class,
)->in(__DIR__ . '/Unit');
