<?php

namespace Zf2Interspire;

use XMLWriter;

class Xml extends Configurable {

    public $startElement = 'xmlrequest';

    public function toArray($string) {
        return (new \Zend\Config\Reader\Xml())->fromString($string);
    }

    public function fromArray(array $array) {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement($this->startElement);

        foreach ($array as $sectionName => $data) {
            if (!is_array($data)) {
                $writer->writeElement($sectionName, (string) $data);
            } else {
                $this->addBranch($sectionName, $data, $writer);
            }
        }

        $writer->endElement();
        $writer->endDocument();

        return $writer->outputMemory();
    }


    /**
     * Add a branch to an XML object recursively.
     *
     * @param  string    $branchName
     * @param  array     $config
     * @param  XMLWriter $writer
     *
     * @throws \Exception
     */
    protected function addBranch($branchName, array $config, XMLWriter $writer)
    {
        $branchType = null;

        foreach ($config as $key => $value) {
            if ($branchType === null) {
                if (is_numeric($key)) {
                    $branchType = 'numeric';
                } else {
                    $writer->startElement($branchName);
                    $branchType = 'string';
                }
            } elseif ($branchType !== (is_numeric($key) ? 'numeric' : 'string')) {
                throw new \Exception('Mixing of string and numeric keys is not allowed');
            }

            if ($branchType === 'numeric') {
                if (is_array($value)) {
                    $this->addBranch($branchName, $value, $writer);
                } else {
                    $writer->writeElement($branchName, (string) $value);
                }
            } else {
                if (is_array($value)) {
                    $this->addBranch($key, $value, $writer);
                } else {
                    $writer->writeElement($key, (string) $value);
                }
            }
        }

        if ($branchType === 'string') {
            $writer->endElement();
        }
    }
}