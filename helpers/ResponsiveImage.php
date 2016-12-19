<?php

namespace Helper;

/**
 * Create Responsive Image Markup Based on The Attachmend Id.
 * It scales the image to the next breakpoint if one exists, so that a bigger
 * than screen-width image is displayed.
 */
class ResponsiveImage
{
    private static function createImageSource($mediaQuery, $imageURL)
    {
        return "<source media='(min-width: ".$mediaQuery."px)' srcset='$imageURL'>";
    }

    private static function getMarkup($image, $altText, $sources = [], $srcset = [], $sizes = [], $classes = [])
    {
        return '<picture>'
        .implode($sources, '')
        ."<img src='$image'"
        .' srcset="'.implode($srcset, ',').'"'
        .' sizes="'.implode($sizes, ', ').'"'
        ." alt='$altText'"
        .' class="'.implode($classes, ' ').'"'
        .' />'
        .'</picture>';
    }

    /**
     * Constructor method to create responsive image markup.
     *
     * @param int $attachmentId
     * @param []  $breakpoints  Breakpoints, starting with the largest first, but not necessary
     * @param []  $sizes        Image Sizes Attribute
     * @param []  $classes      Classes to add to image
     */
    public static function render($attachmentId, $breakpoints = [], $sizes = [], $classes = [])
    {
        if (!$attachmentId) {
            return '';
        }

        $srcset = [];
        $imageUrl = '';
        $sources = [];
        $sizes = [];

        for ($i = 0; $i < count($breakpoints); ++$i) {
            $imageBreakpoint = wp_get_attachment_image_src($attachmentId, $breakpoints[$i]);
            $prevImageBreakpoint = ($i === 0) ? $src : wp_get_attachment_image_src($attachmentId, $breakpoints[$i - 1]);

            if (self::WPImageInformationIsSet($imageBreakpoint)) {
                $srcset[] = self::setSRCSET($prevImageBreakpoint[0], $imageBreakpoint[1]);
                $sizes[] = self::setSize($imageBreakpoint[1], $prevImageBreakpoint[1]);
                $sources[] = self::createImageSource($imageBreakpoint[1], $prevImageBreakpoint[0]);
            }

            if (empty($imageUrl) && $imageBreakpoint && isset($imageBreakpoint[0])) {
                $imageUrl = $imageBreakpoint[0];
            }
        }

        return self::getMarkup(
            $imageUrl,
            get_post_meta($attachmentId, '_wp_attachment_image_alt', true),
            $sources,
            $srcset,
            $sizes,
            $classes
        );
    }

    private static function setSRCSET($url, $width)
    {
        return $url . ' ' . $width . 'w';
    }

    private static function setSize($mediaMinWidth, $imageSize)
    {
        return '(min-width:'.$mediaMinWidth.'px) '.$imageSize.'px';
    }

    private static function WPImageInformationIsSet($imageInfo)
    {
        return $imageInfo && isset($imageInfo[0]) && isset($imageInfo[1]) && $imageInfo[3];
    }
}
