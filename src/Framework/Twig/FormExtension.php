<?php
namespace Framework\Twig;

class FormExtension extends \Twig_Extension
{


    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }


    /**
     * Generate the HTML code of a field
     *
     * @param array $context Twig view context
     * @param string $key Field key
     * @param mixed $value Field value
     * @param null|string $label Label to use
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $value = $this->convertValue($value);
        $class= 'form-group';
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name'  => $key,
            'id'    => $key
        ];
        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class=\"" . $class . "\">
            <label for=\"title\">{$label}</label>
            {$input}
            {$error}
        </div>";
    }


    /**
     * @param $value
     * @return string
     */
    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }


    /**
     * Generate HTML according to context's errors
     *
     * @param array $context
     * @param string $key
     * @return string
     */
    private function getErrorHtml(array $context, string $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<div class='form-control-feedback'>{$error}</div>";
        }
        return "";
    }


    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input " . $this->getHtmlFromArray($attributes) . " type=\"text\" value=\"{$value}\">";
    }


    /**
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }


    /**
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }
}
