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
 * @SuppressWarnings("PHPMD.CyclomaticComplexity")
 *
 */

class Code93
{
    protected const CODE_93_ALPHABET = [
                '0' => [1, 3, 1, 1, 1, 2,  0],
                '1' => [1, 1, 1, 2, 1, 3,  1],
                '2' => [1, 1, 1, 3, 1, 2,  2],
                '3' => [1, 1, 1, 4, 1, 1,  3],
                '4' => [1, 2, 1, 1, 1, 3,  4],
                '5' => [1, 2, 1, 2, 1, 2,  5],
                '6' => [1, 2, 1, 3, 1, 1,  6],
                '7' => [1, 1, 1, 1, 1, 4,  7],
                '8' => [1, 3, 1, 2, 1, 1,  8],
                '9' => [1, 4, 1, 1, 1, 1,  9],
                'A' => [2, 1, 1, 1, 1, 3, 10],
                'B' => [2, 1, 1, 2, 1, 2, 11],
                'C' => [2, 1, 1, 3, 1, 1, 12],
                'D' => [2, 2, 1, 1, 1, 2, 13],
                'E' => [2, 2, 1, 2, 1, 1, 14],
                'F' => [2, 3, 1, 1, 1, 1, 15],
                'G' => [1, 1, 2, 1, 1, 3, 16],
                'H' => [1, 1, 2, 2, 1, 2, 17],
                'I' => [1, 1, 2, 3, 1, 1, 18],
                'J' => [1, 2, 2, 1, 1, 2, 19],
                'K' => [1, 3, 2, 1, 1, 1, 20],
                'L' => [1, 1, 1, 1, 2, 3, 21],
                'M' => [1, 1, 1, 2, 2, 2, 22],
                'N' => [1, 1, 1, 3, 2, 1, 23],
                'O' => [1, 2, 1, 1, 2, 2, 24],
                'P' => [1, 3, 1, 1, 2, 1, 25],
                'Q' => [2, 1, 2, 1, 1, 2, 26],
                'R' => [2, 1, 2, 2, 1, 1, 27],
                'S' => [2, 1, 1, 1, 2, 2, 28],
                'T' => [2, 1, 1, 2, 2, 1, 29],
                'U' => [2, 2, 1, 1, 2, 1, 30],
                'V' => [2, 2, 2, 1, 1, 1, 31],
                'W' => [1, 1, 2, 1, 2, 2, 32],
                'X' => [1, 1, 2, 2, 2, 1, 33],
                'Y' => [1, 2, 2, 1, 2, 1, 34],
                'Z' => [1, 2, 3, 1, 1, 1, 35],
                '-' => [1, 2, 1, 1, 3, 1, 36],
                '.' => [3, 1, 1, 1, 1, 2, 37],
                ' ' => [3, 1, 1, 2, 1, 1, 38],
                '$' => [3, 2, 1, 1, 1, 1, 39],
                '/' => [1, 1, 2, 1, 3, 1, 40],
                '+' => [1, 1, 3, 1, 2, 1, 41],
                '%' => [2, 1, 1, 1, 3, 1, 42],
                '#' => [1, 2, 1, 2, 2, 1, 43], /* ($) */
                '&' => [3, 1, 2, 1, 1, 1, 44], /* (%) */
                '|' => [3, 1, 1, 1, 2, 1, 45], /* (/) */
                '=' => [1, 2, 2, 2, 1, 1, 46], /* (+) */
                '*' => [1, 1, 1, 1, 4, 1,  0],
         ];

    protected const CODE_93_ASCIIBET = [
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
         ];

         /**
          *
          * @return array<mixed>
          */
    public function code_93_encode(string $data): array
    {
        $data = strtoupper((string) preg_replace('/[^0-9A-Za-z%+\/$ .-]/', '', $data));
        $modules = [];
        /* Start */
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 4, 1];
        $modules[] = [0, 1, 1];
        /* Data */
        $values = [];
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = substr($data, $i, 1);
            $block = $this::CODE_93_ALPHABET[$char];
            $modules[] = [1, $block[0], 1];
            $modules[] = [0, $block[1], 1];
            $modules[] = [1, $block[2], 1];
            $modules[] = [0, $block[3], 1];
            $modules[] = [1, $block[4], 1];
            $modules[] = [0, $block[5], 1];
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
        $alphabet = array_values($this::CODE_93_ALPHABET);
        for ($i = count($values) - 2, $n = count($values); $i < $n; $i++) {
            $block = $alphabet[$values[$i]];
            $modules[] = [1, $block[0], 1];
            $modules[] = [0, $block[1], 1];
            $modules[] = [1, $block[2], 1];
            $modules[] = [0, $block[3], 1];
            $modules[] = [1, $block[4], 1];
            $modules[] = [0, $block[5], 1];
        }
        /* End */
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 4, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        /* Return */
        $blocks = [['m' => $modules, 'l' => [$data]]];
        return ['g' => 'l', 'b' => $blocks];
    }

         /**
          *
          * @return array<mixed>
          */
    public function code_93_ascii_encode(string $data): array
    {
        $modules = [];
        /* Start */
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 4, 1];
        $modules[] = [0, 1, 1];
        /* Data */
        $label = '';
        $values = [];
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = substr($data, $i, 1);
            $cha = ord($char);
            if ($cha < 128) {
                if ($cha < 32 || $cha >= 127) {
                    $label .= ' ';
                } else {
                    $label .= $char;
                }
                $cha = $this::CODE_93_ASCIIBET[$cha];
                for ($j = 0, $m = strlen($cha); $j < $m; $j++) {
                    $caa = substr($cha, $j, 1);
                    $baa = $this::CODE_93_ALPHABET[$caa];
                    $modules[] = [1, $baa[0], 1];
                    $modules[] = [0, $baa[1], 1];
                    $modules[] = [1, $baa[2], 1];
                    $modules[] = [0, $baa[3], 1];
                    $modules[] = [1, $baa[4], 1];
                    $modules[] = [0, $baa[5], 1];
                    $values[] = $baa[6];
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
        $alphabet = array_values($this::CODE_93_ALPHABET);
        for ($i = count($values) - 2, $n = count($values); $i < $n; $i++) {
            $block = $alphabet[$values[$i]];
            $modules[] = [1, $block[0], 1];
            $modules[] = [0, $block[1], 1];
            $modules[] = [1, $block[2], 1];
            $modules[] = [0, $block[3], 1];
            $modules[] = [1, $block[4], 1];
            $modules[] = [0, $block[5], 1];
        }
        /* End */
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 4, 1];
        $modules[] = [0, 1, 1];
        $modules[] = [1, 1, 1];
        /* Return */
        $blocks = [['m' => $modules, 'l' => [$label]]];
        return ['g' => 'l', 'b' => $blocks];
    }
}
