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

/**
 * Barcoder\Encoders
 *
 * Barcode Type class
 *
 * @SuppressWarnings("PHPMD.ExcessiveClassComplexity")
 * @SuppressWarnings("PHPMD.CyclomaticComplexity")
 * @SuppressWarnings("PHPMD.NPathComplexity")
 *
 */

class UPC
{
    protected const UPC_ALPHABET = [
                '0' => [3, 2, 1, 1],
                '1' => [2, 2, 2, 1],
                '2' => [2, 1, 2, 2],
                '3' => [1, 4, 1, 1],
                '4' => [1, 1, 3, 2],
                '5' => [1, 2, 3, 1],
                '6' => [1, 1, 1, 4],
                '7' => [1, 3, 1, 2],
                '8' => [1, 2, 1, 3],
                '9' => [3, 1, 1, 2],
        ];

    protected const UPC_PARITY = [
                '0' => [1, 1, 1, 0, 0, 0],
                '1' => [1, 1, 0, 1, 0, 0],
                '2' => [1, 1, 0, 0, 1, 0],
                '3' => [1, 1, 0, 0, 0, 1],
                '4' => [1, 0, 1, 1, 0, 0],
                '5' => [1, 0, 0, 1, 1, 0],
                '6' => [1, 0, 0, 0, 1, 1],
                '7' => [1, 0, 1, 0, 1, 0],
                '8' => [1, 0, 1, 0, 0, 1],
                '9' => [1, 0, 0, 1, 0, 1],
        ];

    protected const EAN2_PARITY = [
                '0' => [0, 0],
                '1' => [0, 1],
                '2' => [1, 0],
                '3' => [1, 1],
        ];

        /* - - - - UPC FAMILY ENCODER - - - - */

        /**
         * @return array<mixed>
         */
    public function upc_a_encode(string $data): array
    {
            $data = $this->upc_a_normalize($data);
            $blocks = [];
            /* Quiet zone, start, first digit. */
            $digit = substr($data, 0, 1);
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => [$digit, 0, 1 / 3]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [
                            [0, $this::UPC_ALPHABET[$digit][0], 1],
                            [1, $this::UPC_ALPHABET[$digit][1], 1],
                            [0, $this::UPC_ALPHABET[$digit][2], 1],
                            [1, $this::UPC_ALPHABET[$digit][3], 1],
                    ]
            ];
            /* Left zone. */
            for ($i = 1; $i < 6; $i++) {
                    $digit = substr($data, $i, 1);
                    $blocks[] = [
                            'm' => [
                                    [0, $this::UPC_ALPHABET[$digit][0], 1],
                                    [1, $this::UPC_ALPHABET[$digit][1], 1],
                                    [0, $this::UPC_ALPHABET[$digit][2], 1],
                                    [1, $this::UPC_ALPHABET[$digit][3], 1],
                            ],
                            'l' => [$digit, 0.5, (6 - $i) / 6]
                    ];
            }
            /* Middle. */
            $blocks[] = [
                    'm' => [
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                    ]
            ];
            /* Right zone. */
            for ($i = 6; $i < 11; $i++) {
                    $digit = substr($data, $i, 1);
                    $blocks[] = [
                            'm' => [
                                    [1, $this::UPC_ALPHABET[$digit][0], 1],
                                    [0, $this::UPC_ALPHABET[$digit][1], 1],
                                    [1, $this::UPC_ALPHABET[$digit][2], 1],
                                    [0, $this::UPC_ALPHABET[$digit][3], 1],
                            ],
                            'l' => [$digit, 0.5, (11 - $i) / 6]
                    ];
            }
            /* Last digit, end, quiet zone. */
            $digit = substr($data, 11, 1);
            $blocks[] = [
                    'm' => [
                            [1, $this::UPC_ALPHABET[$digit][0], 1],
                            [0, $this::UPC_ALPHABET[$digit][1], 1],
                            [1, $this::UPC_ALPHABET[$digit][2], 1],
                            [0, $this::UPC_ALPHABET[$digit][3], 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => [$digit, 0, 2 / 3]
            ];
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

        /**
         * @return array<mixed>
         */
    public function upc_e_encode(string $data): array
    {
            $data = $this->upc_e_normalize($data);
            $blocks = [];
            /* Quiet zone, start. */
            $blocks[] = [
                    'm' => [[2, 9, 0]]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Digits */
            $system = substr($data, 0, 1) & '1';
            $check = substr($data, 7, 1);
            $pbits = $this::UPC_PARITY[$check];
            for ($i = 1; $i < 7; $i++) {
                    $digit = substr($data, $i, 1);
                    $pbit = $pbits[$i - 1] ^ $system;
                    $blocks[] = [
                            'm' => [
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                            ],
                            'l' => [$digit, 0.5, (7 - $i) / 7]
                    ];
            }
            /* End, quiet zone. */
            $blocks[] = [
                    'm' => [
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [[2, 9, 0]]
            ];
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

        /**
         * @return array<mixed>
         */
    public function ean_13_encode(string $data, string $pad): array
    {
            $data = $this->ean_13_normalize($data);
            $blocks = [];
            /* Quiet zone, start, first digit (as parity). */
            $system = substr($data, 0, 1);
            $pbits = (
                    (int)$system ?
                    $this::UPC_PARITY[$system] :
                    [1, 1, 1, 1, 1, 1]
            );
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => [$system, 0.5, 1 / 3]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Left zone. */
            for ($i = 1; $i < 7; $i++) {
                    $digit = substr($data, $i, 1);
                    $pbit = $pbits[$i - 1];
                    $blocks[] = [
                            'm' => [
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                            ],
                            'l' => [$digit, 0.5, (7 - $i) / 7]
                    ];
            }
            /* Middle. */
            $blocks[] = [
                    'm' => [
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                    ]
            ];
            /* Right zone. */
            for ($i = 7; $i < 13; $i++) {
                    $digit = substr($data, $i, 1);
                    $blocks[] = [
                            'm' => [
                                    [1, $this::UPC_ALPHABET[$digit][0], 1],
                                    [0, $this::UPC_ALPHABET[$digit][1], 1],
                                    [1, $this::UPC_ALPHABET[$digit][2], 1],
                                    [0, $this::UPC_ALPHABET[$digit][3], 1],
                            ],
                            'l' => [$digit, 0.5, (13 - $i) / 7]
                    ];
            }
            /* End, quiet zone. */
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => [$pad, 0.5, 2 / 3]
            ];
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

        /**
         * @return array<mixed>
         */
    public function ean_8_encode(string $data): array
    {
            $data = $this->ean_8_normalize($data);
            $blocks = [];
            /* Quiet zone, start. */
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => ['<', 0.5, 1 / 3]
            ];
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Left zone. */
            for ($i = 0; $i < 4; $i++) {
                    $digit = substr($data, $i, 1);
                    $blocks[] = [
                            'm' => [
                                    [0, $this::UPC_ALPHABET[$digit][0], 1],
                                    [1, $this::UPC_ALPHABET[$digit][1], 1],
                                    [0, $this::UPC_ALPHABET[$digit][2], 1],
                                    [1, $this::UPC_ALPHABET[$digit][3], 1],
                            ],
                            'l' => [$digit, 0.5, (4 - $i) / 5]
                    ];
            }
            /* Middle. */
            $blocks[] = [
                    'm' => [
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [0, 1, 1],
                    ]
            ];
            /* Right zone. */
            for ($i = 4; $i < 8; $i++) {
                    $digit = substr($data, $i, 1);
                    $blocks[] = [
                            'm' => [
                                    [1, $this::UPC_ALPHABET[$digit][0], 1],
                                    [0, $this::UPC_ALPHABET[$digit][1], 1],
                                    [1, $this::UPC_ALPHABET[$digit][2], 1],
                                    [0, $this::UPC_ALPHABET[$digit][3], 1],
                            ],
                            'l' => [$digit, 0.5, (8 - $i) / 5]
                    ];
            }
            /* End, quiet zone. */
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            $blocks[] = [
                    'm' => [[2, 9, 0]],
                    'l' => ['>', 0.5, 2 / 3]
            ];
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

        /**
         * @return array<mixed>
         */
    public function ean_5_encode(string $data): array
    {
            //$data = $this->ean_5_normalize($data);
            $check = (((3 * ((int) $data[0] + (int) $data[2] + (int) $data[4])) +
                     (9 * ((int) $data[1] + (int) $data[3]
                     ))) % 10);
            $pbits = $this::UPC_PARITY[$check];
            unset($pbits[0]);
            $pbits = array_values($pbits);
            $blocks = [];
            /* start. */
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Left zone. */
            $digit = substr($data, 0, 1);
            $pbit = $pbits[0];
            $blocks[] = [
                    'm' => [
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                    ],
                    'l' => [$digit, 0.5, 1 / 2]
            ];
            /* zone + seperator */
            for ($i = 1; $i < 5; $i++) {
                    $digit = substr($data, $i, 1);
                    $pbit = $pbits[$i];
                    $blocks[] = [
                            'm' => [
                                    [0, 1, 1],
                                    [1, 1, 1],
                            ]
                    ];
                    $blocks[] = [
                            'm' => [
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                                    [0, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                                    [1, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                            ],
                            'l' => [$digit, 0.5, 1 / 2]
                    ];
            }
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

        /**
         * @return array<mixed>
         */
    public function ean_2_encode(string $data): array
    {
            //$data = $this->ean_2_normalize($data);
            $data = (string) preg_replace('/[^0-9*]/', '', $data);
            $data_length = 2;

        if (strlen($data) < 2) {
            $data = str_pad($data, $data_length, '0', STR_PAD_LEFT);
        }

            $check = (int) $data % 4;
            $pbits = $this::EAN2_PARITY[$check];
            $blocks = [];
            /* start. */
            $blocks[] = [
                    'm' => [
                            [1, 1, 1],
                            [0, 1, 1],
                            [1, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Left zone. */
            $digit = substr($data, 0, 1);
            $pbit = $pbits[0];
            $blocks[] = [
                    'm' => [
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                    ],
                    'l' => [$digit, 0.5, 1 / 2]
            ];
            /* Middle. separator. */
            $blocks[] = [
                    'm' => [
                            [0, 1, 1],
                            [1, 1, 1],
                    ]
            ];
            /* Right zone. */
            $digit = substr($data, 1, 1);
            $pbit = $pbits[1];
            $blocks[] = [
                    'm' => [
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 3 : 0], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 2 : 1], 1],
                            [0, $this::UPC_ALPHABET[$digit][$pbit ? 1 : 2], 1],
                            [1, $this::UPC_ALPHABET[$digit][$pbit ? 0 : 3], 1],
                    ],
                    'l' => [$digit, 0.5, 1 / 2]
            ];
            /* Return code. */
            return ['g' => 'l', 'b' => $blocks];
    }

    private function upc_a_normalize(string $data): string
    {
            $data = (string) preg_replace('/[^0-9*]/', '', $data);
            /* Set length to 12 digits. */
        if (strlen($data) < 5) {
                $data = str_repeat('0', 12);
        } elseif (strlen($data) < 12) {
                $system = substr($data, 0, 1);
                $edata = substr($data, 1, -2);
                $epattern = (int)substr($data, -2, 1);
                $check = substr($data, -1);
            if ($epattern < 3) {
                    $left = $system . substr($edata, 0, 2) . $epattern;
                    $right = substr($edata, 2) . $check;
            } elseif ($epattern < strlen($edata)) {
                    $left = $system . substr($edata, 0, $epattern);
                    $right = substr($edata, $epattern) . $check;
            } else {
                    $left = $system . $edata;
                    $right = $epattern . $check;
            }
                $center = str_repeat('0', 12 - strlen($left . $right));
                $data = $left . $center . $right;
        } elseif (strlen($data) > 12) {
                $left = substr($data, 0, 6);
                $right = substr($data, -6);
                $data = $left . $right;
        }
            /* Replace * with missing or check digit. */
        while (($ott = strpos($data, '*')) !== false) {
                $checksum = 0;
            for ($i = 0; $i < $ott; $i++) {
                    $digit = substr($data, $i, 1);
                    $checksum += (($i % 2) ? 1 : 3) * (int) $digit;
            }
                $checksum *= (($ott % 2) ? 9 : 3);
                $left = substr($data, 0, $ott);
                $center = substr((string) $checksum, -1);
                $right = substr($data, $ott + 1);
                $data = $left . $center . $right;
        }
            return $data;
    }

    private function upc_e_normalize(string $data): string
    {
            $data = (string) preg_replace('/[^0-9*]/', '', $data);
            /* If exactly 8 digits, use verbatim even if check digit is wrong. */
        if (
            preg_match(
                '/^([01])([0-9][0-9][0-9][0-9][0-9][0-9])([0-9])$/',
                $data,
                $mtt
            )
        ) {
                    return $data;
        }
            /* If unknown check digit, use verbatim but calculate check digit. */
        if (
            preg_match(
                '/^([01])([0-9][0-9][0-9][0-9][0-9][0-9])([*])$/',
                $data,
                $mtt
            )
        ) {
                $data = $this->upc_a_normalize($data);
                return $mtt[1] . $mtt[2] . substr($data, -1);
        }
            /* Otherwise normalize to UPC-A and convert back. */
            $data = $this->upc_a_normalize($data);
        if (
            preg_match(
                '/^([01])([0-9][0-9])([0-2])0000([0-9][0-9][0-9])([0-9])$/',
                $data,
                $mtt
            )
        ) {
                return $mtt[1] . $mtt[2] . $mtt[4] . $mtt[3] . $mtt[5];
        }
        if (
            preg_match(
                '/^([01])([0-9][0-9][0-9])00000([0-9][0-9])([0-9])$/',
                $data,
                $mtt
            )
        ) {
                return $mtt[1] . $mtt[2] . $mtt[3] . '3' . $mtt[4];
        }
        if (
            preg_match(
                '/^([01])([0-9][0-9][0-9][0-9])00000([0-9])([0-9])$/',
                $data,
                $mtt
            )
        ) {
                return $mtt[1] . $mtt[2] . $mtt[3] . '4' . $mtt[4];
        }
        if (
            preg_match(
                '/^([01])([0-9][0-9][0-9][0-9][0-9])0000([5-9])([0-9])$/',
                $data,
                $mtt
            )
        ) {
                return $mtt[1] . $mtt[2] . $mtt[3] . $mtt[4];
        }
            return str_repeat('0', 8);
    }

    private function ean_13_normalize(string $data): string
    {
            $data = (string) preg_replace('/[^0-9*]/', '', $data);
            /* Set length to 13 digits. */
        if (strlen($data) < 13) {
                return '0' . $this->upc_a_normalize($data);
        } elseif (strlen($data) > 13) {
                $left = substr($data, 0, 7);
                $right = substr($data, -6);
                $data = $left . $right;
        }
            /* Replace * with missing or check digit. */
        while (($ott = strpos($data, '*')) !== false) {
                $checksum = 0;
            for ($i = 0; $i < $ott; $i++) {
                    $digit = substr($data, $i, 1);
                    $checksum += (($i % 2) ? 3 : 1) * (int) $digit;
            }
                $checksum *= (($ott % 2) ? 3 : 9);
                $left = substr($data, 0, $ott);
                $center = substr((string) $checksum, -1);
                $right = substr($data, $ott + 1);
                $data = $left . $center . $right;
        }
            return $data;
    }

    private function ean_8_normalize(string $data): string
    {
            $data = (string) preg_replace('/[^0-9*]/', '', $data);
            /* Set length to 8 digits. */
        if (strlen($data) < 8) {
                $midpoint = floor(strlen($data) / 2);
                $left = substr($data, 0, (int) $midpoint);
                $center = str_repeat('0', 8 - strlen($data));
                $right = substr($data, (int) $midpoint);
                $data = $left . $center . $right;
        } elseif (strlen($data) > 8) {
                $left = substr($data, 0, 4);
                $right = substr($data, -4);
                $data = $left . $right;
        }
            /* Replace * with missing or check digit. */
        while (($ott = strpos($data, '*')) !== false) {
                $checksum = 0;
            for ($i = 0; $i < $ott; $i++) {
                    $digit = substr($data, $i, 1);
                    $checksum += (($i % 2) ? 1 : 3) * (int) $digit;
            }
                $checksum *= (($ott % 2) ? 9 : 3);
                $left = substr($data, 0, $ott);
                $center = substr((string) $checksum, -1);
                $right = substr($data, $ott + 1);
                $data = $left . $center . $right;
        }
            return $data;
    }
}
