<?php

class Options
{
    /** @var array */
    protected $options;

    /** @var array|null */
    protected $valid;

    /** @var array */
    protected $parsed;

    /**
     * @param array $options
     * @param array|null $valid
     * @throws Exception
     */
    public function __construct(array $options, array $valid = null)
    {
        $this->options = $options;
        $this->valid = array_flip($valid);

        $this->parse();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function parse()
    {
        $skip = [];

        foreach ($this->options as $i => $option) {
            if ($skip[$i] ?? false) {
                continue;
            }

            if (substr($option, 0, 1) == '-') {
                if (strpos($option, '=') !== false) {
                    // --key=value
                    list($key, $value) = explode('=', $option, 2);
                } else if (array_key_exists($i + 1, $this->options) && substr($this->options[$i + 1], 0, 1) != '-') {
                    // --key value
                    $skip[$i + 1] = true;

                    $key = $option;
                    $value = $this->options[$i + 1];
                } else {
                    // --key
                    $key = $option;
                    $value = true;
                }

                if (substr($value, 0, 1) == '"') {
                    // Value is a quoted string. Keep reading until we find a closing quote or run out of options.
                    $value = substr($value, 1);

                    for ($j = $i; $j < count($this->options); $j++) {
                        $skip[$j] = true;

                        $value .= ' ' . $this->options[$j];

                        if (substr($value, -1, 1) == '"') {
                            $value = substr($value, 0, -1);
                            break;
                        }
                    }
                }

                if ($this->valid !== null && !array_key_exists($key, $this->valid)) {
                    throw new Exception('Invalid option: ' . $key);
                }

                $this->parsed[$key] = $value;
            }
        }
    }

    /**
     * @param string $key
     * @return string|true|null
     */
    public function getOption(string $key)
    {
        return $this->parsed[$key] ?? null;
    }

    /**
     * @param string[] $keys
     * @return array
     */
    public function getOptions(array $keys): array
    {
        $values = array_map(function (string $key) {
            return $this->getOption($key);
        }, $keys);

        return array_values(array_filter($values, function ($value) {
            return $value !== null;
        }));
    }

    /**
     * @param array $keys
     * @return string|true|null
     */
    public function getFirstOption(array $keys)
    {
        $values = $this->getOptions($keys);

        return $values ? $values[0] : null;
    }
}
