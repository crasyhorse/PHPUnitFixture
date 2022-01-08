<?php

declare(strict_types=1);

namespace CrasyHorse\Tests\Unit\Encoder;

use CrasyHorse\Tests\TestCase;
use CrasyHorse\Tests\Unit\Config\CreateConfiguration;
use CrasyHorse\Testing\Encoder\Base64;

/**
 * @covers CrasyHorse\Testing\Encoder\Base64
 */
class Base64Test extends TestCase
{
    use CreateConfiguration;

    /**
     * @test
     * @group Encoder
     */
    public function encode_returns_the_base64_encoded_value_for_the_given_content(): void
    {
        $encoder = new Base64();

        $expected = ['SGFsbG8gV2VsdA=='];
        $actual = $encoder->encode(['Hallo Welt']);

        $this->assertEquals($expected, $actual);
    }
}
