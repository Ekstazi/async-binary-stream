<?php

namespace ekstazi\stream\binary\test;

use Amp\ByteStream\OutputStream;
use Amp\PHPUnit\AsyncTestCase;
use Amp\Success;
use ekstazi\stream\binary\ByteWriter;
use PHPinnacle\Buffer\ByteBuffer;

class ByteWriterTest extends AsyncTestCase
{
    public function testGetOriginalStream()
    {
        $stream = $this->createStub(OutputStream::class);
        $reader = new ByteWriter($stream);
        self::assertEquals($stream, $reader->getOriginalStream());
    }


    public function testWriteBytes()
    {
        $stream = $this->createMock(OutputStream::class);
        $stream->expects(self::once())
            ->method('write')
            ->with('test')
            ->willReturn(new Success());

        $writer = new ByteWriter($stream);
        yield $writer->writeBytes(new ByteBuffer('test'));
    }

    public function testEndWithData()
    {
        $stream = $this->createMock(OutputStream::class);
        $stream->expects(self::once())
            ->method('end')
            ->with('test')
            ->willReturn(new Success());

        $writer = new ByteWriter($stream);
        yield $writer->end(new ByteBuffer('test'));
    }

    public function testEndWithoutData()
    {
        $stream = $this->createMock(OutputStream::class);
        $stream->expects(self::once())
            ->method('end')
            ->with(null)
            ->willReturn(new Success());

        $writer = new ByteWriter($stream);
        yield $writer->end();
    }
}
