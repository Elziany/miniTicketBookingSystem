<?php
namespace App\Enum;
enum UserType : string {
    case ADMIN = "admin";
    case GUEST = "guest";
    case STAFF = "staff";
    case AGENT = "agent";
}