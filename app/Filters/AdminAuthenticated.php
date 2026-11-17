<?php
namespace App\Filters;
use App\Models\AdminModel; use CodeIgniter\Filters\FilterInterface; use CodeIgniter\HTTP\RequestInterface; use CodeIgniter\HTTP\ResponseInterface;
class AdminAuthenticated implements FilterInterface { public function before(RequestInterface $request,$arguments=null){$id=session()->get('admin_id');$admin=$id?(new AdminModel())->find($id):null;if(!$admin||$admin['status']!=='active'){session()->remove('admin_id');return redirect()->to(site_url('admin/login'));}} public function after(RequestInterface $request,ResponseInterface $response,$arguments=null){} }
