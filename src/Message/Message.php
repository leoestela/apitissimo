<?php


namespace App\Message;


class Message
{
    //Will be used in constraint asserts messages or in response messages
    public const USER_INVALID_EMAIL = 'Email {{ value }} is not valid';
    public const USER_EMAIL_MAX_LENGTH = 'User email cannot be longer than {{ limit }} digits';
    public const USER_PHONE_MAX_LENGTH = 'User phone number cannot be longer than {{ limit }} digits';
    public const USER_ADDRESS_MAX_LENGTH = 'User address cannot be longer than {{ limit }} characters';

    public const CATEGORY_NAME_MAX_LENGTH = 'Category name cannot be longer than {{ limit }} characters';
    public const CATEGORY_DESCRIPTION_MAX_LENGTH = 'Category description cannot be longer than {{ limit }} characters';

    public const BUDGET_REQUEST_CREATED_OK = 'Budget request created OK';
    public const BUDGET_REQUEST_DISCARDED_OK = 'Budget request discarded OK';
    public const BUDGET_REQUEST_MODIFIED_OK = 'Budget request modified OK';
    public const BUDGET_REQUEST_PUBLISHED_OK = 'Budget request published OK';

    public const BUDGET_REQUEST_TITLE_MAX_LENGTH = 'Budget request title cannot be longer than {{ limit }} characters';
    public const BUDGET_REQUEST_DESCRIPTION_MAX_LENGTH =
        'Budget request description cannot be longer than {{ limit }} characters';
    public const BUDGET_REQUEST_INVALID_STATUS = 'Status {{ value }} is not valid';


    public static function messageReplace (string $search, string $replace, string $message)
    {
        return str_replace('{{ '. $search . ' }}', $replace, $message);
    }
}