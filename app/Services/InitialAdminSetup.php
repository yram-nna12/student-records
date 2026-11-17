<?php

namespace App\Services;

use App\Models\AdminModel;
use Config\Database;
use DomainException;
use RuntimeException;

final class InitialAdminSetup
{
    private const LOCK = 'student-records:initial-admin-setup';

    public function generateToken(): string
    {
        $this->assertDevelopmentAndEmpty();
        $path = $this->tokenPath();
        if (is_file($path)) {
            throw new RuntimeException('A setup token already exists. Complete setup or remove it locally before generating another.');
        }
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Cannot create the private setup-token directory.');
        }
        $token = bin2hex(random_bytes(32));
        if (file_put_contents($path, $token . PHP_EOL, LOCK_EX) === false) {
            throw new RuntimeException('Cannot store the private setup token.');
        }
        @chmod($path, 0600);
        return $token;
    }

    public function create(string $token, string $name, string $email, string $password, string $confirmation): void
    {
        $this->assertDevelopmentAndEmpty();
        $db = Database::connect();
        $locked = (int) ($db->query('SELECT GET_LOCK(?, 10) AS locked', [self::LOCK])->getRowArray()['locked'] ?? 0) === 1;
        if (!$locked) {
            throw new RuntimeException('Initial setup is busy. Please retry.');
        }
        try {
            $this->assertDevelopmentAndEmpty();
            $stored = @file_get_contents($this->tokenPath());
            if (!is_string($stored) || !hash_equals(trim($stored), $token)) {
                throw new DomainException('The one-time setup token is invalid.');
            }
            $email = strtolower(trim($email));
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 150 || filter_var($email, FILTER_VALIDATE_EMAIL) === false
                || strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0") || $password !== $confirmation) {
                throw new DomainException('Enter a valid name, email, and matching 12–72 character password.');
            }
            $admins = new AdminModel();
            if ($admins->where('email', $email)->first()) {
                throw new DomainException('An administrator with that email already exists.');
            }
            if (!$admins->insert(['full_name' => $name, 'email' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'status' => 'active'])) {
                throw new RuntimeException('Administrator account could not be created.');
            }
            if (!@unlink($this->tokenPath())) {
                throw new RuntimeException('Administrator was created but the setup token could not be invalidated. Remove it locally before continuing.');
            }
        } finally {
            $db->query('SELECT RELEASE_LOCK(?)', [self::LOCK]);
        }
    }

    public function available(): bool
    {
        return ENVIRONMENT === 'development' && (new AdminModel())->countAllResults() === 0 && is_file($this->tokenPath());
    }

    private function assertDevelopmentAndEmpty(): void
    {
        if (ENVIRONMENT !== 'development' || (new AdminModel())->countAllResults() !== 0) {
            throw new RuntimeException('Initial administrator setup is unavailable.');
        }
    }

    private function tokenPath(): string
    {
        return WRITEPATH . 'private/admin-setup.token';
    }
}
