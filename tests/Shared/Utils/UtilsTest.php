<?php declare(strict_types=1);

namespace Tests\Shared\Utils;

use Omatech\MageCore\Shared\Utils\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /** @test */
    public function givenEmptyStringWhenCallIsEmptyThenTrue(): void
    {
        $this->assertTrue(Utils::isEmpty(''));
    }

    /** @test */
    public function givenNotEmptyStringWhenCallIsEmptyThenFalse(): void
    {
        $this->assertFalse(Utils::isEmpty('test'));
    }

    /** @test */
    public function givenNullWhenCallIsEmptyThenTrue(): void
    {
        $this->assertTrue(Utils::isEmpty(null));
    }

    /** @test */
    public function givenEmptyArrayWhenCallIsEmptyThenTrue(): void
    {
        $this->assertTrue(Utils::isEmpty([]));
    }

    /** @test */
    public function givenNonEmptyArrayWhenCallIsEmptyThenFalse(): void
    {
        $this->assertFalse(Utils::isEmpty(['test']));
    }

    /** @test */
    public function givenFalseWhenCallIsEmptyThenFalse(): void
    {
        $this->assertFalse(Utils::isEmpty(false));
    }

    /** @test */
    public function givenTrueWhenCallIsEmptyThenFalse(): void
    {
        $this->assertFalse(Utils::isEmpty(true));
    }

    /** @test */
    public function givenZeroWhenCallIsEmptyThenFalse(): void
    {
        $this->assertFalse(Utils::isEmpty(0));
    }
}
