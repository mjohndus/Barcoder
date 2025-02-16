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

class ITF
{
    protected const I25_ALPHABET = [
                '0' => [1, 1, 2, 2, 1],
                '1' => [2, 1, 1, 1, 2],
                '2' => [1, 2, 1, 1, 2],
                '3' => [2, 2, 1, 1, 1],
                '4' => [1, 1, 2, 1, 2],
                '5' => [2, 1, 2, 1, 1],
                '6' => [1, 2, 2, 1, 1],
                '7' => [1, 1, 1, 2, 2],
                '8' => [2, 1, 1, 2, 1],
                '9' => [1, 2, 1, 2, 1],
                'A' => [1, 1],
                'Z' => [2, 1],
        ];

    protected const S25_ALPHABET = [
                '0' => [1, 1, 1, 1, 3, 1, 3, 1, 1, 1],
                '1' => [3, 1, 1, 1, 1, 1, 1, 1, 3, 1],
                '2' => [1, 1, 3, 1, 1, 1, 1, 1, 3, 1],
                '3' => [3, 1, 3, 1, 1, 1, 1, 1, 1, 1],
                '4' => [1, 1, 1, 1, 3, 1, 1, 1, 3, 1],
                '5' => [3, 1, 1, 1, 3, 1, 1, 1, 1, 1],
                '6' => [1, 1, 3, 1, 3, 1, 1, 1, 1, 1],
                '7' => [1, 1, 1, 1, 1, 1, 3, 1, 3, 1],
                '8' => [3, 1, 1, 1, 1, 1, 3, 1, 1, 1],
                '9' => [1, 1, 3, 1, 1, 1, 3, 1, 1, 1],
        ];

    /**
     * Calculate the checksum
     *
     * @param string $data Code to represent.
     *
     * @return int
     */
    protected function getChecksum(string $data): int
    {
        $clen = strlen($data);
        $sum = 0;
        for ($idx = 0; $idx < $clen; $idx += 2) {
             $sum += (int) $data[$idx];
        }

        $sum *= 3;
        for ($idx = 1; $idx < $clen; $idx += 2) {
             $sum += (int) $data[$idx];
        }

        $check = $sum % 10;
        if ($check > 0) {
            return 10 - $check;
        }

        return $check;
    }

    /**
     * Format code
     */
    protected function formatCode(string $data): string
    {
        $data = $data . $this->getChecksum($data);
        return $data;
    }

    /**
     * @return array<mixed>
     */
    protected function i25setbars(string $data): array
    {
        $blocks = [];
        /* Quiet zone, start. */
        $blocks[] = [
                'm' => [[2, 10, 0]]
        ];
        $blocks[] = [
                'm' => [
                        [1, 1, 1],
                        [0, 1, 1],
                        [1, 1, 1],
                        [0, 1, 1],
                ]
        ];
        /* Data. */
        for ($i = 0, $n = strlen($data); $i < $n; $i += 2) {
                $cc1 = substr($data, $i, 1);
                $cc2 = substr($data, $i + 1, 1);
                $bb1 = $this::I25_ALPHABET[$cc1];
                $bb2 = $this::I25_ALPHABET[$cc2];
                $blocks[] = [
                        'm' => [
                                [1, 1, $bb1[0]],
                                [0, 1, $bb2[0]],
                                [1, 1, $bb1[1]],
                                [0, 1, $bb2[1]],
                                [1, 1, $bb1[2]],
                                [0, 1, $bb2[2]],
                                [1, 1, $bb1[3]],
                                [0, 1, $bb2[3]],
                                [1, 1, $bb1[4]],
                                [0, 1, $bb2[4]],
                        ],
                        'l' => [$cc1 . $cc2]
                ];
        }
        /* End, quiet zone. */
        $blocks[] = [
                'm' => [
                        [1, 1, 2],
                        [0, 1, 1],
                        [1, 1, 1],
                ]
        ];
        $blocks[] = [
                'm' => [[2, 10, 0]]
        ];
        return $blocks;
    }

    /**
     * @return array<mixed>
     */
    protected function s25setbars(string $data): array
    {
        $blocks = [];
        /* Quiet zone, start. */
        $blocks[] = [
                'm' => [[2, 10, 0]]
        ];
        $blocks[] = [
                'm' => [
                        [1, 3, 1],
                        [0, 1, 1],
                        [1, 3, 1],
                        [0, 1, 1],
                        [1, 1, 1],
                        [0, 1, 1],
                ]
        ];
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                 $digit = substr($data, $i, 1);
            if ($i % 2 == 0) {
                $cc1 = substr($data, $i, 1);
                $cc2 = substr($data, $i + 1, 1);
            } else {
                $cc1 = '';
                $cc2 = '';
            }
                    $blocks[] = [
                     'm' => [
                            [1, $this::S25_ALPHABET[$digit][0], 1],
                            [0, $this::S25_ALPHABET[$digit][1], 1],
                            [1, $this::S25_ALPHABET[$digit][2], 1],
                            [0, $this::S25_ALPHABET[$digit][3], 1],
                            [1, $this::S25_ALPHABET[$digit][4], 1],
                            [0, $this::S25_ALPHABET[$digit][5], 1],
                            [1, $this::S25_ALPHABET[$digit][6], 1],
                            [0, $this::S25_ALPHABET[$digit][7], 1],
                            [1, $this::S25_ALPHABET[$digit][8], 1],
                            [0, $this::S25_ALPHABET[$digit][9], 1],
                     ],
                     'l' => [$cc1 . $cc2]
                    ];
        }

        /* End, quiet zone. */
        $blocks[] = [
                'm' => [
                        [1, 3, 1],
                        [0, 1, 1],
                        [1, 1, 1],
                        [0, 1, 1],
                        [1, 3, 1],
                ]
        ];
        $blocks[] = [
                'm' => [[2, 10, 0]]
        ];
        return $blocks;
    }

    /* - - - - I25 ENCODER - - - - */

    /**
     * @return array<mixed>
     */
    public function i25_encode(string $data): array
    {
            $data = (string) preg_replace('/[^0-9]/', '', $data);

        if (strlen($data) % 2 != 0) {
            // add leading zero if code-length is odd
            $data = '0' . $data;
        }
            $blocks = $this::i25setbars($data);

            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

    /* - - - - I25+ ENCODER - - - - */

    /**
     *
     * @return array<mixed>
     */
    public function i25check_encode(string $data): array
    {
            $data = (string) preg_replace('/[^0-9]/', '', $data);
            $data = $this->formatCode($data);

        if (strlen($data) % 2 != 0) {
            // add leading zero if code-length is odd
            $data = '0' . $data;
        }
            $blocks = $this::i25setbars($data);

            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

    /* - - - - s25 ENCODER - - - - */

    /**
     * @return array<mixed>
     */
    public function s25_encode(string $data): array
    {
            $data = (string) preg_replace('/[^0-9]/', '', $data);
//                $data = $this->formatCode($data);

        if (strlen($data) % 2 != 0) {
            // add leading zero if code-length is odd
            $data = '0' . $data;
        }
            $blocks = $this::s25setbars($data);

            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

    /* - - - - s25+ ENCODER - - - - */

    /**
     * @return array<mixed>
     */
    public function s25check_encode(string $data): array
    {
            $data = (string) preg_replace('/[^0-9]/', '', $data);
            $data = $this->formatCode($data);

        if (strlen($data) % 2 != 0) {
            // add leading zero if code-length is odd
            $data = '0' . $data;
        }
            $blocks = $this::s25setbars($data);

            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }
}
