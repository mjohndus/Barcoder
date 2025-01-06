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

namespace Barcoder;

//use Barcoder\Encoder\UPC;
//use Barcoder\Encoder\DMTX;
//use Barcoder\Encoder\Codes;
//use Barcoder\Encoder\Codebar;
//use Barcoder\Encoder\ITF;
//use Barcoder\Encoder\Qrcode;

class Barcoder {

	public function output_image($format, $symbology, $data, $options, $imagick) {

            if ($imagick) {
                switch (strtolower(preg_replace('/[^A-Za-z0-9]/', '', $format))) {
                        case 'png':
                                header("Content-Type: image/png");
                                $image = $this->render_image($format, $symbology, $data, $options, $imagick);
                                echo $image;
                                break;
                        case 'gif':
                                header("Content-Type: image/gif");
                                $image = $this->render_image($format, $symbology, $data, $options, $imagick);
                                echo $image;
                                break;
                        case 'jpg':
                                header("Content-Type: image/jpg");
                                $image = $this->render_image($format, $symbology, $data, $options, $imagick);
                                echo $image;
                                break;
                        case 'svg':
                                header('Content-Type: image/svg+xml');
                                echo $this->render_svg($symbology, $data, $options);
                                break;
                }
            }

            if (!$imagick) {
	    	switch (strtolower(preg_replace('/[^A-Za-z0-9]/', '', $format))) {
	    		case 'png':
	    			header('Content-Type: image/png');
	    			$image = $this->render_image($format, $symbology, $data, $options, $imagick);
	    			imagepng($image);
	    			imagedestroy($image);
	    			break;
	    		case 'gif':
	    			header('Content-Type: image/gif');
	    			$image = $this->render_image($format, $symbology, $data, $options, $imagick);
	    			imagegif($image);
	    			imagedestroy($image);
	    			break;
	    		case 'jpg': case 'jpe': case 'jpeg':
	    			header('Content-Type: image/jpeg');
	    			$image = $this->render_image($format, $symbology, $data, $options, $imagick);
	    			imagejpeg($image);
	    			imagedestroy($image);
	    			break;
	    		case 'svg':
	    			header('Content-Type: image/svg+xml');
	    			echo $this->render_svg($symbology, $data, $options);
	    			break;
	    	}
            }
	}

        public function render_image($format, $symbology, $data, $options, $imagick) {
                //if ($imagick && extension_loaded('imagick') && $symbology[0] == 'q') {
                if ($imagick && extension_loaded('imagick')) {
                    $img = $this->render_imageick($format, $symbology, $data, $options, $imagick=true);
                    return $img;
                }
                else {
                    $img = $this->render_imagegd($format, $symbology, $data, $options, $imagick=false);
                    return $img;
                }
        }

        public function render_imageick($format, $symbology, $data, $options, $imagick=true) {
                list($code, $widths, $width, $height, $x, $y, $w, $h, $bord) =
                        $this->encode_and_calculate_size($symbology, $data, $options);

                $nscale = floor($width/50);
                $sf = $nscale > 0 && $nscale < 5 ? ($nscale*6)+4 : 32;
                $bw = $nscale < 2 ? 2 : 4;
                $bb = $nscale < 2 ? 3 : $nscale*4;
                $tw = (($sf*2)+$bb);
                $ab = $nscale < 3 ? 5 : 8;

                $rd = (isset($options['sf']) && $options['sf'] == 1) ? 10 : 15;

                $image = new \Imagick();
                $image->newImage($width, $height, 'none', 'png');
                $barcode = new \imagickdraw();

                $bgcolor = (isset($options['bc']) ? $options['bc'] : '#FFFFFF');
                $bgcolor = new \ImagickPixel($bgcolor);
                $bdcolor = (isset($options['bdc']) ? $options['bdc'] : '#000000');
                $bdcolor = new \ImagickPixel($bdcolor);
                $barcode->setfillcolor($bgcolor);
                $barcode->setStrokeColor($bdcolor);
                $barcode->setStrokeWidth($bw);

                if (in_array('sepa', $options) && $symbology[0] == 'q') {

                    //$this->sepaimagick($barcode, $width, $height, $bdcolor, $bgcolor);
                        $barcode->roundRectangle(ceil($bord/2), ceil($bord/2), $width-$bord, $height-$bord, $rd, $rd);
                        $barcode->setStrokeWidth(0);
                        $sepadat = true;

                   } else if (isset($options['bd']) && $options['bd'][0] !== '0' && $options['bd'][1] == 'r' && !in_array('sepa', $options)) {
                        $barcode->roundRectangle(ceil($bord/2), ceil($bord/2), $width-$bord, $height-$bord, $rd, $rd);
                        $barcode->setStrokeWidth(0);
                        $sepadat = false;

                   } else if (isset($options['bd']) && $options['bd'][0] !== '0' && $options['bd'][1] == '0' && !in_array('sepa', $options)) {
                        $barcode->rectangle(ceil($bord/2), ceil($bord/2), $width-$bord, $height-$bord);
                        $barcode->setStrokeWidth(0);
                        $sepadat = false;

                   } else if (isset($options['bd']) && $options['bd'][0] == '0' && $options['bd'][1] == 'r' && !in_array('sepa', $options)) {
                        $barcode->setStrokeColor($bgcolor);
                        $barcode->setStrokeWidth(0);
                        //$barcode->roundRectangle(ceil($bord/2), ceil($bord/2), $width-$bord, $height-$bord, $rd, $rd);
                        $barcode->roundRectangle(0, 0, $width, $height, $rd, $rd);
                        $sepadat = false;

                   } else {
                        $barcode->setStrokeWidth(0);
                        $barcode->rectangle(-1, -1, $width, $height);
                        $sepadat = false;
                }
//echo '<pre>';
//print_r($bord);
//echo '</pre>';
                $colors = [
                           ((isset($options['cs']) && ! isset($options['ms'])) || (isset($options['ms']) && ($options['ms'] == 's') || $symbology[0] != 'q') ? $options['cs'] : '#00000000'), // not in array r + x ToDo
                           (isset($options['cm']) ? $options['cm'] : '#000000'),
                           ($options['cz'] ?? '#00000000'), // reserved for quit zones linear barcodes
                           (isset($options['tc']) ? $options['tc'] : '#777777'),
                           (isset($options['c4']) ? $options['c4'] : '0F0'),
                           (isset($options['c9']) ? $options['c9'] : '000')
                          ];

                $this->dispatch_render_imagick_gd(
                       $barcode, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick=true
                );

                if ($sepadat) {
                    $barcode->setStrokeColor($bgcolor);
                    $barcode->setfillcolor($bgcolor);
                    //$barcode->rectangle($width-20, ($height/2)-$tw, $width-3, ($height/2)+$tw);
                    $barcode->rectangle($width-10, ($height/2)-$tw, $width-($bw == 4 ? 2 : 3), ($height/2)+$tw);

                    $barcode->setStrokeColor('#00000000');
                    $barcode->setfillcolor('black');
                    $barcode->setFont(__dir__.'/../examples/fonts/FreeSans.ttf');
                    $barcode->setFontSize($sf);
                    $barcode->setTextAlignment(\Imagick::ALIGN_CENTER);
                    //$barcode->setTextUnderColor($bgcolor);
                }

                $image->drawimage($barcode);

                if ($sepadat) {
                    $image->annotateImage($barcode, $width-$ab, $height/2, -90,'Scan2pay');
                }

                //return 0; //$image->getImageBlob();
//                $image->setImageFormat('png');
                $image->setImageFormat($format);
                //return $image->getImageBlob();
                $image = $image->getImageBlob();
                $barcode->clear();
//echo '<pre>';
//print_r($image.' '.$format);
//echo '</pre>';
                return $image;
        }

	public function render_imagegd($format, $symbology, $data, $options, $imagick=false) {
		list($code, $widths, $width, $height, $x, $y, $w, $h, $bord) =
			$this->encode_and_calculate_size($symbology, $data, $options);

                $nscale = floor($width/50);
                $bb = $nscale == 1 ? 2 : 4;

		$image = imagecreatetruecolor($width, $height);
		imagesavealpha($image, true);
		$bgcolor = (isset($options['bc']) ? $options['bc'] : 'FFF');
		$bgcolor = $this->allocate_color($image, $bgcolor);
                $bdcolor = $this->allocate_color($image, '000');
                //$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $bgcolor);

//echo '<pre>';
//print_r($nscale.' '.$bb);
//echo '</pre>';

                if (in_array('sepa', $options) && $symbology[0] == 'q') {
                        //$this->bordersepa($image, $text, $font, $width, $height, $border, $bgcolor, $scale);
                        $this->sepagd($image, $width, $height, $bdcolor, $bgcolor);

                   } else if (isset($options['bd']) && $options['bd'][0] !== '0' && $options['bd'][1] == 'r' && !in_array('sepa', $options)) {
                          $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
                          imagefill($image, 0, 0, $trans_colour);
                          imagesetthickness($image, $bord);
                          $this->imagerectangleround($image, $bord, $bord, $width - $bord, $height - $bord, 10, $bdcolor);
                          //$this->imagerectangleround($image, $bb/2, $bb/2, $width - ($bb/2), $height - ($bb/2), 10, $bdcolor);
                          imagefilltoborder($image, 10, 10, $bdcolor, $bgcolor);

                   } else if (isset($options['bd']) && $options['bd'][0] == '0' && $options['bd'][1] == 'r' && !in_array('sepa', $options)) {
                          $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
                          imagefill($image, 0, 0, $trans_colour);
                          imagesetthickness($image, $bord);
                          $this->imagerectangleround($image, $bord, $bord, $width - $bord, $height - $bord, 10, $bgcolor);
                          imagefilltoborder($image, 10, 10, $bgcolor, $bgcolor);

                   } else if (isset($options['bd']) && $options['bd'][0] !== '0' && $options['bd'][1] == '0' && !in_array('sepa', $options)) {
                          imagesetthickness($image, $bord);
                          //imagerectangle($image, $bord/2, $bord/2, $width - ($bord/2), $height - ($bord/2), $bdcolor);
                          imagerectangle($image, $bord/2, $bord/2, $width - $bord, $height - $bord, $bdcolor);

                   } else {
                          imagerectangle($image, -1, -1, $width, $height, $bdcolor);
                }

		$colors = array(
			((isset($options['cs']) && ! isset($options['ms'])) || (isset($options['ms']) && ($options['ms'] == 's') || $symbology[0] != 'q') ? $options['cs'] : '#00000000'), // not in array r + x ToDo
			(isset($options['cm']) ? $options['cm'] : '000'),
                        ($options['cz'] ?? '#00000000'), // reserved for quit zones linear barcodes
			(isset($options['c3']) ? $options['c3'] : 'FF0'),
			(isset($options['c4']) ? $options['c4'] : '0F0'),
			(isset($options['c9']) ? $options['c9'] : '000')
		);
		foreach ($colors as $i => $color) {
			$colors[$i] = $this->allocate_color($image, $color);
		}
                $this->dispatch_render_imagick_gd(
		       $image, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick=false
		);

//                ob_start();
//                imagepng($image);
//                return ob_get_clean();

                ob_start();
                // case png,gif,jpeg einbauen
                imagepng($image);
                //return ob_get_clean();
                $image = ob_get_clean();
                return $image;
                //$imagestring = ob_get_contents(); // $image  read from buffer
                //ob_end_clean(); // delete buffer
                //return $imagestring; //$image
	}

	public function render_svg($symbology, $data, $options) {
		list($code, $widths, $width, $height, $x, $y, $w, $h, $bord) =
			$this->encode_and_calculate_size($symbology, $data, $options);

                $nscale = floor($width/50);
                $bw = $nscale < 2 ? 2 : 4;

		$svg  = '<?xml version="1.0"?>';
		$svg .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"';
		$svg .= ' width="' . $width . '" height="' . $height . '"';
		$svg .= ' viewBox="0 0 ' . $width . ' ' . $height . '"><g>';
		$bgcolor = (isset($options['bc']) ? $options['bc'] : 'white');
                $bdcolor = 'black';
                //$bord1 = 8;
                $rd = (isset($options['sf']) && $options['sf'] == 1) ? 10 : 15;

		if ($bgcolor) {
			$svg .= '<rect x="'.($bord/2).'" y="'.($bord/2).'"';

                    if ((isset($options['bd']) && $options['bd'][1] == 'r') || in_array('sepa', $options)) {
                        $svg .= ' rx="'.$rd.'" ry="'.$rd.'"';
//                        } else {
//                        $svg .= ' x="0" y="0"';
                    }
			$svg .= ' width="' . ($width-$bord) . '" height="' . ($height-$bord) . '"';
			$svg .= ' fill="' . htmlspecialchars($bgcolor) . '"';
                    if (in_array('sepa', $options) && $symbology[0] == 'q') {
                            $svg .= $this->sepasvg($width, $height, $bdcolor, $bgcolor);
                    }
                    else if (isset($options['bd']) && $options['bd'][0] !== '0' && (!in_array('sepa', $options))) {
                        //if (isset($options['bd'])) {
                        $svg .= ' stroke-width="'.$bord.'" stroke="' . $bdcolor . '"/>';
                    }

                    else {
                        $svg .= '/>';
                    }
                }
		$colors = array(
                        ((isset($options['cs']) && ! isset($options['ms'])) || (isset($options['ms']) && $options['ms'] == 's') || $symbology[0] != 'q' ? $options['cs'] : '#00000000'), // not in array r + x ToDo
			(isset($options['cm']) ? $options['cm'] : 'black'),
                        ($options['cz'] ?? '#00000000'), // reserved for quit zones linear barcodes
			(isset($options['c3']) ? $options['c3'] : '#FFFF00'),
			(isset($options['c4']) ? $options['c4'] : '#00FF00'),
			(isset($options['c9']) ? $options['c9'] : 'black')
		);
		$svg .= $this->dispatch_render_svg(
			$code, $x, $y, $w, $h, $colors, $widths, $options
		);
		$svg .= '</g></svg>';
		return $svg;
	}

	/* - - - - INTERNAL FUNCTIONS - - - - */

	private function encode_and_calculate_size($symbology, $data, $options) {
		$code = $this->dispatch_encode($symbology, $data, $options);
		$widths = array(
			(isset($options['wq']) ? (int)$options['wq'] : 1),
			(isset($options['wm']) ? (int)$options['wm'] : 1),
			(isset($options['ww']) ? (int)$options['ww'] : 3),
			(isset($options['wn']) ? (int)$options['wn'] : 1),
			(isset($options['wt']) ? (int)$options['wt'] : 0),
			(isset($options['w5']) ? (int)$options['w5'] : 1),
			(isset($options['w6']) ? (int)$options['w6'] : 1),
			(isset($options['w7']) ? (int)$options['w7'] : 1),
			(isset($options['w8']) ? (int)$options['w8'] : 1),
			(isset($options['w9']) ? (int)$options['w9'] : 1),
		);
		$size = $this->dispatch_calculate_size($code, $widths, $options);
		$dscale = ($code && isset($code['g']) && $code['g'] == 'm') ? 4 : 1;
		$scale = (isset($options['sf']) ? (float)$options['sf'] : $dscale);
		$scalex = (isset($options['sx']) ? (float)$options['sx'] : $scale);
		$scaley = (isset($options['sy']) ? (float)$options['sy'] : $scale);
		$dpadding = ($code && isset($code['g']) && $code['g'] == 'm') ? 0 : 10;
		$padding = (isset($options['p']) ? (int)$options['p'] : $dpadding);
		$vert = (isset($options['pv']) ? (int)$options['pv'] : $padding);
		$horiz = (isset($options['ph']) ? (int)$options['ph'] : $padding);
		$top = (isset($options['pt']) ? (int)$options['pt'] : $vert);
		$left = (isset($options['pl']) ? (int)$options['pl'] : $horiz);
		$right = (isset($options['pr']) ? (int)$options['pr'] : $horiz);
		$bottom = (isset($options['pb']) ? (int)$options['pb'] : $vert);
                $bord = (isset($options['bd']) && !in_array('sepa', $options) && in_array($options['bd'][0], range(2, 4, 2))) ? (int)$options['bd'][0] : 4;
		$dwidth = ceil($size[0] * $scalex) + $left + $right;
		$dheight = ceil($size[1] * $scaley) + $top + $bottom;
                $iwidth = (isset($options['w']) ? (int)$options['w'] + $left + $right : $dwidth);
                $iheight = (isset($options['h']) ? (int)$options['h'] + $top + $bottom : $dheight);
		$swidth = $iwidth - $left - $right;
		$sheight = $iheight - $top - $bottom;
		return array(
			$code, $widths, $iwidth, $iheight,
			$left, $top, $swidth, $sheight, $bord
		);
	}

        private function sepaimagick($barcode, $width, $height, $bdcolor, $bgcolor) {

                $nscale = floor($width/50);
                $sf = $nscale > 0 && $nscale < 5 ? ($nscale*6)+4 : 32;
                $bw = $nscale < 2 ? 2 : 4;
                $bb = $nscale < 2 ? 3 : $nscale*4;
                $tw = (($sf*2)+$bb);
                $ab = $nscale < 3 ? 5 : 8;

                $barcode->setStrokeColor($bgcolor);
                $barcode->setfillcolor($bgcolor);
                //$barcode->rectangle($width-20, ($height/2)-$tw, $width-3, ($height/2)+$tw);
                $barcode->rectangle($width-10, ($height/2)-$tw, $width-($bw == 4 ? 2 : 3), ($height/2)+$tw);

                $barcode->setStrokeColor('#00000000');
                $barcode->setfillcolor('black');
                $barcode->setFont(__dir__.'/../examples/fonts/FreeSans.ttf');
                $barcode->setFontSize($sf);
                $barcode->setTextAlignment(\Imagick::ALIGN_CENTER);
                //$barcode->setTextUnderColor($bgcolor);
                $sepadat = true;
        }

        private function sepasvg($width, $height, $bdcolor, $bgcolor) {

                $text = 'Scan2pay';
                $font = __dir__.'/../examples/fonts/FreeSans.ttf';
                //$font = 'FreeSans';
                $dc = 'red';
                $nscale = floor($width/50);
                $sf = $nscale > 0 && $nscale < 5 ? ($nscale*6)+4 : 32;
                $bw = $nscale < 2 ? 2 : 4;
                $bb = $nscale < 2 ? 3 : $nscale*4;
                $ab = $nscale < 3 ? 4 : 7;
                $tw = (($sf*2)+$bb);
                $ty = ($height/2)-$tw;
                $theight = ($tw*2);
                $tt = $bw == 2 ? 1 : 0;

                $svg = ' stroke-width="'.$bw.'" stroke="' . $bdcolor . '" />';
                $svg .= '<rect x="'.($width-10).'" y="'.$ty.'" width="'.(10-$tt).'" height="'.$theight.'" fill="'.$bgcolor.'" />';
                $svg .= '<text fill="'.$bdcolor.'" font-size="'.$sf.'" font-family="'.$font.'" x="'.(-$height/2).'" y="'.($width-$ab).'"';
                $svg .= ' text-anchor="middle" transform="rotate(-90)">Scan2pay</text>';

                return $svg;
        }

        //private function bordersepa($image, $text, $font, $width, $height, $border, $bgcolor, $scale) {
        private function sepagd($image, $width, $height, $bdcolor, $bgcolor) {
                $text = 'Scan2pay';
                $font = __dir__.'/../examples/fonts/FreeSans.ttf';
                //$font = '/home/web/basics/barqr/FreeSans.ttf';
                // --> transparenter hintergrund
                $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $trans_colour);
                // --> round border
                $nscale = floor($width/50);
                $bb = $nscale == 1 ? 2 : 4;
                imagesetthickness($image, $bb);
                $this->imagerectangleround($image, $bb/2, $bb/2, $width - ($bb/2), $height - ($bb/2), 10, $bdcolor);
                imagefilltoborder($image, 10, 10, $bdcolor, $bgcolor);
                // --> only text --> in rechteck for position
                $sf = $nscale > 0 && $nscale < 5 ? ($nscale*4)+4 : $sf = 24;
                $box = imageftbbox($sf, 0, $font, $text);
                $xh = round(abs($box[4]/2));
                $xw = round(abs($box[1]));
                imagefilledrectangle($image, $width-($sf*1.5), round(($height/4)-$sf), $width-1, round(($height/4*3)+$sf), $bgcolor);
                imagefttext($image, $sf, 90, $width-$xw, round(($height/2)+$xh), $bdcolor, $font, $text);
        }

        private function imagerectangleround($img, $x1, $y1, $x2, $y2, $radius, $color) {
                $radius = min($radius, floor(min(($x2-$x1)/2, ($y2-$y1)/2)));

                imageline($img, $x1+$radius, $y1, $x2-$radius, $y1, $color);
                imageline($img, $x1+$radius, $y2, $x2-$radius, $y2, $color);
                imageline($img, $x1, $y1+$radius, $x1, $y2-$radius, $color);
                imageline($img, $x2, $y1+$radius, $x2, $y2-$radius, $color);

                imagearc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color);
                imagearc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color);
                imagearc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color);
                imagearc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color);
        }

	private function allocate_color($image, $color) {
		$color = preg_replace('/[^0-9A-Fa-f]/', '', $color);
		switch (strlen($color)) {
			case 1:
				$v = hexdec($color) * 17;
				return imagecolorallocate($image, $v, $v, $v);
			case 2:
				$v = hexdec($color);
				return imagecolorallocate($image, $v, $v, $v);
			case 3:
				$r = hexdec(substr($color, 0, 1)) * 17;
				$g = hexdec(substr($color, 1, 1)) * 17;
				$b = hexdec(substr($color, 2, 1)) * 17;
				return imagecolorallocate($image, $r, $g, $b);
			case 4:
				$a = hexdec(substr($color, 0, 1)) * 17;
				$r = hexdec(substr($color, 1, 1)) * 17;
				$g = hexdec(substr($color, 2, 1)) * 17;
				$b = hexdec(substr($color, 3, 1)) * 17;
				$a = round((255 - $a) * 127 / 255);
				return imagecolorallocatealpha($image, $r, $g, $b, $a);
			case 6:
				$r = hexdec(substr($color, 0, 2));
				$g = hexdec(substr($color, 2, 2));
				$b = hexdec(substr($color, 4, 2));
				return imagecolorallocate($image, $r, $g, $b);
			case 8:
				$a = hexdec(substr($color, 0, 2));
				$r = hexdec(substr($color, 2, 2));
				$g = hexdec(substr($color, 4, 2));
				$b = hexdec(substr($color, 6, 2));
				$a = round((255 - $a) * 127 / 255);
				return imagecolorallocatealpha($image, $r, $g, $b, $a);
			default:
				return imagecolorallocatealpha($image, 0, 0, 0, 127);
		}
	}

	/* - - - - DISPATCH - - - - */

	private function dispatch_encode($symbology, $data, $options) {
		switch (strtolower(preg_replace('/[^A-Za-z0-9]/', '', $symbology))) {
                        case 'upca'       : return (new Encoder\UPC)->upc_a_encode($data);
                        case 'upce'       : return (new Encoder\UPC)->upc_e_encode($data);
                        case 'ean13nopad' : return (new Encoder\UPC)->ean_13_encode($data, ' ');
                        case 'ean13pad'   : return (new Encoder\UPC)->ean_13_encode($data, '>');
                        case 'ean13'      : return (new Encoder\UPC)->ean_13_encode($data, '>');
                        case 'ean8'       : return (new Encoder\UPC)->ean_8_encode($data);
                        case 'code39'     : return (new Encoder\Code)->code_39_encode($data);
                        case 'code39ascii': return (new Encoder\Code)->code_39_ascii_encode($data);
                        case 'code93'     : return (new Encoder\Code)->code_93_encode($data);
                        case 'code93ascii': return (new Encoder\Code)->code_93_ascii_encode($data);
                        case 'code128'    : return (new Encoder\Code)->code_128_encode($data, 0,false);
                        case 'code128a'   : return (new Encoder\Code)->code_128_encode($data, 1,false);
                        case 'code128b'   : return (new Encoder\Code)->code_128_encode($data, 2,false);
                        case 'code128c'   : return (new Encoder\Code)->code_128_encode($data, 3,false);
                        case 'code128ac'  : return (new Encoder\Code)->code_128_encode($data,-1,false);
                        case 'code128bc'  : return (new Encoder\Code)->code_128_encode($data,-2,false);
                        case 'ean128'     : return (new Encoder\Code)->code_128_encode($data, 0, true);
                        case 'ean128a'    : return (new Encoder\Code)->code_128_encode($data, 1, true);
                        case 'ean128b'    : return (new Encoder\Code)->code_128_encode($data, 2, true);
                        case 'ean128c'    : return (new Encoder\Code)->code_128_encode($data, 3, true);
                        case 'ean128ac'   : return (new Encoder\Code)->code_128_encode($data,-1, true);
                        case 'ean128bc'   : return (new Encoder\Code)->code_128_encode($data,-2, true);
                        case 'codabar'    : return (new Encoder\Codebar)->codabar_encode($data);
                        case 'itf'        : return (new Encoder\ITF)->itf_encode($data);
                        case 'itf14'      : return (new Encoder\ITF)->itf_encode($data);
                        case 'qr'         : return (new Encoder\Qrcode)->qr_encode($data, 0);
                        case 'qrl'        : return (new Encoder\Qrcode)->qr_encode($data, 0);
                        case 'qrm'        : return (new Encoder\Qrcode)->qr_encode($data, 1);
                        case 'qrq'        : return (new Encoder\Qrcode)->qr_encode($data, 2);
                        case 'qrh'        : return (new Encoder\Qrcode)->qr_encode($data, 3);
                        case 'dmtx'       : return (new Encoder\Linear)->dmtx_encode($data,false,false);
                        case 'dmtxs'      : return (new Encoder\Linear)->dmtx_encode($data,false,false);
                        case 'dmtxr'      : return (new Encoder\Linear)->dmtx_encode($data, true,false);
                        case 'gs1dmtx'    : return (new Encoder\Linear)->dmtx_encode($data,false, true);
                        case 'gs1dmtxs'   : return (new Encoder\Linear)->dmtx_encode($data,false, true);
                        case 'gs1dmtxr'   : return (new Encoder\Linear)->dmtx_encode($data, true, true);
		}
		return null;
	}

	private function dispatch_calculate_size($code, $widths, $options) {
		if ($code && isset($code['g']) && $code['g']) {
			switch ($code['g']) {
				case 'l':
					return $this->linear_calculate_size($code, $widths);
				case 'm':
					return $this->matrix_calculate_size($code, $widths);
			}
		}
		return array(0, 0);
	}

        private function dispatch_render_imagick_gd(
                $image, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick
        ) {
                if ($code && isset($code['g']) && $code['g']) {
                        switch ($code['g']) {
                                case 'l':
                                if ($imagick) {
                                        $this->linear_render_imagick(
                                                $image, $code, $x, $y, $w, $h,
                                                $colors, $widths, $options, $imagick
                                        );
                                } else {
                                        $this->linear_render_imagegd(
                                                $image, $code, $x, $y, $w, $h,
                                                $colors, $widths, $options, $imagick
                                        );
                                }
                                        break;
                                case 'm':
                                        $this->matrix_render_imagick_gd(
                                                $image, $code, $x, $y, $w, $h,
                                                $colors, $widths, $options, $imagick
                                        );
                                        break;
                                        //return $blob;
                        }
                }
        }

	private function dispatch_render_svg(
		$code, $x, $y, $w, $h, $colors, $widths, $options
	) {
		if ($code && isset($code['g']) && $code['g']) {
			switch ($code['g']) {
				case 'l':
					return $this->linear_render_svg(
						$code, $x, $y, $w, $h,
						$colors, $widths, $options
					);
				case 'm':
					return $this->matrix_render_svg(
						$code, $x, $y, $w, $h,
						$colors, $widths, $options
					);
			}
		}
		return '';
	}

	/* - - - - LINEAR BARCODE RENDERER - - - - */

	private function linear_calculate_size($code, $widths) {
		$width = 0;
		foreach ($code['b'] as $block) {
			foreach ($block['m'] as $module) {
				$width += $module[1] * $widths[$module[2]];
			}
		}
		return array($width, 80);
	}

        private function linear_render_imagick(
                $image, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick
        ) {
                $showtext = (isset($options['st']) && (int)$options['st'] == 0 ? false : true);
                $textheight = (isset($options['th']) ? (int)$options['th'] : 20);
                $textsize = (isset($options['ts']) ? (int)$options['ts'] : 16);
                $textbase = (isset($options['tb']) && (int)$options['tb'] !== 0 ? (int)$options['tb'] : 0);
                //$textfont = (isset($options['tf']) ? (string)$options['tf'] : '/dat/Fonts/fonts/mscorefont/Times_New_Roman.ttf');
                $textfont = (isset($options['tf']) ? (string)$options['tf'] : __dir__.'/../examples/fonts/FreeMono.ttf');
                $textcolor = (isset($options['tc']) ? (int)$options['tc'] : '000');

                $width = 0;
                foreach ($code['b'] as $block) {
                        foreach ($block['m'] as $module) {
                                $width += $module[1] * $widths[$module[2]];
                        }
                }
                if ($width) {
                        $scale = $w / $width;
                        $scale = (($scale > 1) ? floor($scale) : 1);
                        $x = floor($x + ($w - $width * $scale) / 2);
                } else {
                        $scale = 1;
                        $x = floor($x + $w / 2);
                }
                foreach ($code['b'] as $block) {
                        if (isset($block['l'])) {
                                $label = $block['l'][0];
                                $ly = (isset($block['l'][1]) ? (float)$block['l'][1] : 1);
                                $lx = (isset($block['l'][2]) ? (float)$block['l'][2] : 0.5);
                                //$my = round($y + min($h, $h + ($ly - 1) * $textheight));
                                $th1 = $textheight >= 0  ? $th1 = $textheight : $th1 = 0;
                                //$th1 = $textheight > 0 ?: 0;
                                $my = round($y + min($h, $h + ($ly - 1) * $th1));
                                $ly = ($y + $h - ($ly * $textheight) + $textsize - $textbase);
                                //$ly = round($ly + $textsize - 2);
                        } else {
                                $label = null;
                                $my = $y + $h;
                        }
                        $mx = $x;
                        //$image->setFont(__dir__.'/FreeMono.ttf');
                        $image->setFont($textfont);
                        $image->setFontSize($textsize);
                        foreach ($block['m'] as $module) {
                                $mc = $colors[$module[0]];
                                $mw = $mx + $module[1] * $widths[$module[2]] * $scale;
                                $image->setfillcolor($mc);
                                $image->setStrokeColor($mc);
                                $image->setStrokeWidth(0);
                                $image->rectangle($mx, $y, $mw - 1, $my - 1);
                                $mx = $mw;
                        }
                        if (!is_null($label) and $showtext) {
                                $lx = ($x + ($mx - $x) * $lx);
                                $lw = $textsize - 3 * strlen($label);
                                $lx = round($lx - $lw / 2);
                                $image->setfillcolor('#000000');
                                $image->setStrokeColor('#000000');
                                $image->setStrokeWidth(0);
                                $image->annotation($lx, $ly, $label);
                        }
                        $x = $mx;
                }
        }

	private function linear_render_imagegd(
		$image, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick
	) {
                $showtext = (isset($options['st']) && $options['st'] == 0 ? false : true);
                $textheight = (isset($options['th']) ? (int)$options['th'] : 20);
		$textsize = (isset($options['ts']) ? (int)$options['ts'] : 1);
                $textbase = (isset($options['tb']) && (int)$options['tb'] !== 0 ? (int)$options['tb'] : 0);
                $textfont = (isset($options['tf']) ? (string)$options['tf'] : '');
		$textcolor = (isset($options['tc']) ? (string)$options['tc'] : '#666666');
		$textcolor = $this->allocate_color($image, $textcolor);
		$width = 0;
		foreach ($code['b'] as $block) {
			foreach ($block['m'] as $module) {
				$width += $module[1] * $widths[$module[2]];
			}
		}
//echo '<pre>';
//print_r($code);
//echo '</pre>';
		if ($width) {
			$scale = $w / $width;
			$scale = (($scale > 1) ? floor($scale) : 1);
			$x = floor($x + ($w - $width * $scale) / 2);
		} else {
			$scale = 1;
			$x = floor($x + $w / 2);
		}
		foreach ($code['b'] as $block) {
			if (isset($block['l'])) {
				$label = $block['l'][0];
				$ly = (isset($block['l'][1]) ? (float)$block['l'][1] : 1);
				$lx = (isset($block['l'][2]) ? (float)$block['l'][2] : 0.5);
                                $th1 = $textheight >= 0  ? $th1 = $textheight : $th1 = 0;
				$my = round($y + min($h, $h + ($ly - 1) * $th1));
				$ly = ($y + $h - ($ly * $textheight) - $textbase);
			} else {
				$label = null;
				$my = $y + $h;
			}
			$mx = $x;
			foreach ($block['m'] as $module) {
				$mc = $colors[$module[0]];
				$mw = $mx + $module[1] * $widths[$module[2]] * $scale;
				imagefilledrectangle($image, $mx, $y, $mw - 1, $my - 1, $mc);
				$mx = $mw;
//echo '<pre>';
//print_r($block);
//echo '</pre>';
                        }
			if (!is_null($label) and $showtext) {
				$lx = ($x + ($mx - $x) * $lx);
				$lw = imagefontwidth($textsize) * strlen($label);
				$lx = round($lx - $lw / 2);
                                if ($textfont !=  '') {
                                    imagettftext($image, $textsize, 0, $lx, $ly + $textsize, $textcolor, $textfont, $label);
                                    } else {
                                    imagestring($image, $textsize, $lx, $ly, $label, $textcolor);
                                }
			}
			$x = $mx;
		}
	}

	private function linear_render_svg(
		$code, $x, $y, $w, $h, $colors, $widths, $options
	) {
                $showtext = (isset($options['st']) && (int)$options['st'] == 0 ? false : true);
                $textheight = (isset($options['th']) ? (int)$options['th'] : 20);
		$textsize = (isset($options['ts']) ? (int)$options['ts'] : 10);
                $textbase = (isset($options['tb']) && (int)$options['tb'] !== 0 ? (int)$options['tb'] : 0);
                $textfont = (isset($options['tf']) ? (string)$options['tf'] : 'monospace');
		$textcolor = (isset($options['tc']) ? (string)$options['tc'] : 'black');
		$width = 0;
		foreach ($code['b'] as $block) {
			foreach ($block['m'] as $module) {
				$width += $module[1] * $widths[$module[2]];
			}
		}
		if ($width) {
			$scale = $w / $width;
			if ($scale > 1) {
				$scale = floor($scale);
				$x = floor($x + ($w - $width * $scale) / 2);
			}
		} else {
			$scale = 1;
			$x = floor($x + $w / 2);
		}
		$tx = 'translate(' . $x . ' ' . $y . ')';
		if ($scale != 1) $tx .= ' scale(' . $scale . ' 1)';
		$svg = '<g transform="' . htmlspecialchars($tx) . '">';
		$x = 0;
		foreach ($code['b'] as $block) {
			if (isset($block['l'])) {
				$label = $block['l'][0];
				$ly = (isset($block['l'][1]) ? (float)$block['l'][1] : 1);
				$lx = (isset($block['l'][2]) ? (float)$block['l'][2] : 0.5);
                                $th1 = $textheight >= 0  ? $th1 = $textheight : $th1 = 0;
                                $my = min($h, $h + ($ly - 1) * $th1);
				$ly = ($h + 5 - ($ly * $textheight) + $textsize - $textbase);
			} else {
				$label = null;
				$my = $h;
			}
			$svg .= '<g>';
			$mx = $x;
			foreach ($block['m'] as $module) {
				$mc = htmlspecialchars($colors[$module[0]]);
				$mw = $module[1] * $widths[$module[2]];
				if ($mc) {
					$svg .= '<rect';
					$svg .= ' x="' . $mx . '" y="0"';
					$svg .= ' width="' . $mw . '"';
					$svg .= ' height="' . $my . '"';
					$svg .= ' fill="' . $mc . '"/>';
				}
				$mx += $mw;
			}
			if (!is_null($label) and $showtext) {
				$lx = ($x + ($mx - $x) * $lx);
				$svg .= '<text';
				$svg .= ' x="' . $lx . '" y="' . $ly . '"';
				$svg .= ' text-anchor="middle"';
				$svg .= ' font-family="'.htmlspecialchars($textfont).'"';
				$svg .= ' font-size="'.htmlspecialchars($textsize).'"';
				$svg .= ' fill="'.htmlspecialchars($textcolor).'">';
				$svg .= htmlspecialchars($label);
				$svg .= '</text>';
			}
			$svg .= '</g>';
			$x = $mx;
		}
		return $svg . '</g>';
	}

	/* - - - - MATRIX BARCODE RENDERER - - - - */

	private function matrix_calculate_size($code, $widths) {
		$width = (
			$code['q'][3] * $widths[0] +
			$code['s'][0] * $widths[1] +
			$code['q'][1] * $widths[0]
		);
		$height = (
			$code['q'][0] * $widths[0] +
			$code['s'][1] * $widths[1] +
			$code['q'][2] * $widths[0]
		);
		return array($width, $height);
	}

        private function matrix_render_imagick_gd(
                $image, $code, $x, $y, $w, $h, $colors, $widths, $options, $imagick
        ) {
                $shape = (isset($options['ms']) ? strtolower($options['ms']) : '');
                $density = (isset($options['md']) ? (float)$options['md'] : 1);
                list($width, $height) = $this->matrix_calculate_size($code, $widths);
                if ($width && $height) {
                        $scale = min($w / $width, $h / $height);
                        $scale = (($scale > 1) ? floor($scale) : 1);
                        $x = floor($x + ($w - $width * $scale) / 2);
                        $y = floor($y + ($h - $height * $scale) / 2);
                } else {
                        $scale = 1;
                        $x = floor($x + $w / 2);
                        $y = floor($y + $h / 2);
                }
                $x += $code['q'][3] * $widths[0] * $scale;
                $y += $code['q'][0] * $widths[0] * $scale;
                $wh = $widths[1] * $scale;
                foreach ($code['b'] as $by => $row) {
                        $y1 = $y + $by * $wh;
                        foreach ($row as $bx => $color) {
                                $x1 = $x + $bx * $wh;
                                $mc = $colors[$color ? 1 : 0];
                                $this->matrix_dot_imagick_gd(
                                       $image, $x1, $y1, $wh, $wh, $mc, $shape, $density, $imagick
                                );
                        }
                }
                //return $blob;
        }

	private function matrix_render_svg(
		$code, $x, $y, $w, $h, $colors, $widths, $options
	) {
		$shape = (isset($options['ms']) ? strtolower($options['ms']) : '');
		$density = (isset($options['md']) ? (float)$options['md'] : 1);
		list($width, $height) = $this->matrix_calculate_size($code, $widths);
		if ($width && $height) {
			$scale = min($w / $width, $h / $height);
			if ($scale > 1) $scale = floor($scale);
			$x = floor($x + ($w - $width * $scale) / 2);
			$y = floor($y + ($h - $height * $scale) / 2);
		} else {
			$scale = 1;
			$x = floor($x + $w / 2);
			$y = floor($y + $h / 2);
		}
		$tx = 'translate(' . $x . ' ' . $y . ')';
		if ($scale != 1) $tx .= ' scale(' . $scale . ' ' . $scale . ')';
		$svg = '<g transform="' . htmlspecialchars($tx) . '">';
		$x = $code['q'][3] * $widths[0];
		$y = $code['q'][0] * $widths[0];
		$wh = $widths[1];
		foreach ($code['b'] as $by => $row) {
			$y1 = $y + $by * $wh;
			foreach ($row as $bx => $color) {
				$x1 = $x + $bx * $wh;
				$mc = $colors[$color ? 1 : 0];
				if ($mc) {
					$svg .= $this->matrix_dot_svg(
						$x1, $y1, $wh, $wh, $mc, $shape, $density
					);
				}
			}
		}
		return $svg . '</g>';
	}

        private function matrix_dot_imagick_gd($image, $x, $y, $w, $h, $mc, $ms, $md, $imagick) {
                if ($imagick) {
                    $image->setfillcolor($mc);
                    $image->setStrokeColor($mc);
                    $image->setStrokeWidth(1.5);
                  } else {
                    imagesetthickness($image, 2);
                }
                switch ($ms) {
                        default:
                                $x = floor($x + (1 - $md) * $w / 2);
                                $y = floor($y + (1 - $md) * $h / 2);
                                $w = ceil($w * $md);
                                $h = ceil($h * $md);
                                if ($imagick) {
                                    $image->rectangle($x, $y, $x+$w-1, $y+$h-1);
                                    } else {
                                    imagefilledrectangle($image, $x, $y, $x+$w-1, $y+$h-1, $mc);
                                }
                                break;
                        case 'r':
                                $cx = floor($x + $w / 2);
                                $cy = floor($y + $h / 2);
                                $dx = ceil($w * $md);
                                $dy = ceil($h * $md);
                                if ($imagick) {
                                $image->ellipse($cx, $cy, ($dx/2), ($dy/2), 0, 360);
                                    } else {
                                imagefilledellipse($image, $cx, $cy, $dx, $dy, $mc);
                                }
                                break;
                        case 'x':
                                $x = floor($x + (1 - $md) * $w / 2);
                                $y = floor($y + (1 - $md) * $h / 2);
                                $w = ceil($w * $md);
                                $h = ceil($h * $md);
                                if ($imagick) {
                                $image->line($x, $y, $x+$w-1, $y+$h-1);
                                $image->line($x, $y+$h-1, $x+$w-1, $y);
                                    } else {
                                imageline($image, $x, $y, $x+$w-1, $y+$h-1, $mc);
                                imageline($image, $x, $y+$h-1, $x+$w-1, $y, $mc);
                                }
                                break;
                }
        }

	private function matrix_dot_svg($x, $y, $w, $h, $mc, $ms, $md) {
		switch ($ms) {
			default:
				$x += (1 - $md) * $w / 2;
				$y += (1 - $md) * $h / 2;
				$w *= $md;
				$h *= $md;
				$svg  = '<rect x="' . $x . '" y="' . $y . '"';
				$svg .= ' width="' . $w . '" height="' . $h . '"';
				$svg .= ' fill="' . $mc . '"/>';
				return $svg;
			case 'r':
				$cx = $x + $w / 2;
				$cy = $y + $h / 2;
				$rx = $w * $md / 2;
				$ry = $h * $md / 2;
				$svg  = '<ellipse cx="' . $cx . '" cy="' . $cy . '"';
				$svg .= ' rx="' . $rx . '" ry="' . $ry . '"';
				$svg .= ' fill="' . $mc . '"/>';
				return $svg;
			case 'x':
				$x1 = $x + (1 - $md) * $w / 2;
				$y1 = $y + (1 - $md) * $h / 2;
				$x2 = $x + $w - (1 - $md) * $w / 2;
				$y2 = $y + $h - (1 - $md) * $h / 2;
				$svg  = '<line x1="' . $x1 . '" y1="' . $y1 . '"';
				$svg .= ' x2="' . $x2 . '" y2="' . $y2 . '"';
				$svg .= ' stroke="' . $mc . '"';
				$svg .= ' stroke-width="' . ($md / 5) . '"/>';
				$svg .= '<line x1="' . $x1 . '" y1="' . $y2 . '"';
				$svg .= ' x2="' . $x2 . '" y2="' . $y1 . '"';
				$svg .= ' stroke="' . $mc . '"';
				$svg .= ' stroke-width="' . ($md / 5) . '"/>';
				return '<g>' . $svg . '</g>';
		}
	}
}
