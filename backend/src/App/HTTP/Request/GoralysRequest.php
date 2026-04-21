<?php

/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Goralys\App\HTTP\Request;

use Goralys\App\HTTP\Request\Interfaces\RequestInterface;

/**
 * A simple class used to easily access an HTTP request's inputs
 */
class GoralysRequest implements RequestInterface
{
    private array $input;

    public function __construct()
    {
        $raw  = file_get_contents('php://input');
        $json = json_decode($raw, true);

        if (!empty($_POST)) {
            $this->input = $_POST;
        } elseif (is_array($json)) {
            $this->input = $json;
        } else {
            $this->input = [];
        }
    }


    /**
     * Reads the inputs of the request and returns the desired value.
     * By default, it falls back to `null` if the input doesn't exit.
     * @param string $key The name of the input to read.
     * @return int | float | string | bool | null The value of the input.
     */
    public function get(string $key): int | float | string | bool | null
    {
        $v = $this->input[$key] ?? null;
        if ((is_scalar($v) || is_bool($v))) {
            return $v;
        }
        if (is_string($v)) {
            return trim($v);
        }
        return $v;
    }

    /**
     * Check if a request's input is valid.
     * An input is considered valid if it is not empty and exists.
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
                if (empty(trim($value))) {
                    return false;
                }
            } elseif ($value === null) {
                return false;
            }
        }

        return true;
    }
}
