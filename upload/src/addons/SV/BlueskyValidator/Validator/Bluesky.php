<?php

namespace SV\BlueskyValidator\Validator;

use XF\Validator\AbstractValidator;

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
        }
        else if (preg_match('#bsky\.app/profile/(?P<id>[\w\.-_]+)$#ui', $value, $match))
        {
            $value = $match['id'];
        }

        return $value;
    }
}