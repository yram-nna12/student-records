<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;
class VerificationAssistance extends BaseConfig { public string $apiKey=''; public string $storageDirectory=WRITEPATH.'private/verification-assistance'; public int $maxFileSizeBytes=5242880; public function __construct(){parent::__construct();$this->apiKey=(string)env('verificationAssistance.apiKey','');} }
