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

class ITF {

        protected const i25_alphabet = [
        //private $itf_alphabet = array(
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

        protected const s25_alphabet = [
                '0' => '10101110111010',
                '1' => '11101010101110',
                '2' => '10111010101110',
                '3' => '11101110101010',
                '4' => '10101110101110',
                '5' => '11101011101010',
                '6' => '10111011101010',
                '7' => '10101011101110',
                '8' => '11101010111010',
                '9' => '10111010111010',
        ];

        protected const s25h_alphabet = [
                '0' => [1, 0, 1, 0, 1, 1, 1, 0, 1, 1, 1, 0, 1, 0],
                '1' => [1, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0],
                '2' => [1, 0, 1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0],
                '3' => [1, 1, 1, 0, 1, 1, 1, 0, 1, 0, 1, 0, 1, 0],
                '4' => [1, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0],
                '5' => [1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1, 0],
                '6' => [1, 0, 1, 1, 1, 0, 1, 1, 1, 0, 1, 0, 1, 0],
                '7' => [1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 1, 1, 1, 0],
                '8' => [1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 1, 0],
                '9' => [1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1, 0],
        ];

        protected const s25t_alphabet = [
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
         * @param string $code Code to represent.
         *
         * @return int char checksum.
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
        protected function formatCode(string $data): int
        {
            $data = $data . $this->getChecksum($data);
            return $data;
        }

        /**
         *
         * @return array<int, array<int>>
         */
        protected function setbars(string $data): array
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
                    $c1 = substr($data, $i, 1);
                    $c2 = substr($data, $i+1, 1);
                    $b1 = $this::i25_alphabet[$c1];
                    $b2 = $this::i25_alphabet[$c2];
                    $blocks[] = [
                            'm' => [
                                    [1, 1, $b1[0]],
                                    [0, 1, $b2[0]],
                                    [1, 1, $b1[1]],
                                    [0, 1, $b2[1]],
                                    [1, 1, $b1[2]],
                                    [0, 1, $b2[2]],
                                    [1, 1, $b1[3]],
                                    [0, 1, $b2[3]],
                                    [1, 1, $b1[4]],
                                    [0, 1, $b2[4]],
                            ],
                            'l' => [$c1 . $c2]
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
            //return array('g' => 'l', 'b' => $blocks);
        }

        /**
         *
         * @return array<int, array<int>>
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
                        $c1 = substr($data, $i, 1);
                        $c2 = substr($data, $i+1, 1);
                        } else {
                          $c1 = '';
                          $c2 = '';
                     }
                 $blocks[] = [
                         'm' => [
                                [1, $this::s25t_alphabet[$digit][0], 1],
                                [0, $this::s25t_alphabet[$digit][1], 1],
                                [1, $this::s25t_alphabet[$digit][2], 1],
                                [0, $this::s25t_alphabet[$digit][3], 1],
                                [1, $this::s25t_alphabet[$digit][4], 1],
                                [0, $this::s25t_alphabet[$digit][5], 1],
                                [1, $this::s25t_alphabet[$digit][6], 1],
                                [0, $this::s25t_alphabet[$digit][7], 1],
                                [1, $this::s25t_alphabet[$digit][8], 1],
                                [0, $this::s25t_alphabet[$digit][9], 1],
                         ],
                         'l' => [$c1 . $c2]
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
            //return array('g' => 'l', 'b' => $blocks);
        }

        /* - - - - I25 ENCODER - - - - */

        public function i25_encode($data) {
                $data = preg_replace('/[^0-9]/', '', $data);

                if (strlen($data) % 2 != 0) {
                    // add leading zero if code-length is odd
                    $data = '0' . $data;
                }
                $blocks = $this::setbars($data);

                /* Return code. */
                return array('g' => 'l', 'b' => $blocks);
        }

        /* - - - - I25+ ENCODER - - - - */

        public function i25check_encode($data) {
                $data = preg_replace('/[^0-9]/', '', $data);
                $data = $this->formatCode($data);

                if (strlen($data) % 2 != 0) {
                    // add leading zero if code-length is odd
                    $data = '0' . $data;
                }
                $blocks = $this::setbars($data);

                /* Return code. */
                return array('g' => 'l', 'b' => $blocks);
        }

        /* - - - - s25 ENCODER - - - - */

        public function s25_encode($data) {
                $data = preg_replace('/[^0-9]/', '', $data);
//                $data = $this->formatCode($data);

                if (strlen($data) % 2 != 0) {
                    // add leading zero if code-length is odd
                    $data = '0' . $data;
                }
                $blocks = $this::s25setbars($data);

                /* Return code. */
                return array('g' => 'l', 'b' => $blocks);
        }

        /* - - - - s25+ ENCODER - - - - */

        public function s25check_encode($data) {
                $data = preg_replace('/[^0-9]/', '', $data);
                $data = $this->formatCode($data);

                if (strlen($data) % 2 != 0) {
                    // add leading zero if code-length is odd
                    $data = '0' . $data;
                }
                $blocks = $this::s25setbars($data);

                /* Return code. */
                return array('g' => 'l', 'b' => $blocks);
        }
}
