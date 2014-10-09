php-pdftoppm
=============
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/waarneembemiddeling/php-pdftoppm?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

PHP wrapper for the [pdftoppm command][1] which is part of
[poppler-utils][2].

The Pdftoppm is a library that handles conversion of PDF files to images. It is available on variety of systems.

Available packages:
[http://pkgs.org/download/poppler-utils][3]

Usage
-------------
```
use Wb\PdfToPpm\PdfToPpm;
$pdfToPpm = PdfToPpm::create();
// $result is an instance of \FilesystemIterator
$result1 = $pdfToPpm->convertPdf('path/to/pdf');
$result2 = $pdfToPpm->convertPdf('path/to/pdf', 'path/to/other/destination/dir/then/tmp');

// Save as png
$result3 = $pdfToPpm->convertPdf('path/to/pdf', 'path/to/other/destination/dir/then/tmp', true);

// Set specific resolution
$result4 = $pdfToPpm->convertPdf('path/to/pdf', 'path/to/other/destination/dir/then/tmp', true, 300);

```

Testing
-------------

```
cp phpunit.xml.dist phpunit.xml
```

Change the phpunit.xml ```env``` ```binary``` directive if necessary.

```
composer install
```

```
php vendor/bin/phpunit
```

[1]: http://linux.die.net/man/1/pdftoppm
[2]: http://en.wikipedia.org/wiki/Poppler_(software)
[3]: http://pkgs.org/download/poppler-utils