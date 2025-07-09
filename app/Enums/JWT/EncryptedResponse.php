<?php
namespace App\Enums\JWT;

enum EncryptedResponse : string
{
    case ENCRYPTED_RESPONSE = 'encrypted_response';
    case ENCRYPTED_RESPONSE_DATA = 'encrypted_response_data';
    case ENCRYPTED_RESPONSE_PAYLOAD = 'payload';
}
