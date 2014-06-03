<?php
/*
* (c) Waarneembemiddeling.nl
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/ 

namespace Wb\PdfToPpm;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class PdfToPpmTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdfToPpm
     */
    private $pdfToPpm;

    public function setUp()
    {
        $this->pdfToPpm = PdfToPpm::create(array(
            'pdftoppm.binaries' => getenv('binary')
        ));
    }

    public function testConvertPdfOnePages()
    {
        $result = $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf');

        $this->assertSame(1, iterator_count($result));
    }

    public function testConvertPdfMultiplePages()
    {
        $result = $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_3_pages.pdf');

        $this->assertSame(3, iterator_count($result));
    }

    public function testConvertPdfAsPpm()
    {
        $result = $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf', null);

        $mimeTypeGuesser = MimeTypeGuesser::getInstance();
        $mimeType = $mimeTypeGuesser->guess($result->current()->getPathName());

        $this->assertSame('image/x-portable-pixmap', $mimeType);
    }

    public function testConvertPdfAsPng()
    {
        $result = $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf', null, true);

        $mimeTypeGuesser = MimeTypeGuesser::getInstance();
        $mimeType = $mimeTypeGuesser->guess($result->current()->getPathName());

        $this->assertSame('image/png', $mimeType);
    }

    public function testExtractImageToGivenDir()
    {
        $destinationDir = sys_get_temp_dir() . '/pdftoppm';
        @mkdir($destinationDir);

        $result = $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf', $destinationDir);

        $this->assertSame(1, iterator_count($result));
    }
}
