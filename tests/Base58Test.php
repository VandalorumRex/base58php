<?php
declare(strict_types=1);

use StephenHill\Base58;
use StephenHill\BCMathService;
use StephenHill\GMPService;
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase
{
    /**
     * @dataProvider encodingsProvider
     */
    public function testEncode($string, $encoded, $instance)
    {
        $string = (string) $string;
        $encoded = (string) $encoded;

        $this->assertSame($encoded, $instance->encode($string));
    }

    /**
     * @dataProvider encodingsProvider
     */
    public function testDecode($string, $encoded, $instance)
    {
        $string = (string) $string;
        $encoded = (string) $encoded;

        $this->assertSame($string, $instance->decode($encoded));
    }

    public function encodingsProvider()
    {
        $instances = array(
            new Base58(null, new BCMathService()),
            new Base58(null, new GMPService())
        );

        $tests = array(
            array('', ''),
            array('1', 'r'),
            array('a', '2g'),
            array('bbb', 'a3gV'),
            array('ccc', 'aPEr'),
            array('hello!', 'tzCkV5Di'),
            array('Hello World', 'JxF12TrwUP45BMd'),
            array('this is a test', 'jo91waLQA1NNeBmZKUF'),
            array('the quick brown fox', 'NK2qR8Vz63NeeAJp9XRifbwahu'),
            array('THE QUICK BROWN FOX', 'GRvKwF9B69ssT67JgRWxPQTZ2X'),
            array('simply a long string', '2cFupjhnEsSn59qHXstmK2ffpLv2'),
            array("\x00\x61", '12g'),
            array("\x00", '1'),
            array("\x00\x00", '11'),
            array('0248ac9d3652ccd8350412b83cb08509e7e4bd41', '3PtvAWwSMPe2DohNuCFYy76JhMV3rhxiSxQMbPBTtiPvYvneWu95XaY')
        );

        $return = array();

        foreach ($instances as $instance) {
            foreach ($tests as $test) {
                $test[] = $instance;
                $return[] = $test;
            }
        }

        return $return;
    }

    /**
     * @expectedException        TypeError
     * @expectedExceptionMessage Argument $alphabet must be a string.
     */
    public function testConstructorTypeException()
    {
        $this->expectException(\TypeError::class);
        new Base58(intval(123));

    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Argument $alphabet must contain 58 characters.
     */
    public function testConstructorLengthException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Base58('');
    }

    /**
     * @expectedException        TypeError
     * @expectedExceptionMessage Argument $string must be a string.
     */
    public function testEncodeTypeException()
    {
        $this->expectException(\TypeError::class);
        $base58 = new Base58();
        $base58->encode(intval(123));
    }

    /**
     * @expectedException        TypeError
     * @expectedExceptionMessage Argument $base58 must be a string.
     */
    public function testDecodeTypeException()
    {
        $this->expectException(\TypeError::class);
        $base58 = new Base58();
        $base58->decode(intval(123));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Argument $base58 contains invalid characters.
     */
    public function testInvalidBase58()
    {
        $this->expectException(\InvalidArgumentException::class);
        $base58 = new Base58();
        $base58->decode("This isn't valid base58");
    }
}
