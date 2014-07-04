<?php
/*
* (c) Waarneembemiddeling.nl
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/ 

namespace Wb\PdfToPpm;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Wb\PdfToPpm\Exception\RuntimeException;

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

    public function testExceptionIfInvalidInputFileIsGiven()
    {
        $exception = new RuntimeException(sprintf('Input file "%s" not found', null));
        try {
            $destinationDir = sys_get_temp_dir() . '/pdftoppm';
            @mkdir($destinationDir);

            $this->pdfToPpm->convertPdf(null, $destinationDir);
        } catch (RuntimeException $expected) {
            $this->assertEquals($expected, $exception);
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testExceptionIfDestinationRootFolderIsNotADirectory()
    {
        $destinationRootFolder = '!@#';
        $exception = new RuntimeException(sprintf('Destination folder "%s" not found', $destinationRootFolder));
        try {
            $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf', $destinationRootFolder);
        } catch (RuntimeException $expected) {
            $this->assertEquals($expected, $exception);
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testExceptionIfDirIsNotWritable()
    {
        $destinationDir = sys_get_temp_dir() . '/pdftoppm';
        @mkdir($destinationDir);
        chmod($destinationDir, '000');

        $exception = new RuntimeException(sprintf('Destination folder "%s" is not writable', $destinationDir));
        try {
            $this->pdfToPpm->convertPdf(dirname(__DIR__) . '/Resources/test_1_page.pdf', $destinationDir);
        } catch (RuntimeException $expected) {
            $this->assertEquals($expected, $exception);
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @after
     */
    public function tearDownEnsureDirExistsAndIsWritable()
    {
        chmod(sys_get_temp_dir() . '/pdftoppm', '755');
    }

}
