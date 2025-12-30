<?php

namespace Goralys\Tests\Fakes;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;

class FakeGoralysRequest implements RequestInterface
{
    private array $input = [];

    /**
     * Manually set the input values for the fake request.
     * @param array $input
     */
    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    /**
     * Reads the inputs of the request and returns the desired value.
     * @param string $key The name of the input to read.
     * @return mixed The value of the input.
     */
    public function get(string $key): mixed
    {
        return $this->input[$key] ?? null;
    }

    /**
     * Check if a request's input is valid.
     * @param string $key1 The name of the input to validate.
     * @param string ...$_ [OPTIONAL] The other inputs to validate.
     * @return bool
     */
    public function validate(string $key1, string ...$_): bool
    {
        foreach ([$key1, ...$_] as $k) {
            if (!array_key_exists($k, $this->input)) {
                return false;
            }

            $value = $this->input[$k];

            if (is_string($value)) {
                if (trim($value) === "") {
                    return false;
                }
            } elseif ($value === null) {
                return false;
            }
        }

        return true;
    }
}
