<?php

namespace ekstazi\stream\binary;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\PendingReadError;
use Amp\Promise;
use PHPinnacle\Buffer\ByteBuffer;

/**
 * Read binary data from stream
 * Interface BinaryReader.
 * @package ekstazi\stream\binary
 */
interface BinaryReader
{
    /**
     * Return original stream.
     * @return InputStream
     */
    public function getOriginalStream(): InputStream;

    /**
     * Read byte buffer with specified length.
     * @param int $length the length to read
     * @return Promise<ByteBuffer>
     * @throws ClosedException
     * @throws PendingReadError
     */
    public function readBytes(int $length): Promise;

    /**
     * Return rest buffer of reader.
     * @return ByteBuffer
     * @throws ClosedException
     */
    public function cancel(): ByteBuffer;
}
