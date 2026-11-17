<?php

namespace App\Commands;

use App\Services\InitialAdminSetup;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

final class GenerateAdminSetupToken extends BaseCommand
{
    protected $group = 'Student Records';
    protected $name = 'admin:setup-token';
    protected $description = 'Create the one-time local browser setup token for the first administrator.';

    public function run(array $params)
    {
        if (PHP_SAPI !== 'cli' || ENVIRONMENT !== 'development' || $params !== [] || CLI::getOptions() !== []) {
            CLI::error('This development-only command accepts no arguments or options.');
            return EXIT_ERROR;
        }
        try {
            CLI::write((new InitialAdminSetup())->generateToken(), 'yellow');
            return EXIT_SUCCESS;
        } catch (\Throwable $exception) {
            CLI::error($exception->getMessage());
            return EXIT_ERROR;
        }
    }
}
