<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Check if the provided data contains non-empty values for the specified key.
 *
 * @param mixed       $data          The data to be checked (array or string).
 * @param string|null $arrKey        The key to check within the data.
 * @param bool        $returnData    If true, returns the data value if found.
 * @param mixed       $defaultValue  The default value to return if data is not found.
 *
 * @return bool|string|null Returns true if data exists, data value if $returnData is true and data exists, otherwise null or $defaultValue.
 */
if (!function_exists('hasData')) {
    function hasData($data = NULL, $arrKey = NULL, $returnData = false, $defaultValue = NULL)
    {
        // Check if data is not set, empty, or null
        if (!isset($data) || empty($data) || is_null($data)) {
            return $returnData ? ($defaultValue ?? $data) : false;
        }

        // If arrKey is not provided, consider data itself as having data
        if (is_null($arrKey)) {
            return $returnData ? ($defaultValue ?? $data) : true;
        }

        // Split the keys into an array
        $keys = explode('.', $arrKey);
        $currentKey = array_shift($keys);

        // Check if $data is an array or an object
        if (is_array($data) || is_object($data)) {
            // If it's an array and the key exists, or it's an object and the property exists
            if ((is_array($data) && array_key_exists($currentKey, $data)) || (is_object($data) && isset($data->$currentKey))) {

                // If no more keys left, return the data or true based on returnData flag
                if (empty($keys)) {
                    return $returnData ? ($data instanceof ArrayAccess ? $data[$currentKey] ?? $defaultValue : $data->$currentKey ?? $defaultValue) : true;
                }

                // Recursively call hasData for the nested key
                return hasData(is_array($data) ? ($data[$currentKey] ?? null) : ($data->$currentKey ?? null), implode('.', $keys), $returnData, $defaultValue);
            }
        }

        // No match found, return false or return default value
        return $returnData ? $defaultValue : false;
    }
}

/**
 * Replaces placeholders in a string with corresponding values from the provided array.
 * Placeholders are of the form %placeholder%.
 * If a placeholder is not found in the array, the original placeholder is retained.
 *
 * @param {string} $string - The input string containing placeholders.
 * @param {Array} $arrayOfStringToReplace - An associative array containing key-value pairs for replacement.
 * @returns {string} The input string with placeholders replaced by array values.
 */
if (!function_exists('replaceTextWithData')) {
    function replaceTextWithData($string = NULL, $arrayOfStringToReplace = [])
    {
        $replacedString = str_replace(
            array_map(fn ($key) => "%$key%", array_keys($arrayOfStringToReplace)),
            array_values($arrayOfStringToReplace),
            $string
        );

        return $replacedString;
    }
}

/**
 * Check if a file exists at the given path.
 *
 * @param string|null $path The file path to check.
 * @return bool Returns true if the file exists, otherwise false.
 */
if (!function_exists('fileExist')) {
    function fileExist($path = NULL)
    {
        // Check if the path is not null and call the hasData function to validate the path.
        if (hasData($path)) {
            return file_exists($path) ? true : false;
        }

        return false;
    }
}