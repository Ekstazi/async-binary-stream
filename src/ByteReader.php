<?php

namespace ekstazi\stream\binary;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\PendingReadError;
use Amp\Promise;
use PHPinnacle\Buffer\ByteBuffer;
use function Amp\call;

final class ByteReader implements BinaryReader
{
    /**
     * @var InputStream
     */
    private $stream;

    private $buffer;
    private $pendingRead;
    /**
     * @var bool
     */
    private $isCancelled;

    public function __construct(InputStream $stream, string $buffer = '')
    {
        $this->stream = $stream;
        $this->buffer = new ByteBuffer($buffer);
    }

    /**
     * Return original stream.
     * @return InputStream
     */
    public function getOriginalStream(): InputStream
    {
        return $this->stream;
    }

    /**
     * Read byte buffer with specified length.
     * @param int $length the length to read
     * @return Promise<ByteBuffer>
     * @throws ClosedException
     * @throws PendingReadError
     */
    public function readBytes(int $length): Promise
    {
        if ($this->isCancelled) {
            throw new ClosedException('The stream was cancelled');
        }
        if ($this->pendingRead) {
            throw new PendingReadError();
        }
        $this->pendingRead = true;
        return call(function () use ($length) {
            while (\strlen($this->buffer) < $length) {
                $readed = yield $this->stream->read();

                if ($readed === null) {
                    throw new ClosedException('Cannot read bytes because stream was closed');
                }

                $this->buffer->append($readed);
            }

            $read = $this->buffer->slice($length);
            $this->buffer = $this->buffer->discard($length);
            $this->pendingRead = false;
            return $read;
        });
    }

    /**
     * Return rest buffer of reader.
     * @return ByteBuffer
     * @throws ClosedException
     */
    public function cancel(): ByteBuffer
    {
        if ($this->isCancelled) {
            throw new ClosedException('The stream was cancelled');
        }
        $this->isCancelled = true;
        $buffer = $this->buffer;
        $this->buffer = null;
        return $buffer;
    }
}
