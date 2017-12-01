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
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
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
     * Generate an <input>
     *
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input " . $this->getHtmlFromArray($attributes) . " type=\"text\" value=\"{$value}\">";
    }


    /**
     * Generate an <input type="file">
     *
     * @param array $attributes
     * @return string
     */
    private function file(array $attributes)
    {
        return "<input " . $this->getHtmlFromArray($attributes) . " type=\"file\">";
    }


    /**
     * Generate an <input type="checkbox">
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function checkbox(?string $value, array $attributes): string
    {
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0"/>';
        if ($value) {
            $attributes['checked'] = true;
        }
        return $html . "<input type=\"checkbox\" " . $this->getHtmlFromArray($attributes) . " value=\"1\">";
    }


    /**
     * Generate a <textarea>
     *
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }


    /**
     * Generate a <select>
     *
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes)
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">{$htmlOptions}</select>";
    }


    /**
     * Transform a $key => $value array into html attributes
     *
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
    }
}
