<?php

namespace App\Services\Email;

use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsible for retrieving email HTML templates
 * from storage with caching support.
 */
class TemplateStorageService
{
    protected string $basePath = 'templates/email';
    protected string $disk = 'local';

    private const TTL = 3600;

    /**
     * Retrieve an email template content.
     *
     * The template content is cached for performance.
     * Falls back to a default template path if the main
     * template does not exist.
     *
     * @param  string  $templateName  Template file name (without extension).
     *
     * @return string The HTML content of the template.
     *
     * @throws NotFoundException If neither the primary nor fallback template exists.
     */
    public function get(string $templateName): string
    {
        $cacheKey = "template:email:{$templateName}";

        return Cache::remember($cacheKey, self::TTL, function () use ($templateName) {
            $path = $this->basePath . "/{$templateName}.html";

            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->get($path);
            }

            Log::info("template not found: {$templateName}");

            throw new NotFoundException(
                'C009',
                'email template not found',
                'template de email não encontrado',
                [
                    'path' => $path,
                ]
            );
        });
    }
}
