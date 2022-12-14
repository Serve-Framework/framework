<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\utility;

use function array_flip;

use function explode;

/**
 * Mime type converter.
 *
 * @author Joe J. Howard
 */
class Mime
{
    /**
     * Associative array of file to MIME-types.
     *
     * @var array
     */
    public static $mimeMap = [

        // Image formats
        'jpg|jpeg|jpe'                 => 'image/jpeg',
        'gif'                          => 'image/gif',
        'png'                          => 'image/png',
        'bmp'                          => 'image/bmp',
        'tiff'                         => 'image/tiff',
        'ico'                          => 'image/x-icon',
        'svg'                          => 'image/svg+xml',

        // Video formats
        'asf|asx'                      => 'video/x-ms-asf',
        'wmv'                          => 'video/x-ms-wmv',
        'wmx'                          => 'video/x-ms-wmx',
        'wm'                           => 'video/x-ms-wm',
        'avi'                          => 'video/avi',
        'divx'                         => 'video/divx',
        'flv'                          => 'video/x-flv',
        'mov|qt'                       => 'video/quicktime',
        'mpeg|mpg|mpe'                 => 'video/mpeg',
        'mp4|m4v'                      => 'video/mp4',
        'ogv'                          => 'video/ogg',
        'webm'                         => 'video/webm',
        'mkv'                          => 'video/x-matroska',

        // Text formats
        'txt|asc|c|cc|h'               => 'text/plain',
        'csv'                          => 'text/csv',
        'tsv'                          => 'text/tab-separated-values',
        'ics'                          => 'text/calendar',
        'rtx'                          => 'text/richtext',
        'css'                          => 'text/css',
        'html|htm'                     => 'text/html',
        'xml'                          => 'text/xml',

        // Audio formats
        'mp3|m4a|m4b'                  => 'audio/mpeg',
        'ra|ram'                       => 'audio/x-realaudio',
        'wav'                          => 'audio/wav',
        'ogg|oga'                      => 'audio/ogg',
        'mid|midi'                     => 'audio/midi',
        'wma'                          => 'audio/x-ms-wma',
        'wax'                          => 'audio/x-ms-wax',
        'mka'                          => 'audio/x-matroska',

        // Misc application formats
        'rtf'                          => 'application/rtf',
        'js'                           => 'application/javascript',
        'pdf'                          => 'application/pdf',
        'swf'                          => 'application/x-shockwave-flash',
        'class'                        => 'application/java',
        'tar'                          => 'application/x-tar',
        'zip'                          => 'application/zip',
        'gz|gzip'                      => 'application/x-gzip',
        'rar'                          => 'application/rar',
        '7z'                           => 'application/x-7z-compressed',
        'exe'                          => 'application/x-msdownload',

        // MS Office formats
        'doc'                          => 'application/msword',
        'pot|pps|ppt'                  => 'application/vnd.ms-powerpoint',
        'wri'                          => 'application/vnd.ms-write',
        'xla|xls|xlt|xlw'              => 'application/vnd.ms-excel',
        'mdb'                          => 'application/vnd.ms-access',
        'mpp'                          => 'application/vnd.ms-project',
        'docx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'docm'                         => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm'                         => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm'                         => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsb'                         => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xltx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm'                         => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam'                         => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm'                         => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm'                         => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm'                         => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam'                         => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm'                         => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
        'json'                         => 'application/json',

        // OpenOffice formats
        'odt'                          => 'application/vnd.oasis.opendocument.text',
        'odp'                          => 'application/vnd.oasis.opendocument.presentation',
        'ods'                          => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg'                          => 'application/vnd.oasis.opendocument.graphics',
        'odc'                          => 'application/vnd.oasis.opendocument.chart',
        'odb'                          => 'application/vnd.oasis.opendocument.database',
        'odf'                          => 'application/vnd.oasis.opendocument.formula',

        // WordPerfect formats
        'wp|wpd'                       => 'application/wordperfect',

        // iWork formats
        'key'                          => 'application/vnd.apple.keynote',
        'numbers'                      => 'application/vnd.apple.numbers',
        'pages'                        => 'application/vnd.apple.pages',

        // RSS formats
        'atom'                         => 'application/atom+xml',
        'rss'                          => 'application/rss+xml',
        'rdf'                          => 'application/rdf+xml',
    ];

    /**
     * Convert a file extension to a valid MIME-type.
     *
     * @param  string       $ext File extension
     * @return false|string
     */
    public static function fromExt(string $ext)
    {
        foreach (self::$mimeMap as $type => $mime)
        {
            $types = explode('|', $type);

            foreach ($types as $_type)
            {
                if ($ext === $_type)
                {
                    return $mime;
                }
            }
        }
        return false;
    }

    /**
     * Convert a valid MIME-type to a file extension.
     *
     * @param  string       $mime File extension
     * @return false|string
     */
    public static function toExt(string $mime)
    {
        $mimeMap = array_flip(self::$mimeMap);

        if (isset($mimeMap[$mime]))
        {
            return explode('|', $mimeMap[$mime])[0];
        }

        return false;
    }
}
