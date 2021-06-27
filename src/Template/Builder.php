<?php

namespace App\Template;

class Builder
{
    /**
     * @var string
     */
    private $template;

    /**
     * The map of replacement(s).
     *
     * @var array<string,string>
     */
    private $replacement = null;

    /**
     * Construct.
     *
     * @param string $template
     * @param array<string,string>|null $replacement
     */
    public function __construct(string $template, array $replacement = null)
    {
        $this->template = $template;
        $this->replacement = $replacement;
    }

    /**
     * Create the instance using the given template and the replacement (if possible).
     *
     * @param string $template
     * @param array<string,string>|null $replacement
     */
    public static function template($template, array $replacement = null)
    {
        return new static($template, $replacement);
    }

    /**
     * Guess the replace resource.
     *
     * @param array<string,string>|null $replacement
     * @return array<string,string>
     *
     * @throws \RuntimeException
     */
    protected function getReplacement($replacement = null)
    {
        if ($replacement || $this->replacement) {
            return $replacement ?: $this->replacement;
        }

        throw new \RuntimeException("The replacement map must exist.");
    }

    /**
     * Build the template use the given replacement.
     *
     * @param array<string,string>|null $replacement
     * @return string
     */
    public function build(array $replacement = null)
    {
        $buildingTemplate = $this->template;

        foreach ($this->getReplacement($replacement) as $key => $value) {
            $placeholder = sprintf('{%s}', $key);
            $buildingTemplate = str_replace($placeholder, $value, $buildingTemplate);
        }

        return $buildingTemplate;
    }
}
