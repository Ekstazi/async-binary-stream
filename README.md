# async-binary-stream
Set of amphp streams to work with binary data. It can read [ByteBuffer](https://github.com/phpinnacle/buffer) with specified length and write it.
# Installation
This package can be installed as a Composer dependency.

`composer require ekstazi/async-binary-stream`
# Requirements
PHP 7.2+

# Usage
## For reading and parsing data
```php
use \ekstazi\stream\binary\ByteReader;
use \Amp\ByteStream\InputStream;
use \PHPinnacle\Buffer\ByteBuffer;

/** @var InputStream $inputStream */
$reader = new ByteReader($inputStream);
/** @var ByteBuffer $buffer */
$buffer = yield $reader->readBytes(4);

$opCode = $buffer->consumeInt16();
$mask = $buffer->consumeInt16();
```

## For writing data
```php
use \ekstazi\stream\binary\ByteWriter;
use \Amp\ByteStream\OutputStream;
use \PHPinnacle\Buffer\ByteBuffer;

$opCode = 2;
$mask = 1;

/** @var OutputStream $outputStream */
$writer = new ByteWriter($outputStream);

/** @var ByteBuffer $buffer */
$buffer = new ByteBuffer();
$buffer->appendInt16($opCode);
$buffer->appendInt16($mask);

yield $writer->writeBytes($buffer);
```
## For recording read data
```php
use \ekstazi\stream\binary\ByteReader;
use \ekstazi\stream\binary\ByteRecorder;

use \Amp\ByteStream\InputStream;
use \PHPinnacle\Buffer\ByteBuffer;

/** @var InputStream $inputStream */
$reader = new ByteReader($inputStream);
$recorder = new ByteRecorder($reader);

$recorder->startRecord();
/** @var ByteBuffer $buffer */
$buffer = yield $reader->readBytes(4);

$opCode = $buffer->consumeInt16();
$mask = $buffer->consumeInt16();
// ....
$buffer = yield $recorder->readBytes(12);
// ....

/** @var ByteBuffer $recorded  The data recorded after startRecord*/
$recorded = $recorder->stopRecord();
```
