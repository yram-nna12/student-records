<?php

namespace App\Controllers;

use App\Services\InitialAdminSetup;
use CodeIgniter\Exceptions\PageNotFoundException;
use DomainException;

final class InitialAdminSetupController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function form(): string
    {
        $this->assertAvailable();
        return view('admin/setup', ['title' => 'Initial administrator setup']);
    }

    public function create()
    {
        $this->assertAvailable();
        try {
            (new InitialAdminSetup())->create(
                $this->text('setup_token'), $this->text('full_name'), $this->text('email'),
                $this->text('password'), $this->text('password_confirmation')
            );
            return redirect()->to(site_url('admin/login'))->with('success', 'Administrator created. Sign in now.');
        } catch (DomainException $exception) {
            return redirect()->to(site_url('admin/setup'))->with('error', $exception->getMessage())
                ->with('full_name', $this->text('full_name'))->with('email', $this->text('email'));
        }
    }

    private function assertAvailable(): void
    {
        if (!in_array($this->request->getIPAddress(), ['127.0.0.1', '::1'], true) || !(new InitialAdminSetup())->available()) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function text(string $name): string
    {
        $value = $this->request->getPost($name);
        return is_string($value) ? $value : '';
    }
}
