<?php

namespace App\Services\Email\Builds;

use App\Contracts\MountEmailInterface;
use App\Services\Email\TemplateStorageService;
use Illuminate\Support\Facades\Blade;

/**
 * Service responsible for mounting and formatting password recovery email HTML.
 *
 * This class retrieves the appropriate email template by type, validates the
 * provided parameters, and replaces predefined variables within the template
 * HTML. It implements the MountEmailInterface contract.
 */
class BuildPasswordRecoveryEmailClientService implements MountEmailInterface
{
    public const TEMPLATE_NAME = 'password-recovery';

    /**
     * MountPasswordRecoveryEmailClientService constructor
     *
     * @param  TemplateStorageService  $templateStorageService
     */
    public function __construct(
        protected TemplateStorageService $templateStorageService
    ) {}

    /**
     * Build the final HTML for a password recovery email.
     *
     * @param  string  $type  The email template type to load.
     * @param  array|null  $parameters  Variables used to populate the template.
     *
     * @return string The final formatted HTML email.
     */
    public function mountHTML(string $type, ?array $parameters = null): string
    {
        $emailTemplate = $this->templateStorageService->get(self::TEMPLATE_NAME);
        $parameters = $this->mountParameters($parameters);

        return $this->overwriteVariablesInHTML($emailTemplate, $type, $parameters);
    }

    /**
     * Replace template variables inside the HTML with actual values.
     *
     * @param  string  $emailTemplate  Raw HTML template.
     * @param  string  $type  The template type determining which variables should be replaced.
     * @param  array<string, mixed>|null  $parameters  Values used for substitution.
     *
     * @return string The processed HTML with variables replaced.
     */
    public function overwriteVariablesInHTML(string $emailTemplate, string $type, ?array $parameters = null): string
    {
        return Blade::render($emailTemplate, $parameters);
    }

    /**
     * Prepare parameters required by the email template.
     *
     * @param  array<string, mixed>  $parameters  Raw parameters.
     *
     * @return array<string, mixed> Normalized parameters used by the template.
     */
    protected function mountParameters(array $parameters): array
    {
        return [
            'name' => $parameters['name'],
            'recoveryLink' => $parameters['url'],
        ];
    }
}
