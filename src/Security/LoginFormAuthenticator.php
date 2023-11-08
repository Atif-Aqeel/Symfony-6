<?php

namespace App\Security;

// use App\Security\LoginFormAuthenticator;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\Endpoints\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenGenerator;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManagerInterface, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenGenerator = $csrfTokenManagerInterface;
        $this->passwordEncoder = $passwordEncoder;
    }


    // Supports method
    /**
     * Show this authenticator be used for this request 
     * @param Request $request
     * @return bool|void
     */
    public function supports(Request $request): bool
    {
        // dd("You are here");
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }


    // Get Credentials
    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request) #First
    {

        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        // dd($credentials);

        //set Session for last username
        $request->getSession()->set(
            // Security::LAST_USERNAME,
            SecurityRequestAttributes::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }


    // Authenticate User
    /**
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider) #Second
    {
        // dd($credentials);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']])
            ?? throw new CustomUserMessageAuthenticationException(sprintf('User "%s" Not Found', $credentials['email']));

        // dd($user);

        return $user;
    }


    // Check Credentials method
    /**
     * @param array $credentials
     * @param PasswordAuthenticatedUserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, PasswordAuthenticatedUserInterface $user) #Third
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenGenerator->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
            // throw new InvalidCsrfTokenException('Invalid CSRF Token');
        }
        // dd($token);

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }



    // Create Passport method
    /**
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }


    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|void|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response #Fourth
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // if admin
        // if ($token->getUser()->isAdmin()) {
        // $hasAccess = $this->isGranted('ROLE_ADMIN');
        // if ($hasAccess) {
        if (in_array('ROLE_ADMIN', $token->getRoleNames())) {

            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}


// BAD - $user->getRoles() will not know about the role hierarchy
// $hasAccess = in_array('ROLE_ADMIN', $user->getRoles());

// GOOD - use of the normal security methods
// $hasAccess = $this->isGranted('ROLE_ADMIN');
// $this->denyAccessUnlessGranted('ROLE_ADMIN');