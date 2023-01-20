<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Translatable
{
    /**
     * Attempts to translates a message string returning the corresponding key and translated string.
     *
     * If the message cannot be translated, the returning key is null and message remains the same.
     *
     * @param string $message
     * @param array $attributes
     * @param string $path_prefix
     *
     * @return array
     */
    public function translateMessageToArray(string $message, array $attributes = [], ?string $path_prefix = 'errors'): array
    {
        $path = ! empty($path_prefix) ? "{$path_prefix}.{$message}" : $message;

        $key = null;
        $translated_message = __($path, $attributes);

        if (! Str::startsWith($translated_message, $path)) {
            $message = $translated_message;
            $key = Str::slug(last(explode('.', $path)), '_');
        }

        return ['key' => $key, 'message' => $message];
    }
}
