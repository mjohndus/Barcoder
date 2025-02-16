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

class Pharma
{
    /* - - - - PHARMA ENCODER - - - - */

    /**
     * @return array<string, list<array<string, array<int, list<int>|string>>>|string>
     */
    public function pharma_encode(string $data): array
    {
            $data = (int) preg_replace('/[^0-9]/', '', $data);

            $char = '';
            $blocks = [];
        while ($data > 0) {
            if (($data % 2) == 0) {
                $blocks[] = [
                        'm' => [
                                [0, 2, 1],
                                [1, 3, 1],
                        ],
                        'l' => [$char]
                ];
                $data -= 2;
            } else {
                $blocks[] = [
                        'm' => [
                                [0, 2, 1],
                                [1, 1, 1],
                        ],
                        'l' => [$char]
                ];
                --$data;
            }
            $data /= 2;
        }
            $blocks = array_reverse($blocks);
            unset($blocks[0]['m'][0]);
            return ['g' => 'l', 'b' => $blocks];
    }
}
