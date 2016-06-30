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
 * Filter for fixing the issue about 0x0 character.
 *
 * @see: http://stackoverflow.com/questions/3466035/how-to-skip-invalid-characters-in-xml-file-using-php
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ValidUtf8XmlFilter extends \php_user_filter
{
    protected static $pattern = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';

    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = preg_replace(self::$pattern, '', $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
