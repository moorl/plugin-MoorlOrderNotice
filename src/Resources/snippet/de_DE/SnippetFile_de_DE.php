<?php declare(strict_types=1);

namespace MoorlOrderNotice\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
    }

    public function getAuthor(): string
    {
        return 'Moorleiche Web Solutions';
    }

    public function isBase(): bool
    {
        return true;
    }
}
