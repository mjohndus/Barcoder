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

    protected const CODE_39_ALPHABET = array(
                '1' => array(2, 1, 1, 2, 1, 1, 1, 1, 2),
                '2' => array(1, 1, 2, 2, 1, 1, 1, 1, 2),
                '3' => array(2, 1, 2, 2, 1, 1, 1, 1, 1),
                '4' => array(1, 1, 1, 2, 2, 1, 1, 1, 2),
                '5' => array(2, 1, 1, 2, 2, 1, 1, 1, 1),
                '6' => array(1, 1, 2, 2, 2, 1, 1, 1, 1),
                '7' => array(1, 1, 1, 2, 1, 1, 2, 1, 2),
                '8' => array(2, 1, 1, 2, 1, 1, 2, 1, 1),
                '9' => array(1, 1, 2, 2, 1, 1, 2, 1, 1),
                '0' => array(1, 1, 1, 2, 2, 1, 2, 1, 1),
                'A' => array(2, 1, 1, 1, 1, 2, 1, 1, 2),
                'B' => array(1, 1, 2, 1, 1, 2, 1, 1, 2),
                'C' => array(2, 1, 2, 1, 1, 2, 1, 1, 1),
                'D' => array(1, 1, 1, 1, 2, 2, 1, 1, 2),
                'E' => array(2, 1, 1, 1, 2, 2, 1, 1, 1),
                'F' => array(1, 1, 2, 1, 2, 2, 1, 1, 1),
                'G' => array(1, 1, 1, 1, 1, 2, 2, 1, 2),
                'H' => array(2, 1, 1, 1, 1, 2, 2, 1, 1),
                'I' => array(1, 1, 2, 1, 1, 2, 2, 1, 1),
                'J' => array(1, 1, 1, 1, 2, 2, 2, 1, 1),
                'K' => array(2, 1, 1, 1, 1, 1, 1, 2, 2),
                'L' => array(1, 1, 2, 1, 1, 1, 1, 2, 2),
                'M' => array(2, 1, 2, 1, 1, 1, 1, 2, 1),
                'N' => array(1, 1, 1, 1, 2, 1, 1, 2, 2),
                'O' => array(2, 1, 1, 1, 2, 1, 1, 2, 1),
                'P' => array(1, 1, 2, 1, 2, 1, 1, 2, 1),
                'Q' => array(1, 1, 1, 1, 1, 1, 2, 2, 2),
                'R' => array(2, 1, 1, 1, 1, 1, 2, 2, 1),
                'S' => array(1, 1, 2, 1, 1, 1, 2, 2, 1),
                'T' => array(1, 1, 1, 1, 2, 1, 2, 2, 1),
                'U' => array(2, 2, 1, 1, 1, 1, 1, 1, 2),
                'V' => array(1, 2, 2, 1, 1, 1, 1, 1, 2),
                'W' => array(2, 2, 2, 1, 1, 1, 1, 1, 1),
                'X' => array(1, 2, 1, 1, 2, 1, 1, 1, 2),
                'Y' => array(2, 2, 1, 1, 2, 1, 1, 1, 1),
                'Z' => array(1, 2, 2, 1, 2, 1, 1, 1, 1),
                '-' => array(1, 2, 1, 1, 1, 1, 2, 1, 2),
                '.' => array(2, 2, 1, 1, 1, 1, 2, 1, 1),
                ' ' => array(1, 2, 2, 1, 1, 1, 2, 1, 1),
                '*' => array(1, 2, 1, 1, 2, 1, 2, 1, 1),
                '+' => array(1, 2, 1, 1, 1, 2, 1, 2, 1),
                '/' => array(1, 2, 1, 2, 1, 1, 1, 2, 1),
                '$' => array(1, 2, 1, 2, 1, 2, 1, 1, 1),
                '%' => array(1, 1, 1, 2, 1, 2, 1, 2, 1),
        );

    protected const CODE_39_ASCIIBET = array(
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
        );

        /**
         *
         * @return array<mixed>
         */
    public function code_39_encode(string $data): array
    {
            $data = strtoupper((string) preg_replace('/[^0-9A-Za-z%$\/+ .-]/', '', $data));
            $blocks = array();
            /* Start */
            $blocks[] = array(
                    'm' => array(
                            array(1, 1, 1), array(0, 1, 2), array(1, 1, 1),
                            array(0, 1, 1), array(1, 1, 2), array(0, 1, 1),
                            array(1, 1, 2), array(0, 1, 1), array(1, 1, 1),
                    ),
                    'l' => array('*')
            );
            /* Data */
            for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                    $blocks[] = array(
                            'm' => array(array(0, 1, 3))
                    );
                    $char = substr($data, $i, 1);
                    $block = $this::CODE_39_ALPHABET[$char];
                    $blocks[] = array(
                            'm' => array(
                                    array(1, 1, $block[0]),
                                    array(0, 1, $block[1]),
                                    array(1, 1, $block[2]),
                                    array(0, 1, $block[3]),
                                    array(1, 1, $block[4]),
                                    array(0, 1, $block[5]),
                                    array(1, 1, $block[6]),
                                    array(0, 1, $block[7]),
                                    array(1, 1, $block[8]),
                            ),
                            'l' => array($char)
                    );
            }
            $blocks[] = array(
                    'm' => array(array(0, 1, 3))
            );
            /* End */
            $blocks[] = array(
                    'm' => array(
                            array(1, 1, 1), array(0, 1, 2), array(1, 1, 1),
                            array(0, 1, 1), array(1, 1, 2), array(0, 1, 1),
                            array(1, 1, 2), array(0, 1, 1), array(1, 1, 1),
                    ),
                    'l' => array('*')
            );
            /* Return */
            return array('g' => 'l', 'b' => $blocks);
    }

        /**
         *
         * @return array<mixed>
         */
    public function code_39_ascii_encode(string $data): array
    {
            $modules = array();
            /* Start */
            $modules[] = array(1, 1, 1);
            $modules[] = array(0, 1, 2);
            $modules[] = array(1, 1, 1);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 2);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 2);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 1);
            /* Data */
            $label = '';
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                $char = substr($data, $i, 1);
                $cha = ord($char);
            if ($cha < 128) {
                if ($cha < 32 || $cha >= 127) {
                        $label .= ' ';
                } else {
                        $label .= $char;
                }
                $cha = $this::CODE_39_ASCIIBET[$cha];
                for ($j = 0, $m = strlen($cha); $j < $m; $j++) {
                        $caa = substr($cha, $j, 1);
                        $baa = $this::CODE_39_ALPHABET[$caa];
                        $modules[] = array(0, 1, 3);
                        $modules[] = array(1, 1, $baa[0]);
                        $modules[] = array(0, 1, $baa[1]);
                        $modules[] = array(1, 1, $baa[2]);
                        $modules[] = array(0, 1, $baa[3]);
                        $modules[] = array(1, 1, $baa[4]);
                        $modules[] = array(0, 1, $baa[5]);
                        $modules[] = array(1, 1, $baa[6]);
                        $modules[] = array(0, 1, $baa[7]);
                        $modules[] = array(1, 1, $baa[8]);
                }
            }
        }
            $modules[] = array(0, 1, 3);
            /* End */
            $modules[] = array(1, 1, 1);
            $modules[] = array(0, 1, 2);
            $modules[] = array(1, 1, 1);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 2);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 2);
            $modules[] = array(0, 1, 1);
            $modules[] = array(1, 1, 1);
            /* Return */
            $blocks = array(array('m' => $modules, 'l' => array($label)));
            return array('g' => 'l', 'b' => $blocks);
    }
/*
        private $code_39_alphabet = array(
                '1' => array(2, 1, 1, 2, 1, 1, 1, 1, 2),
                '2' => array(1, 1, 2, 2, 1, 1, 1, 1, 2),
                '3' => array(2, 1, 2, 2, 1, 1, 1, 1, 1),
                '4' => array(1, 1, 1, 2, 2, 1, 1, 1, 2),
                '5' => array(2, 1, 1, 2, 2, 1, 1, 1, 1),
                '6' => array(1, 1, 2, 2, 2, 1, 1, 1, 1),
                '7' => array(1, 1, 1, 2, 1, 1, 2, 1, 2),
                '8' => array(2, 1, 1, 2, 1, 1, 2, 1, 1),
                '9' => array(1, 1, 2, 2, 1, 1, 2, 1, 1),
                '0' => array(1, 1, 1, 2, 2, 1, 2, 1, 1),
                'A' => array(2, 1, 1, 1, 1, 2, 1, 1, 2),
                'B' => array(1, 1, 2, 1, 1, 2, 1, 1, 2),
                'C' => array(2, 1, 2, 1, 1, 2, 1, 1, 1),
                'D' => array(1, 1, 1, 1, 2, 2, 1, 1, 2),
                'E' => array(2, 1, 1, 1, 2, 2, 1, 1, 1),
                'F' => array(1, 1, 2, 1, 2, 2, 1, 1, 1),
                'G' => array(1, 1, 1, 1, 1, 2, 2, 1, 2),
                'H' => array(2, 1, 1, 1, 1, 2, 2, 1, 1),
                'I' => array(1, 1, 2, 1, 1, 2, 2, 1, 1),
                'J' => array(1, 1, 1, 1, 2, 2, 2, 1, 1),
                'K' => array(2, 1, 1, 1, 1, 1, 1, 2, 2),
                'L' => array(1, 1, 2, 1, 1, 1, 1, 2, 2),
                'M' => array(2, 1, 2, 1, 1, 1, 1, 2, 1),
                'N' => array(1, 1, 1, 1, 2, 1, 1, 2, 2),
                'O' => array(2, 1, 1, 1, 2, 1, 1, 2, 1),
                'P' => array(1, 1, 2, 1, 2, 1, 1, 2, 1),
                'Q' => array(1, 1, 1, 1, 1, 1, 2, 2, 2),
                'R' => array(2, 1, 1, 1, 1, 1, 2, 2, 1),
                'S' => array(1, 1, 2, 1, 1, 1, 2, 2, 1),
                'T' => array(1, 1, 1, 1, 2, 1, 2, 2, 1),
                'U' => array(2, 2, 1, 1, 1, 1, 1, 1, 2),
                'V' => array(1, 2, 2, 1, 1, 1, 1, 1, 2),
                'W' => array(2, 2, 2, 1, 1, 1, 1, 1, 1),
                'X' => array(1, 2, 1, 1, 2, 1, 1, 1, 2),
                'Y' => array(2, 2, 1, 1, 2, 1, 1, 1, 1),
                'Z' => array(1, 2, 2, 1, 2, 1, 1, 1, 1),
                '-' => array(1, 2, 1, 1, 1, 1, 2, 1, 2),
                '.' => array(2, 2, 1, 1, 1, 1, 2, 1, 1),
                ' ' => array(1, 2, 2, 1, 1, 1, 2, 1, 1),
                '*' => array(1, 2, 1, 1, 2, 1, 2, 1, 1),
                '+' => array(1, 2, 1, 1, 1, 2, 1, 2, 1),
                '/' => array(1, 2, 1, 2, 1, 1, 1, 2, 1),
                '$' => array(1, 2, 1, 2, 1, 2, 1, 1, 1),
                '%' => array(1, 1, 1, 2, 1, 2, 1, 2, 1),
        );

        private $code_39_asciibet = array(
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
        );
*/
}
