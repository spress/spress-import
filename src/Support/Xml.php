<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Support;

/**
 * Utils for processing XML files.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Xml
{
    /**
     * Loads an XML file.
     *
     * @param string $file The filename.
     *
     * @return SimpleXMLElement
     *
     * @throw RuntimeException If there was an error when reading the file.
     */
    public static function loadFile($file)
    {
        $internal_errors = libxml_use_internal_errors(true);
        stream_filter_register('xmlutf8', 'Spress\Import\Support\ValidUtf8XmlFilter');

        $xml = simplexml_load_file('php://filter/read=xmlutf8/resource='.$file);

        if ($xml === false) {
            throw new RuntimeException(sprintf(
                'There was an error when reading this XML file: "%s".',
                libxml_get_errors()));
        }

        return $xml;
    }
}
