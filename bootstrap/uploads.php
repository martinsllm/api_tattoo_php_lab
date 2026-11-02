<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

function uploadedFileToLegacyEntry(UploadedFileInterface $file): array
{
    return [
        'name' => $file->getClientFilename() ?? '',
        'type' => $file->getClientMediaType() ?? '',
        'tmp_name' => (string) ($file->getStream()->getMetadata('uri') ?? ''),
        'error' => $file->getError(),
        'size' => $file->getSize() ?? 0,
    ];
}

function psr7UploadedFileToLegacyEntry(mixed $node): array
{
    if($node instanceof UploadedFileInterface) {
        return uploadedFileToLegacyEntry($node);
    }
    
    if(!is_array($node)) {
        return [];
    }

    $entry = ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []];

    foreach ($node as $i => $file) {
        $legacy = uploadedFileToLegacyEntry($file);
        $entry['name'][$i] = $legacy['name'];
        $entry['type'][$i] = $legacy['type'];
        $entry['tmp_name'][$i] = $legacy['tmp_name'];
        $entry['error'][$i] = $legacy['error'];
        $entry['size'][$i] = $legacy['size'];
    }

    return $entry;
    
}

function requestFilesBag(ServerRequestInterface $request): array
{
    $bag = [];

    foreach ($request->getUploadedFiles() as $field => $node) {
        $bag[$field] = psr7UploadedFileToLegacyEntry($node);
    }

    return $bag;
}
