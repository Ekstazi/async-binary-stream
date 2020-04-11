<?php

namespace ekstazi\stream\binary;

use Amp\ByteStream\OutputStream;
use Amp\Promise;
use PHPinnacle\Buffer\ByteBuffer;

/**
 * Write binary data to stream
 * Interface BinaryWriter.
 * @package ekstazi\stream\binary
 */
interface BinaryWriter
{
    public function getOriginalStream(): OutputStream;

    /**
     * @param ByteBuffer $buffer
     * @return Promise
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function writeBytes(ByteBuffer $buffer);

    /**
     * @param ByteBuffer $buffer
     * @return Promise
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function end(ByteBuffer $buffer = null);
}
