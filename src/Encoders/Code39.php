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

class Code39
{
    /* - - - - CODE 39 FAMILY ENCODER - - - - */

    /**
     * Map characters to barcodes
     *
     * @var array<int|string, array<int>>
     */
    protected const CODE_39_ALPHABET = [
        '1' => [2, 1, 1, 2, 1, 1, 1, 1, 2],
        '2' => [1, 1, 2, 2, 1, 1, 1, 1, 2],
        '3' => [2, 1, 2, 2, 1, 1, 1, 1, 1],
        '4' => [1, 1, 1, 2, 2, 1, 1, 1, 2],
        '5' => [2, 1, 1, 2, 2, 1, 1, 1, 1],
        '6' => [1, 1, 2, 2, 2, 1, 1, 1, 1],
        '7' => [1, 1, 1, 2, 1, 1, 2, 1, 2],
        '8' => [2, 1, 1, 2, 1, 1, 2, 1, 1],
        '9' => [1, 1, 2, 2, 1, 1, 2, 1, 1],
        '0' => [1, 1, 1, 2, 2, 1, 2, 1, 1],
        'A' => [2, 1, 1, 1, 1, 2, 1, 1, 2],
        'B' => [1, 1, 2, 1, 1, 2, 1, 1, 2],
        'C' => [2, 1, 2, 1, 1, 2, 1, 1, 1],
        'D' => [1, 1, 1, 1, 2, 2, 1, 1, 2],
        'E' => [2, 1, 1, 1, 2, 2, 1, 1, 1],
        'F' => [1, 1, 2, 1, 2, 2, 1, 1, 1],
        'G' => [1, 1, 1, 1, 1, 2, 2, 1, 2],
        'H' => [2, 1, 1, 1, 1, 2, 2, 1, 1],
        'I' => [1, 1, 2, 1, 1, 2, 2, 1, 1],
        'J' => [1, 1, 1, 1, 2, 2, 2, 1, 1],
        'K' => [2, 1, 1, 1, 1, 1, 1, 2, 2],
        'L' => [1, 1, 2, 1, 1, 1, 1, 2, 2],
        'M' => [2, 1, 2, 1, 1, 1, 1, 2, 1],
        'N' => [1, 1, 1, 1, 2, 1, 1, 2, 2],
        'O' => [2, 1, 1, 1, 2, 1, 1, 2, 1],
        'P' => [1, 1, 2, 1, 2, 1, 1, 2, 1],
        'Q' => [1, 1, 1, 1, 1, 1, 2, 2, 2],
        'R' => [2, 1, 1, 1, 1, 1, 2, 2, 1],
        'S' => [1, 1, 2, 1, 1, 1, 2, 2, 1],
        'T' => [1, 1, 1, 1, 2, 1, 2, 2, 1],
        'U' => [2, 2, 1, 1, 1, 1, 1, 1, 2],
        'V' => [1, 2, 2, 1, 1, 1, 1, 1, 2],
        'W' => [2, 2, 2, 1, 1, 1, 1, 1, 1],
        'X' => [1, 2, 1, 1, 2, 1, 1, 1, 2],
        'Y' => [2, 2, 1, 1, 2, 1, 1, 1, 1],
        'Z' => [1, 2, 2, 1, 2, 1, 1, 1, 1],
        '-' => [1, 2, 1, 1, 1, 1, 2, 1, 2],
        '.' => [2, 2, 1, 1, 1, 1, 2, 1, 1],
        ' ' => [1, 2, 2, 1, 1, 1, 2, 1, 1],
        '*' => [1, 2, 1, 1, 2, 1, 2, 1, 1],
        '+' => [1, 2, 1, 1, 1, 2, 1, 2, 1],
        '/' => [1, 2, 1, 2, 1, 1, 1, 2, 1],
        '$' => [1, 2, 1, 2, 1, 2, 1, 1, 1],
        '%' => [1, 1, 1, 2, 1, 2, 1, 2, 1],
    ];

    /**
     * Map for extended characters
     *
     * @var array<string>
     */
    protected const CODE_39_ASCIIBET = [
        '%U', '$A', '$B', '$C', '$D', '$E', '$F', '$G',
        '$H', '$I', '$J', '$K', '$L', '$M', '$N', '$O',
        '$P', '$Q', '$R', '$S', '$T', '$U', '$V', '$W',
        '$X', '$Y', '$Z', '%A', '%B', '%C', '%D', '%E',
        ' ' , '/A', '/B', '/C', '/D', '/E', '/F', '/G',
        '/H', '/I', '/J', '/K', '/L', '-' , '.' , '/O',
        '0' , '1' , '2' , '3' , '4' , '5' , '6' , '7' ,
        '8' , '9' , '/Z', '%F', '%G', '%H', '%I', '%J',
        '%V', 'A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' ,
        'H' , 'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' ,
        'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' ,
        'X' , 'Y' , 'Z' , '%K', '%L', '%M', '%N', '%O',
        '%W', '+A', '+B', '+C', '+D', '+E', '+F', '+G',
        '+H', '+I', '+J', '+K', '+L', '+M', '+N', '+O',
        '+P', '+Q', '+R', '+S', '+T', '+U', '+V', '+W',
        '+X', '+Y', '+Z', '%P', '%Q', '%R', '%S', '%T',
    ];

    /**
     * Characters used for checksum
     *
     * @var array<string>
     */
    protected const CHKSUM = [
        '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', '-', '.', ' ', '$',
        '/', '+', '%',
    ];

    /**
     * Encode a string to be used for CODE 39 Extended mode.
     *
     * @param string $data Code to extend
     *
     */
    protected function getExtData(string $data): string
    {
        $extdata = '';
        $len = strlen($data);
        for ($chr = 0; $chr < $len; ++$chr) {
            $char = ord($data[$chr]);
//            if ($item > 127) {
//                throw new BarcodeException('Invalid character: chr(' . $item . ')');
//            }

            $extdata .= $this::CODE_39_ASCIIBET[$char];
        }

        return $extdata;
    }

    /**
     * Calculate CODE 39 checksum (modulo 43).
     *
     * @param string $data Code to represent.
     *
     * @return string char checksum.
     */
    protected function getChecksum(string $data): string
    {
        $sum = 0;
        $len = strlen($data);
        for ($chr = 0; $chr < $len; ++$chr) {
            $key = array_keys($this::CHKSUM, $data[$chr]);
            $sum += $key[0];
        }

        $idx = ($sum % 43);
        return $this::CHKSUM[$idx];
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
    protected function code_39setbars(string $data): array
    {
        $blocks = [];
        /* Start */
        $blocks[] = [
                'm' => [
                        [1, 1, 1], [0, 1, 2], [1, 1, 1],
                        [0, 1, 1], [1, 1, 2], [0, 1, 1],
                        [1, 1, 2], [0, 1, 1], [1, 1, 1],
                ],
                'l' => ['*']
        ];
        /* Data */
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = substr($data, $i, 1);
            $block = $this::CODE_39_ALPHABET[$char];

            $blocks[] = [
                    'm' => [[0, 1, 3]]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, $block[0]],
                            [0, 1, $block[1]],
                            [1, 1, $block[2]],
                            [0, 1, $block[3]],
                            [1, 1, $block[4]],
                            [0, 1, $block[5]],
                            [1, 1, $block[6]],
                            [0, 1, $block[7]],
                            [1, 1, $block[8]],
                        ],
                        'l' => [$char]
            ];
        }
        $blocks[] = [
                'm' => [[0, 1, 3]]
        ];
        /* End */
        $blocks[] = [
                'm' => [
                        [1, 1, 1], [0, 1, 2], [1, 1, 1],
                        [0, 1, 1], [1, 1, 2], [0, 1, 1],
                        [1, 1, 2], [0, 1, 1], [1, 1, 1],
                ],
                'l' => ['*']
        ];
        /* Return */
        return $blocks;
    }

    /**
     *
     * @return array<mixed>
     */
    public function code_39(string $data): array
    {
        $data = strtoupper((string) preg_replace('/[^0-9A-Za-z%$\/+ .-]/', '', $data));
        //$data = $this->formatCode($data);

        $blocks = $this::code_39setbars($data);

        /* Return */
        return ['g' => 'l', 'b' => $blocks];
    }

    /**
     *
     * @return array<mixed>
     */
    public function code_39_check(string $data): array
    {
        $data = strtoupper((string) preg_replace('/[^0-9A-Za-z%$\/+ .-]/', '', $data));
        $data = $this->formatCode($data);

        $blocks = $this::code_39setbars($data);

        /* Return */
        return ['g' => 'l', 'b' => $blocks];
    }

    /**
     *
     * @return array<mixed>
     */
    public function code_39_ascii(string $data): array
    {
        //$extdata = $this->getExtData(strtoupper($data));
        $data = $this->getExtData($data);
        //$data = $extdata . $this->getChecksum($extdata);

        $blocks = $this::code_39setbars($data);

        /* Return */
        return ['g' => 'l', 'b' => $blocks];
    }

    /**
     *
     * @return array<mixed>
     */
    public function code_39_ascii_check(string $data): array
    {
        //$extdata = $this->getExtData(strtoupper($data));
        $extdata = $this->getExtData($data);
        $data = $extdata . $this->getChecksum($extdata);

        $blocks = $this::code_39setbars($data);

        /* Return */
        return ['g' => 'l', 'b' => $blocks];
    }

    /**
     *
     * @return array<mixed>
     */
    public function code_39_ascii_encode2(string $data): array
    {
        //$extdata = $this->getExtData(strtoupper($data));
        $extdata = $this->getExtData($data);
        $data = $extdata . $this->getChecksum($extdata);
//echo '<pre>';
//print_r($data);
//echo '</pre>';
        $blocks = [];
        /* Start */
        $blocks[] = [
                'm' => [
                        [1, 1, 1], [0, 1, 2], [1, 1, 1],
                        [0, 1, 1], [1, 1, 2], [0, 1, 1],
                        [1, 1, 2], [0, 1, 1], [1, 1, 1],
                ],
                'l' => ['*']
        ];
        /* Data */
/*
            if ($cha < 128) {
                if ($cha < 32 || $cha >= 127) {
                        $label .= ' ';
                } else {
                        $label .= $char;
                }
*/
        for ($j = 0, $m = strlen($data); $j < $m; $j++) {
            $char = substr($data, $j, 1);
            $baa = $this::CODE_39_ALPHABET[$char];

            $blocks[] = [
                    'm' => [[0, 1, 3]]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, $baa[0]],
                            [0, 1, $baa[1]],
                            [1, 1, $baa[2]],
                            [0, 1, $baa[3]],
                            [1, 1, $baa[4]],
                            [0, 1, $baa[5]],
                            [1, 1, $baa[6]],
                            [0, 1, $baa[7]],
                            [1, 1, $baa[8]],
                    ],
                    'l' => [$char]
            ];
        }
        $blocks[] = [
                'm' => [[0, 1, 3]]
        ];
        /* End */
        $blocks[] = [
                'm' => [
                        [1, 1, 1], [0, 1, 2], [1, 1, 1],
                        [0, 1, 1], [1, 1, 2], [0, 1, 1],
                        [1, 1, 2], [0, 1, 1], [1, 1, 1],
                ],
                'l' => ['*']
        ];

        /* Return */
        return ['g' => 'l', 'b' => $blocks];
    }
}
