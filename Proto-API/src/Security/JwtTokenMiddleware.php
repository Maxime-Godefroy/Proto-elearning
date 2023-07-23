<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtTokenMiddleware
{
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Vérifiez que la route nécessite une authentification (si vous avez des routes publiques, vous pouvez les exclure ici)
        if (!$this->requiresAuthentication($request)) {
            return;
        }

        // Récupérez le token JWT depuis l'en-tête "Authorization"
        $token = $request->headers->get('Authorization');

        if (!$token) {
            throw new AccessDeniedException('Token JWT manquant.');
        }

        // Vérifiez si le token est valide
        try {
            $decodedToken = $this->jwtEncoder->decode($token);
        } catch (\Exception $e) {
            throw new AccessDeniedException('Token JWT invalide.', $e);
        }

        // Stockez les informations du token dans une variable globale (facultatif)
        $request->attributes->set('decoded_token', $decodedToken);
    }

    private function requiresAuthentication(Request $request)
    {
        // Vérifiez ici si la route nécessite une authentification
        // Vous pouvez utiliser des annotations ou d'autres méthodes pour déterminer cela
        // Retournez true si l'authentification est nécessaire, sinon false.
    }
}