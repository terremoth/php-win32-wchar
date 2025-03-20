<?php

namespace Terremoth\Win32;

use FFI;

class WindowsString
{
    public function __construct(private readonly string $str)
    {
    }

    public function toChar(int $size): ?FFI\CData
    {
        $utf8ToStr = iconv("UTF-8", 'UTF-' . $size . 'LE', $this->str);
        $stringBufferSize = $size / 8;
        $length = strlen($utf8ToStr);
        $ffi = FFI::cdef();
        $buffer = $ffi->new("unsigned short[" . ($length / $stringBufferSize + 1) . "]");

        for ($i = 0; $i < $length; $i += $stringBufferSize) {
            $buffer[$i / $stringBufferSize] = unpack("S", substr($utf8ToStr, $i, $stringBufferSize))[1];
        }

        return $buffer;
    }

    public function wchar(): ?FFI\CData
    {
        return $this->toChar(16);
    }
}
