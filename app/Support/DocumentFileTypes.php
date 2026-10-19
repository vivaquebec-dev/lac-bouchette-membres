<?php

namespace App\Support;

final class DocumentFileTypes
{
    /** extension => type de contenu servi au téléchargement */
    public const CONTENT_TYPES = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /** @return list<string> */
    public static function extensions(): array
    {
        return array_keys(self::CONTENT_TYPES);
    }
}