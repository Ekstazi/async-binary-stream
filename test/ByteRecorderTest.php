<?php

namespace ekstazi\stream\binary\test;

use Amp\ByteStream\InMemoryStream;
use Amp\ByteStream\InputStream;
use Amp\PHPUnit\AsyncTestCase;
use Amp\Success;
use ekstazi\stream\binary\BinaryReader;
use ekstazi\stream\binary\ByteReader;
use ekstazi\stream\binary\ByteRecorder;
use PHPinnacle\Buffer\ByteBuffer;

class ByteRecorderTest extends AsyncTestCase
{
    public function testGetOriginalStream()
    {
        $inputStream = $this->createMock(InputStream::class);

        $original = $this->createMock(BinaryReader::class);
        $original->expects(self::once())
            ->method('getOriginalStream')
            ->willReturn($inputStream);

        $reader = new ByteRecorder($original);
        self::assertEquals($reader->getOriginalStream(), $inputStream);
    }

    public function testReadBytes()
    {
        $byteBuffer = new ByteBuffer('1234');

        $original = $this->createMock(BinaryReader::class);
        $original->expects(self::once())
            ->method('readBytes')
            ->with(4)
            ->willReturn(new Success($byteBuffer));

        $reader = new ByteRecorder($original);
        $readed = yield $reader->readBytes(4);
        self::assertEquals($byteBuffer, $readed);
    }

    public function testReadRecorded()
    {
        $stream = new InMemoryStream('12345678');
        $reader = new ByteReader($stream);
        $recorder = new ByteRecorder($reader);

        $recorder->startRecord();
        $buffer = yield $recorder->readBytes(4);
        self::assertEquals('1234', $buffer->flush());

        $buffer = yield $recorder->readBytes(4);
        self::assertEquals('5678', $buffer->flush());

        $recorded = $recorder->stopRecord();
        self::assertEquals('12345678', $recorded->flush());
    }

    public function testCancel()
    {
        $stream = new InMemoryStream('test123');
        $reader = new ByteReader($stream);
        $recorder = new ByteRecorder($reader);
        $recorder->startRecord();

        /** @var ByteBuffer $buffer */
        $buffer = yield $recorder->readBytes(4);
        $rest = $recorder->cancel();
        self::assertInstanceOf(ByteBuffer::class, $rest);
        self::assertEquals(3, $rest->size());
        self::assertEquals('123', $rest->flush());

        $recorded = $recorder->stopRecord();
        self::assertNull($recorded);
    }
}
