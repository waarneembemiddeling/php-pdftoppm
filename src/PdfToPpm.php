<?php
/*
* (c) Waarneembemiddeling.nl
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/ 

namespace Wb\PdfToPpm;

use Alchemy\BinaryDriver\AbstractBinary;
use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ConfigurationInterface;
use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use Psr\Log\LoggerInterface;
use Wb\PdfToPpm\Exception\RuntimeException;

class PdfToPpm extends AbstractBinary
{
    /**
     * Returns the name of the driver
     *
     * @return string
     */
    public function getName()
    {
        return 'pdftoppm';
    }

    /**
     * Convert given pdf to images
     *
     * @param $inputPdf
     * @param null $destinationRootFolder
     * @return \FilesystemIterator
     * @throws Exception\RuntimeException
     */
    public function convertPdf($inputPdf, $destinationRootFolder = null, $saveAsPng = false, $resolution = null)
    {
        if (false === is_file($inputPdf)) {
            throw new RuntimeException(sprintf('Input file "%s" not found', $inputPdf));
        }

        if (null === $destinationRootFolder) {
            $destinationRootFolder = sys_get_temp_dir();
        }

        if (false === is_dir($destinationRootFolder)) {
            throw new RuntimeException(sprintf('Destination folder "%s" not found', $destinationRootFolder));
        }

        if (false === is_writable($destinationRootFolder)) {
            throw new RuntimeException('Destination folder "%s" is not writable', $destinationRootFolder);
        }

        $destinationFolder = $destinationRootFolder . '/' . uniqid('pdftoppm').'/';

        if (! mkdir($destinationFolder)) {
            throw new RuntimeException('Destination folder "%s" could not be created', $destinationFolder);
        }

        $options = $this->buildOptions($inputPdf, $destinationFolder, $saveAsPng, $resolution);

        try {
            $this->command($options);
        } catch (ExecutionFailureException $e) {
            throw new RuntimeException('PdfToPpm was unable to convert pdf to images', $e->getCode(), $e);
        }

        return new \FilesystemIterator($destinationFolder, \FilesystemIterator::SKIP_DOTS);
    }

    /**
     * @param bool $saveAsPng
     * @param $inputPdf
     * @param null $destinationRootFolder
     */
    private function buildOptions($inputPdf, $destinationFolder, $saveAsPng, $resolution = null)
    {
        $options = array();

        if ($saveAsPng) {
            $options[] = '-png';
        }

        if ($resolution) {
            $options[] = '-r';
            $options[] = $resolution;
        }

        $options[] = $inputPdf;
        $options[] = $destinationFolder;

        return $options;
    }


    /**
     * Creates the pdftoppm wrapper
     *
     * @param array|ConfigurationInterface $configuration
     * @param LoggerInterface              $logger
     *
     * @return PdfToPpm
     */
    public static function create($configuration = array(), LoggerInterface $logger = null)
    {
        if (!$configuration instanceof ConfigurationInterface) {
            $configuration = new Configuration($configuration);
        }

        $binaries = $configuration->get('pdftoppm.binaries', array('pdftoppm'));

        return static::load($binaries, $logger, $configuration);
    }
}
