
![Linear Example](https://raw.github.com/mjohndus/barcode/mjohndus/examples/images/qr-code.png)  
![Linear Example](https://raw.github.com/mjohndus/barcode/mjohndus/examples/images/linear.png)

# barcode.php

### Generate barcodes from a single PHP file. MIT license.

  * Output to PNG, GIF, JPEG, or SVG.
  * Generates: UPC-A, UPC-E, EAN-2, EAN-5, EAN-8, EAN-13
  * Generates: Code 11, Code 39, Code 93, Code 128, Codabar
  * Generates: Pharma, I25(+), S25(+), Msi(+), QR Code, and Data Matrix.
 
### Use directly as a PHP script with GET or POST:

```
barcode.php?f={format}&s={symbology}&d={data}&{options}
```

e.g.

```
barcode.php?f=png&s=upc-e&d=06543217
barcode.php?f=svg&s=qr&d=HELLO%20WORLD&sf=8&ms=r&md=0.8
```

**When using this method, you must escape non-alphanumeric characters with URL encoding, for example `%26` for `&` or `%2F` for `?`.**

### Or use as a library from another PHP script:

```
include 'barcode.php';

$generator = new barcode_generator();

/* Output directly to standard output. */
header("Content-Type: image/$format");
$generator->output_image($format, $symbology, $data, $options);

/* Create bitmap image and write to standard output. */
header('Content-Type: image/png');
$image = $generator->render_image($symbology, $data, $options);
imagepng($image);
imagedestroy($image);

/* Create bitmap image and write to file. */
$image = $generator->render_image($symbology, $data, $options);
imagepng($image, $filename);
imagedestroy($image);

/* Generate SVG markup and write to standard output. */
header('Content-Type: image/svg+xml');
$svg = $generator->render_svg($symbology, $data, $options);
echo $svg;

/* Generate SVG markup and write to file. */
$svg = $generator->render_svg($symbology, $data, $options);
file_put_contents($filename, $svg);
```

**When using this method, you must NOT use URL encoding.**

### Options:

`f` - Format. One of:
```
    png
    gif
    jpeg
    svg
```

`s` - Symbology (type of barcode). One of:
```
    upc-a        code-39         i25     qr-m   dmtx
    upc-e        code-39-ascii   i25+    qr-l   dmtx-s
    ean-2        code-93         s25     qr-q   dmtx-r
    ean-5        code-93-ascii   s25+    qr-h   gs1-dmtx
    ean-8        code-128        msi           gs1-dmtx-s
    ean-13       code-11         msi+          gs1-dmtx-r
    ean-13-pad   codabar         pharma
    ean-128

```

`d` - Data. For UPC or EAN, use `*` (Fixed bug - now working correct) for missing digit. For Codabar, use `ABCD` or `ENT*` for start and stop characters. For QR, encode in Shift-JIS for kanji mode.

`w` - Width of image. Overrides `sf` or `sx`.

`h` - Height of image. Overrides `sf` or `sy`.

`sf` - Scale factor. Default is 1 for linear barcodes or 4 for matrix barcodes.

`sx` - Horizontal scale factor. Overrides `sf`.

`sy` - Vertical scale factor. Overrides `sf`.

`p` - Padding. Default is 10 for linear barcodes or 0 for matrix barcodes.

`pv` - Top and bottom padding. Default is value of `p`.

`ph` - Left and right padding. Default is value of `p`.

`pt` - Top padding. Default is value of `pv`.

`pl` - Left padding. Default is value of `ph`.

`pr` - Right padding. Default is value of `ph`.

`pb` - Bottom padding. Default is value of `pv`.

`bc` - Background color in `#RRGGBB` format.

`cs` - Color of spaces in `#RRGGBB` format - Diabled by qr-code with option ms => r or x.

`cm` - Color of modules in `#RRGGBB` format.

`tc` - Text color in `#RRGGBB` format. Applies to linear barcodes only.

`tf` - Text font for SVG output. Default is monospace. Applies to linear barcodes only.

`ts` - Text size. For SVG output, this is in points and the default is 10. For PNG, GIF, or JPEG output, this is the GD library built-in font number from 1 to 5 and the default is 1. Applies to linear barcodes only.

`th` - Distance from text baseline to bottom of modules. Default is 10. Applies to linear barcodes only. Fixed wrong direction

```diff
+ st - Showtext -> set to 0 disables text on output Default is 1. Applies to linear barcodes only.

+ bd - draws a border -> 0 no border, 1 normal, r with radius (default).

+ sepa - adds Text Scan2pay on the right side from bottom to top.
```
`ms` - Module shape. One of: `s` for square, `r` for round, or `x` for X-shaped. Default is `s`. Applies to matrix barcodes only.

`md` - Module density. A number between 0 and 1. Default is 1. Applies to matrix barcodes only.

`wq` - Width of quiet area units. Default is 1. Use 0 to suppress quiet area.

`wm` - Width of narrow modules and spaces. Default is 1.
