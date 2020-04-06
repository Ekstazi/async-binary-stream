<?php

namespace ekstazi\stream\binary;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\InputStream;
use Amp\Promise;
use PHPinnacle\Buffer\ByteBuffer;
use function Amp\call;

class ByteRecorder extends ByteReader
{
    /**
     * @var ByteReader
     */
    private $byteReader;

    /**
     * @var ByteBuffer
     */
    private $recordBuffer;

    public function __construct(ByteReader $byteReader)
    {
        $this->byteReader = $byteReader;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalStream(): InputStream
    {
        return $this->byteReader->getOriginalStream();
    }

    /**
     * Reset the current record buffer and start new record.
     */
    public function startRecord()
    {
        $this->recordBuffer = new ByteBuffer();
    }

    /**
     * @inheritDoc
     */
    public function readBytes(int $length): Promise
    {
        return call(function () use ($length) {
            $buffer = yield $this->byteReader->readBytes($length);
            if (!$this->recordBuffer) {
                return $buffer;
            }

            $this->recordBuffer->append($buffer);
            return $buffer;
        });
    }

    /**
     * Clear buffer and stop recording data.
     * @return ByteBuffer|null recorded data if record is started
     */
    public function stopRecord(): ?ByteBuffer
    {
        $buffer = $this->recordBuffer;
        $this->recordBuffer = null;
        return $buffer;
    }

    /**
     * Stop recording and return rest buffer of reader.
     * @return ByteBuffer
     * @throws ClosedException
     */
    public function cancel(): ByteBuffer
    {
        $this->stopRecord();
        return $this->byteReader->cancel();
    }
}
