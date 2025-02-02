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
 * @SuppressWarnings("PHPMD.CyclomaticComplexity")
 * @SuppressWarnings("PHPMD.NPathComplexity")
 * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
 * @SuppressWarnings("PHPMD.UnusedPrivateMethod")
 *
 */

class Code128
{
        /* - - - - CODE 128 ENCODER - - - - */

    protected const CODE_128_ALPHABET = array(
                array(2, 1, 2, 2, 2, 2), array(2, 2, 2, 1, 2, 2),
                array(2, 2, 2, 2, 2, 1), array(1, 2, 1, 2, 2, 3),
                array(1, 2, 1, 3, 2, 2), array(1, 3, 1, 2, 2, 2),
                array(1, 2, 2, 2, 1, 3), array(1, 2, 2, 3, 1, 2),
                array(1, 3, 2, 2, 1, 2), array(2, 2, 1, 2, 1, 3),
                array(2, 2, 1, 3, 1, 2), array(2, 3, 1, 2, 1, 2),
                array(1, 1, 2, 2, 3, 2), array(1, 2, 2, 1, 3, 2),
                array(1, 2, 2, 2, 3, 1), array(1, 1, 3, 2, 2, 2),
                array(1, 2, 3, 1, 2, 2), array(1, 2, 3, 2, 2, 1),
                array(2, 2, 3, 2, 1, 1), array(2, 2, 1, 1, 3, 2),
                array(2, 2, 1, 2, 3, 1), array(2, 1, 3, 2, 1, 2),
                array(2, 2, 3, 1, 1, 2), array(3, 1, 2, 1, 3, 1),
                array(3, 1, 1, 2, 2, 2), array(3, 2, 1, 1, 2, 2),
                array(3, 2, 1, 2, 2, 1), array(3, 1, 2, 2, 1, 2),
                array(3, 2, 2, 1, 1, 2), array(3, 2, 2, 2, 1, 1),
                array(2, 1, 2, 1, 2, 3), array(2, 1, 2, 3, 2, 1),
                array(2, 3, 2, 1, 2, 1), array(1, 1, 1, 3, 2, 3),
                array(1, 3, 1, 1, 2, 3), array(1, 3, 1, 3, 2, 1),
                array(1, 1, 2, 3, 1, 3), array(1, 3, 2, 1, 1, 3),
                array(1, 3, 2, 3, 1, 1), array(2, 1, 1, 3, 1, 3),
                array(2, 3, 1, 1, 1, 3), array(2, 3, 1, 3, 1, 1),
                array(1, 1, 2, 1, 3, 3), array(1, 1, 2, 3, 3, 1),
                array(1, 3, 2, 1, 3, 1), array(1, 1, 3, 1, 2, 3),
                array(1, 1, 3, 3, 2, 1), array(1, 3, 3, 1, 2, 1),
                array(3, 1, 3, 1, 2, 1), array(2, 1, 1, 3, 3, 1),
                array(2, 3, 1, 1, 3, 1), array(2, 1, 3, 1, 1, 3),
                array(2, 1, 3, 3, 1, 1), array(2, 1, 3, 1, 3, 1),
                array(3, 1, 1, 1, 2, 3), array(3, 1, 1, 3, 2, 1),
                array(3, 3, 1, 1, 2, 1), array(3, 1, 2, 1, 1, 3),
                array(3, 1, 2, 3, 1, 1), array(3, 3, 2, 1, 1, 1),
                array(3, 1, 4, 1, 1, 1), array(2, 2, 1, 4, 1, 1),
                array(4, 3, 1, 1, 1, 1), array(1, 1, 1, 2, 2, 4),
                array(1, 1, 1, 4, 2, 2), array(1, 2, 1, 1, 2, 4),
                array(1, 2, 1, 4, 2, 1), array(1, 4, 1, 1, 2, 2),
                array(1, 4, 1, 2, 2, 1), array(1, 1, 2, 2, 1, 4),
                array(1, 1, 2, 4, 1, 2), array(1, 2, 2, 1, 1, 4),
                array(1, 2, 2, 4, 1, 1), array(1, 4, 2, 1, 1, 2),
                array(1, 4, 2, 2, 1, 1), array(2, 4, 1, 2, 1, 1),
                array(2, 2, 1, 1, 1, 4), array(4, 1, 3, 1, 1, 1),
                array(2, 4, 1, 1, 1, 2), array(1, 3, 4, 1, 1, 1),
                array(1, 1, 1, 2, 4, 2), array(1, 2, 1, 1, 4, 2),
                array(1, 2, 1, 2, 4, 1), array(1, 1, 4, 2, 1, 2),
                array(1, 2, 4, 1, 1, 2), array(1, 2, 4, 2, 1, 1),
                array(4, 1, 1, 2, 1, 2), array(4, 2, 1, 1, 1, 2),
                array(4, 2, 1, 2, 1, 1), array(2, 1, 2, 1, 4, 1),
                array(2, 1, 4, 1, 2, 1), array(4, 1, 2, 1, 2, 1),
                array(1, 1, 1, 1, 4, 3), array(1, 1, 1, 3, 4, 1),
                array(1, 3, 1, 1, 4, 1), array(1, 1, 4, 1, 1, 3),
                array(1, 1, 4, 3, 1, 1), array(4, 1, 1, 1, 1, 3),
                array(4, 1, 1, 3, 1, 1), array(1, 1, 3, 1, 4, 1),
                array(1, 1, 4, 1, 3, 1), array(3, 1, 1, 1, 4, 1),
                array(4, 1, 1, 1, 3, 1), array(2, 1, 1, 4, 1, 2),
                array(2, 1, 1, 2, 1, 4), array(2, 1, 1, 2, 3, 2),
                array(2, 3, 3, 1, 1, 1, 2)
        );

        /**
         *
         * @return array<mixed>
         */
    public function code_128_encode(string $data, int $dstate, bool $fnc1): array
    {
        $data = (string) preg_replace('/[\x80-\xFF]/', '', $data);
        $label = preg_replace('/[\x00-\x1F\x7F]/', ' ', $data);
        $chars = $this->code_128_normalize($data, $dstate, $fnc1);
        $checksum = (int) $chars[0] % 103;
        for ($i = 1, $n = count($chars); $i < $n; $i++) {
            $checksum += $i * (int) $chars[$i];
            $checksum %= 103;
        }
        $chars[] = $checksum;
        $chars[] = 106;
        $modules = [];
        $modules[] = [0, 10, 0];
        foreach ($chars as $char) {
            $block = $this::CODE_128_ALPHABET[$char];
            foreach ($block as $i => $module) {
//echo '<pre>';
//print_r($i);
//echo '</pre>';
                //$modules[] = array(($i & 1) ^ 1, $module, 1);
                $modules[] = [$i & 1 ^ 1, $module, 1];
            }
//echo '<pre>';
//print_r($modules);
//echo '</pre>';
        }
        $modules[] = [0, 10, 0];
        $blocks = [['m' => $modules, 'l' => [$label]]];
//echo '<pre>';
//print_r($blocks);
//echo '</pre>';
        return ['g' => 'l', 'b' => $blocks];
    }

    private function cas1(): void
    {
    }

        /**
         *
         * @return array<int>
         */
    private function code_128_normalize(string $data, int $dstate, bool $fnc1): array
    {
            $detectcba = '/(^[0-9]{4,}|^[0-9]{2}$)|([\x60-\x7F])|([\x00-\x1F])/';
            $detectc = '/(^[0-9]{6,}|^[0-9]{4,}$)/';
            $detectba = '/([\x60-\x7F])|([\x00-\x1F])/';
            $consumec = '/(^[0-9]{2})/';
            $state = (($dstate > 0 && $dstate < 4) ? $dstate : 0);
            $abstate = ((abs($dstate) == 2) ? 2 : 1);
            $chars = [102 + ($state ? $state : $abstate)];
        if ($fnc1) {
            $chars[] = 102;
        }
        while (strlen($data)) {
            switch ($state) {
                case 0:
                    if (preg_match($detectcba, $data, $mem)) {
                        if ($mem[1]) {
                                    $state = 3;
                        } elseif ($mem[2]) {
                                        $state = 2;
                        } else {
                            $state = 1;
                        }
                    } else {
                            $state = $abstate;
                    }
                        $chars = [102 + $state];
                    if ($fnc1) {
                        $chars[] = 102;
                    }
                    break;
                case 1:
                    if ($dstate <= 0 && preg_match($detectc, $data, $mem)) {
                        if (strlen($mem[0]) % 2) {
                            $data = substr($data, 1);
                            $chars[] = 16 + ((int) substr($mem[0], 0, 1));
                        }
                                $state = 3;
                                $chars[] = 99;
                    } else {
                                    $cha = ord(substr($data, 0, 1));
                                    $data = substr($data, 1);
                        if ($cha < 32) {
                                $chars[] = $cha + 64;
                        } elseif ($cha < 96) {
                                $chars[] = $cha - 32;
                        } else {
                            if (preg_match($detectba, $data, $mem)) {
                                if ($mem[1]) {
                                    $state = 2;
                                    $chars[] = 100;
                                } else {
                                    $chars[] = 98;
                                }
                            } else {
                                    $chars[] = 98;
                            }
                                $chars[] = $cha - 32;
                        }
                    }
                    break;
                case 2:
                    if ($dstate <= 0 && preg_match($detectc, $data, $mem)) {
                        if (strlen($mem[0]) % 2) {
                            $data = substr($data, 1);
                            $chars[] = 16 + ((int) substr($mem[0], 0, 1));
                        }
                                $state = 3;
                                $chars[] = 99;
                    } else {
                                    $cha = ord(substr($data, 0, 1));
                                    $data = substr($data, 1);
                        if ($cha >= 32) {
                                $chars[] = $cha - 32;
                        } else {
                            if (preg_match($detectba, $data, $mem)) {
                                if (isset($mem[2])) {
                                    $state = 1;
                                    $chars[] = 101;
                                } else {
                                    $chars[] = 98;
                                }
                            } else {
                                    $chars[] = 98;
                            }
                                $chars[] = $cha + 64;
                        }
                    }
                    break;
                case 3:
                    if (preg_match($consumec, $data, $mem)) {
                                $data = substr($data, 2);
                                $chars[] = (int)$mem[0];
                    } else {
                        if (preg_match($detectba, $data, $mem)) {
                            if ($mem[1]) {
                                $state = 2;
                            } else {
                                            $state = 1;
                            }
                        } else {
                            $state = $abstate;
                        }
                                    $chars[] = 102 - $state;
                    }
                    break;
            }
        }
            return $chars;
    }
}
