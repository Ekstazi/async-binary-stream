<?php

namespace ekstazi\stream\binary\test;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\InMemoryStream;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\PendingReadError;
use Amp\Delayed;
use Amp\PHPUnit\AsyncTestCase;
use ekstazi\stream\binary\ByteReader;
use PHPinnacle\Buffer\ByteBuffer;

class ByteReaderTest extends AsyncTestCase
{
    public function testGetOriginalStream()
    {
        $stream = $this->createStub(InputStream::class);
        $reader = new ByteReader($stream);
        self::assertEquals($stream, $reader->getOriginalStream());
    }

    public function testReadBytesSuccess()
    {
        $stream = new InMemoryStream('test');
        $reader = new ByteReader($stream);
        /** @var ByteBuffer $buffer */
        $buffer = yield $reader->readBytes(4);

        self::assertInstanceOf(ByteBuffer::class, $buffer);
        self::assertEquals(4, $buffer->size());
        self::assertEquals('test', $buffer->flush());
    }

    public function testReadBytesError()
    {
        $stream = new InMemoryStream('test');
        $reader = new ByteReader($stream);

        $this->expectException(ClosedException::class);
        $this->expectExceptionMessage('Cannot read bytes because stream was closed');

        /** @var ByteBuffer $buffer */
        $buffer = yield $reader->readBytes(5);
    }

    public function testReadAfterCancel()
    {
        $stream = new InMemoryStream('test');
        $reader = new ByteReader($stream);
        /** @var ByteBuffer $buffer */
        yield $reader->readBytes(3);
        $reader->cancel();

        $this->expectException(ClosedException::class);
        $this->expectExceptionMessage('The stream was cancelled');

        yield $reader->readBytes(1);
    }


    public function testReadPendingError()
    {
        $stream = $this->createMock(InputStream::class);
        $stream->expects(self::once())
            ->method('read')
            ->willReturn(new Delayed(100, 'test'));

        $reader = new ByteReader($stream);
        $reader->readBytes(4);
        $this->expectException(PendingReadError::class);
        $reader->readBytes(4);
    }

    public function testCancel()
    {
        $stream = new InMemoryStream('test123');
        $reader = new ByteReader($stream);


        /** @var ByteBuffer $buffer */
        $buffer = yield $reader->readBytes(4);
        $rest = $reader->cancel();
        self::assertInstanceOf(ByteBuffer::class, $rest);
        self::assertEquals(3, $rest->size());
        self::assertEquals('123', $rest->flush());
    }

    public function testCancelAfterCancel()
    {
        $stream = new InMemoryStream('test123');
        $reader = new ByteReader($stream);


        /** @var ByteBuffer $buffer */
        yield $reader->readBytes(4);
        $reader->cancel();

        $this->expectException(ClosedException::class);
        $this->expectExceptionMessage('The stream was cancelled');
        $reader->cancel();
    }
}
