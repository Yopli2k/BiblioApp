<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace WebApp\Core\Tools;

class Tools
{
    const HTML_CHARS = ['<', '>', '"', "'"];
    const HTML_REPLACEMENTS = ['&lt;', '&gt;', '&quot;', '&#39;'];

    /**
     * @param string $key
     * @param $default
     * @return mixed|null
     */
    public static function config(string $key, $default = null)
    {
        $constants = [$key, strtoupper($key), 'APP_' . strtoupper($key)];
        foreach ($constants as $constant) {
            if (defined($constant)) {
                return constant($constant);
            }
        }

        return $default;
    }

    public static function fixHtml(?string $text = null): ?string
    {
        if (empty($text)) {
            return $text;
        }
        return str_replace(self::HTML_REPLACEMENTS, self::HTML_CHARS, trim($text));
    }

    public static function noHtml(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }
        return str_replace(self::HTML_CHARS, self::HTML_REPLACEMENTS, trim($text));
    }

    public static function textBreak(string $text, int $length = 50, string $break = '...'): string
    {
        if (strlen($text) <= $length) {
            return trim($text);
        }

        // separamos el texto en palabras
        $words = explode(' ', trim($text));
        $result = '';
        foreach ($words as $word) {
            if (strlen($result . ' ' . $word . $break) <= $length) {
                $result .= $result === '' ? $word : ' ' . $word;
                continue;
            }

            $result .= $break;
            break;
        }

        return $result;
    }
}
