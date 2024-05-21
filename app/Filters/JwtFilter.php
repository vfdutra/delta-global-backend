<?php 

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Services\JwtService;
use Config\Services;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtService = new JwtService();
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader) {
            return Services::response()->setStatusCode(401)->setJSON(['message' => 'Authorization header missing']);
        }

        $token = explode(' ', $authHeader)[1];
        $decodedToken = $jwtService->verifyToken($token);

        if (!$decodedToken) {
            return Services::response()->setStatusCode(401)->setJSON(['message' => 'Invalid token']);
        }

        $request->user = $decodedToken;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
      //
    }
}
