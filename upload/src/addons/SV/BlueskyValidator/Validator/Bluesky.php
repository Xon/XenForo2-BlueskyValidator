<?php

namespace SV\BlueskyValidator\Validator;

use XF\Validator\AbstractValidator;
use function is_string;
use function rtrim;
use function stripos;

class Bluesky extends AbstractValidator
{
    public function isValid($value, &$errorKey = null): bool
    {
        $value = (string)$value;
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_DOMAIN))
        {
            $errorKey = 'please_enter_valid_bluesky_name';
            return false;
        }

        return true;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function coerceValue($value)
    {
        if (is_string($value) && $value && $value[0] == '@')
        {
            $value = substr($value, 1);
            // handle @example => example.bsky.social
            $value = rtrim($value, '.');
            if (stripos($value, '.') === false)
            {
                $value .= '.bsky.social';
            }
        }
        else if (preg_match('#bsky\.app/profile/(?P<id>[\w\.-_]+)$#ui', $value, $match))
        {
            $value = $match['id'];
        }

        return $value;
    }
}