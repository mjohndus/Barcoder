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

class Codabar
{
    protected const CODABAR_ALPHABET = [
                '0' => [1, 1, 1, 1, 1, 2, 2],
                '1' => [1, 1, 1, 1, 2, 2, 1],
                '4' => [1, 1, 2, 1, 1, 2, 1],
                '5' => [2, 1, 1, 1, 1, 2, 1],
                '2' => [1, 1, 1, 2, 1, 1, 2],
                '-' => [1, 1, 1, 2, 2, 1, 1],
                '$' => [1, 1, 2, 2, 1, 1, 1],
                '9' => [2, 1, 1, 2, 1, 1, 1],
                '6' => [1, 2, 1, 1, 1, 1, 2],
                '7' => [1, 2, 1, 1, 2, 1, 1],
                '8' => [1, 2, 2, 1, 1, 1, 1],
                '3' => [2, 2, 1, 1, 1, 1, 1],
                'C' => [1, 1, 1, 2, 1, 2, 2],
                'D' => [1, 1, 1, 2, 2, 2, 1],
                'A' => [1, 1, 2, 2, 1, 2, 1],
                'B' => [1, 2, 1, 2, 1, 1, 2],
                '*' => [1, 1, 1, 2, 1, 2, 2],
                'E' => [1, 1, 1, 2, 2, 2, 1],
                'T' => [1, 1, 2, 2, 1, 2, 1],
                'N' => [1, 2, 1, 2, 1, 1, 2],
                '.' => [2, 1, 2, 1, 2, 1, 1],
                '/' => [2, 1, 2, 1, 1, 1, 2],
                ':' => [2, 1, 1, 1, 2, 1, 2],
                '+' => [1, 1, 2, 1, 2, 1, 2],
        ];

    /* - - - - CODABAR ENCODER - - - - */

    /**
     * @return array<string, list<array<string, array<int, list<int>|string>>>|string>
     */
    public function codabar_encode(string $data): array
    {
            $data = strtoupper((string) preg_replace(
                '/[^0-9ABCDENTabcdent*.\/:+$-]/',
                '',
                $data
            ));
            $blocks = [];
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            if ($blocks) {
                $blocks[] = [
                        'm' => [[0, 1, 3]]
                ];
            }
                $char = substr($data, $i, 1);
                $block = $this::CODABAR_ALPHABET[$char];
                $blocks[] = [
                        'm' => [
                                [1, 1, $block[0]],
                                [0, 1, $block[1]],
                                [1, 1, $block[2]],
                                [0, 1, $block[3]],
                                [1, 1, $block[4]],
                                [0, 1, $block[5]],
                                [1, 1, $block[6]],
                        ],
                        'l' => [$char]
                ];
        }
            return ['g' => 'l', 'b' => $blocks];
    }
}
