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

namespace Barcoder\Encoder;

class Codes {

        /* - - - - CODE 39 FAMILY ENCODER - - - - */

        public function code_39_encode($data) {
                $data = strtoupper(preg_replace('/[^0-9A-Za-z%$\/+ .-]/', '', $data));
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
                        $block = $this->code_39_alphabet[$char];
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

        public function code_39_ascii_encode($data) {
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
                        $ch = ord($char);
                        if ($ch < 128) {
                                if ($ch < 32 || $ch >= 127) {
                                        $label .= ' ';
                                } else {
                                        $label .= $char;
                                }
                                $ch = $this->code_39_asciibet[$ch];
                                for ($j = 0, $m = strlen($ch); $j < $m; $j++) {
                                        $c = substr($ch, $j, 1);
                                        $b = $this->code_39_alphabet[$c];
                                        $modules[] = array(0, 1, 3);
                                        $modules[] = array(1, 1, $b[0]);
                                        $modules[] = array(0, 1, $b[1]);
                                        $modules[] = array(1, 1, $b[2]);
                                        $modules[] = array(0, 1, $b[3]);
                                        $modules[] = array(1, 1, $b[4]);
                                        $modules[] = array(0, 1, $b[5]);
                                        $modules[] = array(1, 1, $b[6]);
                                        $modules[] = array(0, 1, $b[7]);
                                        $modules[] = array(1, 1, $b[8]);
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

        public function code_93_encode($data) {
                $data = strtoupper(preg_replace('/[^0-9A-Za-z%+\/$ .-]/', '', $data));
                $modules = array();
                /* Start */
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 4, 1);
                $modules[] = array(0, 1, 1);
                /* Data */
                $values = array();
                for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                        $char = substr($data, $i, 1);
                        $block = $this->code_93_alphabet[$char];
                        $modules[] = array(1, $block[0], 1);
                        $modules[] = array(0, $block[1], 1);
                        $modules[] = array(1, $block[2], 1);
                        $modules[] = array(0, $block[3], 1);
                        $modules[] = array(1, $block[4], 1);
                        $modules[] = array(0, $block[5], 1);
                        $values[] = $block[6];
                }
                /* Check Digits */
                for ($i = 0; $i < 2; $i++) {
                        $index = count($values);
                        $weight = 0;
                        $checksum = 0;
                        while ($index) {
                                $index--;
                                $weight++;
                                $checksum += $weight * $values[$index];
                                $checksum %= 47;
                                $weight %= ($i ? 15 : 20);
                        }
                        $values[] = $checksum;
                }
                $alphabet = array_values($this->code_93_alphabet);
                for ($i = count($values) - 2, $n = count($values); $i < $n; $i++) {
                        $block = $alphabet[$values[$i]];
                        $modules[] = array(1, $block[0], 1);
                        $modules[] = array(0, $block[1], 1);
                        $modules[] = array(1, $block[2], 1);
                        $modules[] = array(0, $block[3], 1);
                        $modules[] = array(1, $block[4], 1);
                        $modules[] = array(0, $block[5], 1);
                }
                /* End */
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 4, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                /* Return */
                $blocks = array(array('m' => $modules, 'l' => array($data)));
                return array('g' => 'l', 'b' => $blocks);
        }

        public function code_93_ascii_encode($data) {
                $modules = array();
                /* Start */
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 4, 1);
                $modules[] = array(0, 1, 1);
                /* Data */
                $label = '';
                $values = array();
                for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                        $char = substr($data, $i, 1);
                        $ch = ord($char);
                        if ($ch < 128) {
                                if ($ch < 32 || $ch >= 127) {
                                        $label .= ' ';
                                } else {
                                        $label .= $char;
                                }
                                $ch = $this->code_93_asciibet[$ch];
                                for ($j = 0, $m = strlen($ch); $j < $m; $j++) {
                                        $c = substr($ch, $j, 1);
                                        $b = $this->code_93_alphabet[$c];
                                        $modules[] = array(1, $b[0], 1);
                                        $modules[] = array(0, $b[1], 1);
                                        $modules[] = array(1, $b[2], 1);
                                        $modules[] = array(0, $b[3], 1);
                                        $modules[] = array(1, $b[4], 1);
                                        $modules[] = array(0, $b[5], 1);
                                        $values[] = $b[6];
                                }
                        }
                }
                /* Check Digits */
                for ($i = 0; $i < 2; $i++) {
                        $index = count($values);
                        $weight = 0;
                        $checksum = 0;
                        while ($index) {
                                $index--;
                                $weight++;
                                $checksum += $weight * $values[$index];
                                $checksum %= 47;
                                $weight %= ($i ? 15 : 20);
                        }
                        $values[] = $checksum;
                }
                }
                $alphabet = array_values($this->code_93_alphabet);
                for ($i = count($values) - 2, $n = count($values); $i < $n; $i++) {
                        $block = $alphabet[$values[$i]];
                        $modules[] = array(1, $block[0], 1);
                        $modules[] = array(0, $block[1], 1);
                        $modules[] = array(1, $block[2], 1);
                        $modules[] = array(0, $block[3], 1);
                        $modules[] = array(1, $block[4], 1);
                        $modules[] = array(0, $block[5], 1);
                }
                /* End */
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 4, 1);
                $modules[] = array(0, 1, 1);
                $modules[] = array(1, 1, 1);
                /* Return */
                $blocks = array(array('m' => $modules, 'l' => array($label)));
                return array('g' => 'l', 'b' => $blocks);
        }

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

        private $code_93_alphabet = array(
                '0' => array(1, 3, 1, 1, 1, 2,  0),
                '1' => array(1, 1, 1, 2, 1, 3,  1),
                '2' => array(1, 1, 1, 3, 1, 2,  2),
                '3' => array(1, 1, 1, 4, 1, 1,  3),
                '4' => array(1, 2, 1, 1, 1, 3,  4),
                '5' => array(1, 2, 1, 2, 1, 2,  5),
                '6' => array(1, 2, 1, 3, 1, 1,  6),
                '7' => array(1, 1, 1, 1, 1, 4,  7),
                '8' => array(1, 3, 1, 2, 1, 1,  8),
                '9' => array(1, 4, 1, 1, 1, 1,  9),
                'A' => array(2, 1, 1, 1, 1, 3, 10),
                'B' => array(2, 1, 1, 2, 1, 2, 11),
                'C' => array(2, 1, 1, 3, 1, 1, 12),
                'D' => array(2, 2, 1, 1, 1, 2, 13),
                'E' => array(2, 2, 1, 2, 1, 1, 14),
                'F' => array(2, 3, 1, 1, 1, 1, 15),
                'G' => array(1, 1, 2, 1, 1, 3, 16),
                'H' => array(1, 1, 2, 2, 1, 2, 17),
                'I' => array(1, 1, 2, 3, 1, 1, 18),
                'J' => array(1, 2, 2, 1, 1, 2, 19),
                'K' => array(1, 3, 2, 1, 1, 1, 20),
                'L' => array(1, 1, 1, 1, 2, 3, 21),
                'M' => array(1, 1, 1, 2, 2, 2, 22),
                'N' => array(1, 1, 1, 3, 2, 1, 23),
                'O' => array(1, 2, 1, 1, 2, 2, 24),
                'P' => array(1, 3, 1, 1, 2, 1, 25),
                'Q' => array(2, 1, 2, 1, 1, 2, 26),
                'R' => array(2, 1, 2, 2, 1, 1, 27),
                'S' => array(2, 1, 1, 1, 2, 2, 28),
                'T' => array(2, 1, 1, 2, 2, 1, 29),
                'U' => array(2, 2, 1, 1, 2, 1, 30),
                'V' => array(2, 2, 2, 1, 1, 1, 31),
                'W' => array(1, 1, 2, 1, 2, 2, 32),
                'X' => array(1, 1, 2, 2, 2, 1, 33),
                'Y' => array(1, 2, 2, 1, 2, 1, 34),
                'Z' => array(1, 2, 3, 1, 1, 1, 35),
                '-' => array(1, 2, 1, 1, 3, 1, 36),
                '.' => array(3, 1, 1, 1, 1, 2, 37),
                ' ' => array(3, 1, 1, 2, 1, 1, 38),
                '$' => array(3, 2, 1, 1, 1, 1, 39),
                '/' => array(1, 1, 2, 1, 3, 1, 40),
                '+' => array(1, 1, 3, 1, 2, 1, 41),
                '%' => array(2, 1, 1, 1, 3, 1, 42),
                '#' => array(1, 2, 1, 2, 2, 1, 43), /* ($) */
                '&' => array(3, 1, 2, 1, 1, 1, 44), /* (%) */
                '|' => array(3, 1, 1, 1, 2, 1, 45), /* (/) */
                '=' => array(1, 2, 2, 2, 1, 1, 46), /* (+) */
                '*' => array(1, 1, 1, 1, 4, 1,  0),
        );

        private $code_93_asciibet = array(
                '&U', '#A', '#B', '#C', '#D', '#E', '#F', '#G',
                '#H', '#I', '#J', '#K', '#L', '#M', '#N', '#O',
                '#P', '#Q', '#R', '#S', '#T', '#U', '#V', '#W',
                '#X', '#Y', '#Z', '&A', '&B', '&C', '&D', '&E',
                ' ' , '|A', '|B', '|C', '$' , '%' , '|F', '|G',
                '|H', '|I', '|J', '+' , '|L', '-' , '.' , '/' ,
                '0' , '1' , '2' , '3' , '4' , '5' , '6' , '7' ,
                '8' , '9' , '|Z', '&F', '&G', '&H', '&I', '&J',
                '&V', 'A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' ,
                'H' , 'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' ,
                'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' ,
                'X' , 'Y' , 'Z' , '&K', '&L', '&M', '&N', '&O',
                '&W', '=A', '=B', '=C', '=D', '=E', '=F', '=G',
                '=H', '=I', '=J', '=K', '=L', '=M', '=N', '=O',
                '=P', '=Q', '=R', '=S', '=T', '=U', '=V', '=W',
                '=X', '=Y', '=Z', '&P', '&Q', '&R', '&S', '&T',
        );

        /* - - - - CODE 128 ENCODER - - - - */

        private function code_128_encode($data, $dstate, $fnc1) {
                $data = preg_replace('/[\x80-\xFF]/', '', $data);
                $label = preg_replace('/[\x00-\x1F\x7F]/', ' ', $data);
                $chars = $this->code_128_normalize($data, $dstate, $fnc1);
                $checksum = $chars[0] % 103;
                for ($i = 1, $n = count($chars); $i < $n; $i++) {
                        $checksum += $i * $chars[$i];
                        $checksum %= 103;
                }
                $chars[] = $checksum;
                $chars[] = 106;
                $modules = array();
                $modules[] = array(0, 10, 0);
                foreach ($chars as $char) {
                        $block = $this->code_128_alphabet[$char];
                        foreach ($block as $i => $module) {
                                $modules[] = array(($i & 1) ^ 1, $module, 1);
                        }
                }
                $modules[] = array(0, 10, 0);
                $blocks = array(array('m' => $modules, 'l' => array($label)));
                return array('g' => 'l', 'b' => $blocks);
        }

        private function code_128_normalize($data, $dstate, $fnc1) {
                $detectcba = '/(^[0-9]{4,}|^[0-9]{2}$)|([\x60-\x7F])|([\x00-\x1F])/';
                $detectc = '/(^[0-9]{6,}|^[0-9]{4,}$)/';
                $detectba = '/([\x60-\x7F])|([\x00-\x1F])/';
                $consumec = '/(^[0-9]{2})/';
                $state = (($dstate > 0 && $dstate < 4) ? $dstate : 0);
                $abstate = ((abs($dstate) == 2) ? 2 : 1);
                $chars = array(102 + ($state ? $state : $abstate));
                if ($fnc1) $chars[] = 102;
                while (strlen($data)) {
                        switch ($state) {
                                case 0:
                                        if (preg_match($detectcba, $data, $m)) {
                                                if ($m[1]) {
                                                        $state = 3;
                                                } else if ($m[2]) {
                                                        $state = 2;
                                                } else {
                                                        $state = 1;
                                                }
                                        } else {
                                                $state = $abstate;
                                        }
                                        $chars = array(102 + $state);
                                        if ($fnc1) $chars[] = 102;
                                        break;
                                case 1:
                                        if ($dstate <= 0 && preg_match($detectc, $data, $m)) {
                                                if (strlen($m[0]) % 2) {
                                                        $data = substr($data, 1);
                                                        $chars[] = 16 + substr($m[0], 0, 1);
                                                }
                                                $state = 3;
                                                $chars[] = 99;
                                        } else {
                                                $ch = ord(substr($data, 0, 1));
                                                $data = substr($data, 1);
                                                if ($ch < 32) {
                                                        $chars[] = $ch + 64;
                                                } else if ($ch < 96) {
                                                        $chars[] = $ch - 32;
                                                } else {
                                                        if (preg_match($detectba, $data, $m)) {
                                                                if ($m[1]) {
                                                                        $state = 2;
                                                                        $chars[] = 100;
                                                                } else {
                                                                        $chars[] = 98;
                                                                }
                                                        } else {
                                                                $chars[] = 98;
                                                        }
                                                        $chars[] = $ch - 32;
                                                }
                                        }
                                        break;
                                case 2:
                                        if ($dstate <= 0 && preg_match($detectc, $data, $m)) {
                                                if (strlen($m[0]) % 2) {
                                                        $data = substr($data, 1);
                                                        $chars[] = 16 + substr($m[0], 0, 1);
                                                }
                                                $state = 3;
                                                $chars[] = 99;
                                        } else {
                                                $ch = ord(substr($data, 0, 1));
                                                $data = substr($data, 1);
                                                if ($ch >= 32) {
                                                        $chars[] = $ch - 32;
                                                } else {
                                                        if (preg_match($detectba, $data, $m)) {
                                                                if ($m[2]) {
                                                                        $state = 1;
                                                                        $chars[] = 101;
                                                                } else {
                                                                        $chars[] = 98;
                                                                }
                                                        } else {
                                                                $chars[] = 98;
                                                        }
                                                        $chars[] = $ch + 64;
                                                }
                                        }
                                        break;
                                case 3:
                                        if (preg_match($consumec, $data, $m)) {
                                                $data = substr($data, 2);
                                                $chars[] = (int)$m[0];
                                        } else {
                                                if (preg_match($detectba, $data, $m)) {
                                                        if ($m[1]) {
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

        private $code_128_alphabet = array(
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

        /* - - - - CODABAR ENCODER - - - - */

        public function codabar_encode($data) {
                $data = strtoupper(preg_replace(
                        '/[^0-9ABCDENTabcdent*.\/:+$-]/', '', $data
                ));
                $blocks = array();
                for ($i = 0, $n = strlen($data); $i < $n; $i++) {
                        if ($blocks) {
                                $blocks[] = array(
                                        'm' => array(array(0, 1, 3))
                                );
                        }
                        $char = substr($data, $i, 1);
                        $block = $this->codabar_alphabet[$char];
                        $blocks[] = array(
                                'm' => array(
                                        array(1, 1, $block[0]),
                                        array(0, 1, $block[1]),
                                        array(1, 1, $block[2]),
                                        array(0, 1, $block[3]),
                                        array(1, 1, $block[4]),
                                        array(0, 1, $block[5]),
                                        array(1, 1, $block[6]),
                                ),
                                'l' => array($char)
                        );
                }
                return array('g' => 'l', 'b' => $blocks);
        }

        private $codabar_alphabet = array(
                '0' => array(1, 1, 1, 1, 1, 2, 2),
                '1' => array(1, 1, 1, 1, 2, 2, 1),
                '4' => array(1, 1, 2, 1, 1, 2, 1),
                '5' => array(2, 1, 1, 1, 1, 2, 1),
                '2' => array(1, 1, 1, 2, 1, 1, 2),
                '-' => array(1, 1, 1, 2, 2, 1, 1),
                '$' => array(1, 1, 2, 2, 1, 1, 1),
                '9' => array(2, 1, 1, 2, 1, 1, 1),
                '6' => array(1, 2, 1, 1, 1, 1, 2),
                '7' => array(1, 2, 1, 1, 2, 1, 1),
                '8' => array(1, 2, 2, 1, 1, 1, 1),
                '3' => array(2, 2, 1, 1, 1, 1, 1),
                'C' => array(1, 1, 1, 2, 1, 2, 2),
                'D' => array(1, 1, 1, 2, 2, 2, 1),
                'A' => array(1, 1, 2, 2, 1, 2, 1),
                'B' => array(1, 2, 1, 2, 1, 1, 2),
                '*' => array(1, 1, 1, 2, 1, 2, 2),
                'E' => array(1, 1, 1, 2, 2, 2, 1),
                'T' => array(1, 1, 2, 2, 1, 2, 1),
                'N' => array(1, 2, 1, 2, 1, 1, 2),
                '.' => array(2, 1, 2, 1, 2, 1, 1),
                '/' => array(2, 1, 2, 1, 1, 1, 2),
                ':' => array(2, 1, 1, 1, 2, 1, 2),
                '+' => array(1, 1, 2, 1, 2, 1, 2),
        );
}
