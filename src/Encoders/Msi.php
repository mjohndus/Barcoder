<?php

/****************************************************************************\

barcode.php - Generate barcodes from a single PHP file. MIT license.

Copyright (c) 2016-2018 Kreative Software.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.

\****************************************************************************/

namespace Barcoder\Encoders;

class Msi
{
    /**
     *
     */
    protected const MSI_ALPHABET = [
        '0' => [1, 0, 0, 1, 0, 0, 1, 0, 0, 1, 0, 0],
        '1' => [1, 0, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0],
        '2' => [1, 0, 0, 1, 0, 0, 1, 1, 0, 1, 0, 0],
        '3' => [1, 0, 0, 1, 0, 0, 1, 1, 0, 1, 1, 0],
        '4' => [1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0],
        '5' => [1, 0, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0],
        '6' => [1, 0, 0, 1, 1, 0, 1, 1, 0, 1, 0, 0],
        '7' => [1, 0, 0, 1, 1, 0, 1, 1, 0, 1, 1, 0],
        '8' => [1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 0, 0],
        '9' => [1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0],
        'A' => [1, 1, 0, 1, 0, 0, 1, 1, 0, 1, 0, 0],
        'B' => [1, 1, 0, 1, 0, 0, 1, 1, 0, 1, 1, 0],
        'C' => [1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0],
        'D' => [1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1, 0],
        'E' => [1, 1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 0],
        'F' => [1, 1, 0, 1, 1, 0, 1, 1, 0, 1, 1, 0],
    ];

    /**
     *
     */
    protected const MSI_BIN = [
        '0' => [0, 0, 0, 0],
        '1' => [0, 0, 0, 1],
        '2' => [0, 0, 1, 0],
        '3' => [0, 0, 1, 1],
        '4' => [0, 1, 0, 0],
        '5' => [0, 1, 0, 1],
        '6' => [0, 1, 1, 0],
        '7' => [0, 1, 1, 1],
        '8' => [1, 0, 0, 0],
        '9' => [1, 0, 0, 1],
        'A' => [1, 0, 1, 0],
        'B' => [1, 0, 1, 1],
        'C' => [1, 1, 0, 0],
        'D' => [1, 1, 0, 1],
        'E' => [1, 1, 1, 0],
        'F' => [1, 1, 1, 1],
    ];

    /* - - - - Msi ENCODER - - - - */

    /**
     * Calculate the checksum Modulo 11
     *
     * @param string $data data to represent.
     *
     * @return int char checksum.
     */
    protected function getChecksum(string $data): int
    {
        $clen = strlen($data);
        $pix = 2;
        $check = 0;
        for ($pos = ($clen - 1); $pos >= 0; --$pos) {
            $hex = $data[$pos];
            if (! ctype_xdigit($hex)) {
                continue;
            }

            $check += (hexdec($hex) * $pix);
            ++$pix;
            if ($pix > 7) {
                $pix = 2;
            }
        }

        $check %= 11;
        if ($check > 0) {
            return 11 - $check;
        }

        return $check;
    }

    protected function formatCode(string $data): string
    {
        $data = $data . $this->getChecksum($data);
        return $data;
    }

    /**
     * @return array<string, list<array<string, array<int, list<int>|string>>>|string>
     */
    public function msi_encode(string $data, int $mod): array
    {
        $data = (string) preg_replace('/[^0-9]/', '', $data);
        $data = $mod == 11 ? $this->formatCode($data) : $data;

        $blocks = [];
        /* left guard */
        $blocks[] = [
                'm' => [
                        [1, 1, 1],
                        [1, 1, 1],
                        [0, 1, 1],
                ]
        ];

        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = substr($data, $i, 1);
            $block = $this::MSI_BIN[$char];
            $blocks[] = [
                    'm' => [
                            [1, $block[0] == 0 ? 1 : 2, 1],
                            [0, $block[0] == 0 ? 2 : 1, 1],
                            [1, $block[1] == 0 ? 1 : 2, 1],
                            [0, $block[1] == 0 ? 2 : 1, 1],
                            [1, $block[2] == 0 ? 1 : 2, 1],
                            [0, $block[2] == 0 ? 2 : 1, 1],
                            [1, $block[3] == 0 ? 1 : 2, 1],
                            [0, $block[3] == 0 ? 2 : 1, 1],
                            ],
                            'l' => [$char]
                        ];
        }

        /* right guard */
        $blocks[] = [
                'm' => [
                        [1, 1, 1],
                        [0, 1, 1],
                        [0, 1, 1],
                        [1, 1, 1],
                ]
        ];

//echo '<pre>';
//print_r($data);
//echo '</pre>';
        return ['g' => 'l', 'b' => $blocks];
    }
}
