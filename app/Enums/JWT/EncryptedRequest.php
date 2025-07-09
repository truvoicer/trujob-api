<?php
namespace App\Enums\JWT;

enum EncryptedRequest : string
{
    case ENCRYPTED_REQUEST = 'encrypted_request';
    case ENCRYPTED_REQUEST_DATA = 'encrypted_request_data';
    case ENCRYPTED_REQUEST_PAYLOAD = 'payload';
}
