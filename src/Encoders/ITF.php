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

        /* - - - - ITF ENCODER - - - - */

        public function itf_encode($data) {
                $data = preg_replace('/[^0-9]/', '', $data);
                if (strlen($data) % 2) $data = '0' . $data;
                $blocks = array();
                /* Quiet zone, start. */
                $blocks[] = array(
                        'm' => array(array(2, 10, 0))
                );
                $blocks[] = array(
                        'm' => array(
                                array(1, 1, 1),
                                array(0, 1, 1),
                                array(1, 1, 1),
                                array(0, 1, 1),
                        )
                );
                /* Data. */
                for ($i = 0, $n = strlen($data); $i < $n; $i += 2) {
                        $c1 = substr($data, $i, 1);
                        $c2 = substr($data, $i+1, 1);
                        $b1 = $this->itf_alphabet[$c1];
                        $b2 = $this->itf_alphabet[$c2];
                        $blocks[] = array(
                                'm' => array(
                                        array(1, 1, $b1[0]),
                                        array(0, 1, $b2[0]),
                                        array(1, 1, $b1[1]),
                                        array(0, 1, $b2[1]),
                                        array(1, 1, $b1[2]),
                                        array(0, 1, $b2[2]),
                                        array(1, 1, $b1[3]),
                                        array(0, 1, $b2[3]),
                                        array(1, 1, $b1[4]),
                                        array(0, 1, $b2[4]),
                                ),
                                'l' => array($c1 . $c2)
                        );
                }
                /* End, quiet zone. */
                $blocks[] = array(
                        'm' => array(
                                array(1, 1, 2),
                                array(0, 1, 1),
                                array(1, 1, 1),
                        )
                );
                $blocks[] = array(
                        'm' => array(array(2, 10, 0))
                );
                /* Return code. */
                return array('g' => 'l', 'b' => $blocks);
        }

        private $itf_alphabet = array(
                '0' => array(1, 1, 2, 2, 1),
                '1' => array(2, 1, 1, 1, 2),
                '2' => array(1, 2, 1, 1, 2),
                '3' => array(2, 2, 1, 1, 1),
                '4' => array(1, 1, 2, 1, 2),
                '5' => array(2, 1, 2, 1, 1),
                '6' => array(1, 2, 2, 1, 1),
                '7' => array(1, 1, 1, 2, 2),
                '8' => array(2, 1, 1, 2, 1),
                '9' => array(1, 2, 1, 2, 1),
        );
}
