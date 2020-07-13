<?php


namespace App\Message;


class Message
{
    public const USER_INVALID_EMAIL = 'Email {{ value }} is not valid';
    public const USER_EMAIL_MAX_LENGTH = 'User email cannot be longer than {{ limit }} digits';
    public const USER_PHONE_MAX_LENGTH = 'User phone number cannot be longer than {{ limit }} digits';
    public const USER_ADDRESS_MAX_LENGTH = 'User address cannot be longer than {{ limit }} characters';

    public const CATEGORY_NAME_MAX_LENGTH = 'Category name cannot be longer than {{ limit }} characters';
    public const CATEGORY_DESCRIPTION_MAX_LENGTH = 'Category description cannot be longer than {{ limit }} characters';

    public const BUDGET_REQUEST_TITLE_MAX_LENGTH = 'Budget request title cannot be longer than {{ limit }} characters';
    public const BUDGET_REQUEST_DESCRIPTION_MAX_LENGTH =
        'Budget request description cannot be longer than {{ limit }} characters';
    public const BUDGET_REQUEST_INVALID_STATUS = 'Status {{ value }} is not valid';
    public const BUDGET_REQUEST_CREATED_OK = 'Budget request created OK';
    public const BUDGET_REQUEST_INVALID_JSON_FOR_CREATE = 'Invalid Json body for budget request creation';
    public const BUDGET_REQUEST_DISCARD_OK = 'Budget request discarded OK';
    public const BUDGET_REQUEST_ID_NOT_EXISTS = 'Budget request {{ id }} not exists';
    public const BUDGET_REQUEST_DISCARD_NOT_ALLOWED = 'Discard is not allowed';
    public const BUDGET_REQUEST_MODIFIED_OK = 'Budget request modified OK';
    public const BUDGET_REQUEST_INVALID_JSON_FOR_MODIFY = 'Invalid Json body for budget request modification';
    public const BUDGET_REQUEST_MODIFY_NOT_ALLOWED = 'Modify is not allowed';
    public const BUDGET_REQUEST_PUBLISHED_OK = 'Budget request published OK';
    public const BUDGET_REQUEST_PUBLISH_NOT_ALLOWED = 'Publish is not allowed';


    public static function messageReplace (string $search, string $replace, string $message)
    {
        return str_replace('{{ '. $search . ' }}', $replace, $message);
    }
}