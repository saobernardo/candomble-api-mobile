<?php

namespace App\Contracts;

/**
 * Interface MountEmailInterface
 *
 * Defines the contract for building and processing email HTML content.
 * Implementations are responsible for mounting raw HTML templates and
 * replacing variables or placeholders within the template.
 */
interface MountEmailInterface
{
    /**
     * Mount an email HTML template based on the given type and parameters.
     *
     * @param  string  $type  The email template type or identifier.
     * @param  array|null  $parameters  Optional variables to inject into the template.
     *
     * @return string The fully mounted email HTML.
     */
    public function mountHTML(string $type, ?array $parameters = null): string;

    /**
     * Replace variables inside an HTML email template.
     *
     * @param  string  $html  The HTML content containing variables or placeholders.
     * @param  string  $type  The email template type, allowing type-specific replacements.
     * @param  array|null  $parameters  Optional values used to overwrite variables.
     *
     * @return string The processed HTML with variables replaced.
     */
    public function overwriteVariablesInHTML(string $html, string $type, ?array $parameters = null): string;
}
