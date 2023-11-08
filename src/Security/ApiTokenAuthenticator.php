<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */

    public function supports(Request $request): ?bool
    {
        // TODO: Implement supports() method.
        // dd("You are here");
        // return false;
        return $request->headers->has('x-api-token');
        // return str_starts_with($request->getPathInfo(), '/api/');
    }

    // The authenticate() method is the most important method of the authenticator. 
    // Its job is to extract credentials (e.g. username & password, or API tokens) from the Request object 
    // and transform these into a security Passport 
    public function authenticate(Request $request): Passport
    {
        // TODO: Implement authenticate() method.
        // dd("Authenticate");
        // A token extractor retrieves the token from the request (e.g. a header or request body).
        $apiToken = $request->headers->get('x-api-token');

        if (null == $apiToken) {
            throw new CustomUserMessageAuthenticationException('No Api Token Provided');
        }

        // $userIdentifier = 

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function ($apiToken) {
                dd($apiToken);
                $user = $this->userRepository->findByApiToken($apiToken);
                if (!$user) {
                    throw new UserNotFoundException();
                    // throw new CustomUserMessageAuthenticationException('Invalid Api Token');
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // TODO: Implement onAuthenticationSuccess() method.
        // dd("On Authentication Success");
        // If the user is authenticated, this method is called with the authenticated $token. 
        // This method can return a response (e.g. redirect the user to the homepage).


        // If null is returned, the request continues like normal (i.e. the controller matching the 
        // login route is called). This is useful for API routes where each route is protected by an API key header.
        return null;
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // TODO: Implement onAuthenticationFailure() method.
        // dd("On Authentication Failure");
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
