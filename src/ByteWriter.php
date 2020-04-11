<?php

namespace ekstazi\stream\binary;

use Amp\ByteStream\OutputStream;
use Amp\Promise;
use PHPinnacle\Buffer\ByteBuffer;

final class ByteWriter implements BinaryWriter
{

    /**
     * @var OutputStream
     */
    private $stream;

    public function __construct(OutputStream $stream)
    {
        $this->stream = $stream;
    }

    public function getOriginalStream(): OutputStream
    {
        return $this->stream;
    }

    /**
     * @param ByteBuffer $buffer
     * @return Promise
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function writeBytes(ByteBuffer $buffer): Promise
    {
        return $this->stream->write($buffer);
    }

    /**
     * @param ByteBuffer $buffer
     * @return Promise
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function end(ByteBuffer $buffer = null): Promise
    {
        return $this->stream->end($buffer ?? '');
    }
}
