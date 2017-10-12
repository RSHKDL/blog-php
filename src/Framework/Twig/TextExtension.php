<?php
namespace Framework\Twig;

/**
 * Extensions about text
 * Class TextExtension
 * @package Framework\Twig
 */
class TextExtension extends \Twig_Extension
{


    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('excerpt', [$this, 'excerpt'])
        ];
    }


    /**
     * Return an excerpt from a content
     *
     * @param string $content
     * @param int $maxLength
     * @return string
     */
    public function excerpt(string $content, int $maxLength = 100): string
    {
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
