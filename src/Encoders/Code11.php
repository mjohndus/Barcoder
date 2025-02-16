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

class Code11
{
    /**
     * Map characters to barcodes
     *
     * @var array<int|string, string>
     */
    protected const CODE11_BIN = [
        '0' => '111121',
        '1' => '211121',
        '2' => '121121',
        '3' => '221111',
        '4' => '112121',
        '5' => '212111',
        '6' => '122111',
        '7' => '111221',
        '8' => '211211',
        '9' => '211111',
        '-' => '112111',
        'S' => '112211',
    ];

    /* - - - - Code11 ENCODER - - - - */

    /**
     * Calculate the checksum.
     *
     * @param string $data Code to represent.
     *
     * @return string char checksum.
     */
    protected function getChecksum(string $data): string
    {
        $len = strlen($data);
        // calculate check digit C
        $ptr = 1;
        $ccheck = 0;
        for ($pos = ($len - 1); $pos >= 0; --$pos) {
            $digit = $data[$pos];
            $dval = $digit == '-' ? 10 : (int) $digit;

            $ccheck += ($dval * $ptr);
            ++$ptr;
            if ($ptr > 10) {
                $ptr = 1;
            }
        }

        $ccheck %= 11;
        if ($ccheck == 10) {
            $ccheck = '-';
        }

        if ($len <= 10) {
            return ((string) $ccheck);
        }

        // calculate check digit K
        $data .= $ccheck;
        $ptr = 1;
        $kcheck = 0;
        for ($pos = $len; $pos >= 0; --$pos) {
            $digit = $data[$pos];
            $dval = $digit == '-' ? 10 : (int) $digit;

            $kcheck += ($dval * $ptr);
            ++$ptr;
            if ($ptr > 9) {
                $ptr = 1;
            }
        }

        $kcheck %= 11;
        return ((string) $ccheck . $kcheck);
    }

    /**
     * Format code
     *
     * @param string $data Code.
     *
     * @return string char.
     */
    protected function formatCode(string $data): string
    {
        $data = $data . $this->getChecksum($data);
        return $data;
    }

    /**
     * @return array{g: 'l', b: non-empty-list<array{m: array{0:array{1, 1, 1}, 1: array{0, 1, 1}, 2: array{1, 2, 1}, 3: array{0, 2, 1}, 4: array{
     * 1, 1, 1}, 5?: array{0, 1, 1}}} | array{m: array{array{1, non-empty-string, 1}, array{0, non-empty-string, 1}, array{1,
     * non-empty-string, 1}, array{0, non-empty-string, 1}, array{1, non-empty-string, 1}, array{0, non-empty-string, 1}}, l: array{string}}>}
     */
    public function code_11_encode(string $data): array
    {
        $data = (string) preg_replace('/[^0-9]/', '', $data);
        $data = $this->formatCode($data);

        $blocks = [];
        /* left guard */
        $blocks[] = [
                'm' => [
                        [1, 1, 1],
                        [0, 1, 1],
                        [1, 2, 1],
                        [0, 2, 1],
                        [1, 1, 1],
                        [0, 1, 1],
                ]
        ];
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = substr($data, $i, 1);
            $block = $this::CODE11_BIN[$char];
            $blocks[] = [
                    'm' => [
                            [1, $block[0], 1],
                            [0, $block[1], 1],
                            [1, $block[2], 1],
                            [0, $block[3], 1],
                            [1, $block[4], 1],
                            [0, $block[5], 1],
                            ],
                            'l' => [$char]
                        ];
        }
        /* right guard */
        $blocks[] = [
                'm' => [
                        [1, 1, 1],
                        [0, 1, 1],
                        [1, 2, 1],
                        [0, 2, 1],
                        [1, 1, 1],
//                        [0, 1, 1],
                ]
        ];

//echo '<pre>';
//print_r($data);
//echo '</pre>';
        return ['g' => 'l', 'b' => $blocks];
    }
}
