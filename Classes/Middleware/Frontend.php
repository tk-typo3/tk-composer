<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TimonKreis\TkComposer\Domain\Model\Package;
use TimonKreis\TkComposer\Domain\Repository\AccountRepository;
use TimonKreis\TkComposer\Domain\Repository\PackageRepository;
use TimonKreis\TkComposer\Exception;
use TimonKreis\TkComposer\Service\AccountService;
use TimonKreis\TkComposer\Service\FilesystemService;
use TimonKreis\TkComposer\Service\PackageService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Message;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @package TimonKreis\TkComposer\Middleware
 */
class Frontend implements MiddlewareInterface
{
    /**
     * @var ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var FilesystemService
     */
    protected $filesystemService;

    /**
     * @var PackageService
     */
    protected $packageService;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var PackageRepository
     */
    protected $packageRepository;

    /**
     * @param ExtensionConfiguration $extensionConfiguration
     * @param AccountService $accountService
     * @param FilesystemService $filesystemService
     * @param PackageService $packageService
     * @param AccountRepository $accountRepository
     * @param PackageRepository $packageRepository
     */
    public function __construct(
        ExtensionConfiguration $extensionConfiguration,
        AccountService $accountService,
        FilesystemService $filesystemService,
        PackageService $packageService,
        AccountRepository $accountRepository,
        PackageRepository $packageRepository
    ) {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->accountService = $accountService;
        $this->filesystemService = $filesystemService;
        $this->packageService = $packageService;
        $this->accountRepository = $accountRepository;
        $this->packageRepository = $packageRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $requestUri = rawurldecode(ltrim($request->getAttribute('normalizedParams')->getRequestUri(), '/'));
        $requestUri = strpos($requestUri, '?') !== false ? strstr($requestUri, '?', true) : $requestUri;
        $response = null;

        try {
            // Human-readable frontend
            if ($requestUri == '') {
                // Check if frontend is enabled
                if ($this->extensionConfiguration->get('tk_composer', 'frontend/disable')) {
                    throw new \Exception();
                }

                /** @var Site $site */
                $site = $request->getAttribute('site');

                // Initialize TypoScriptFrontendController
                $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
                    TypoScriptFrontendController::class,
                    $GLOBALS['TYPO3_CONF_VARS'],
                    $site,
                    $site->getDefaultLanguage()
                );

                $account = $this->accountService->getAuthorizedAccount();
                $packages = $this->packageRepository->findByAccount($account);

                $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
                $standaloneView->setLayoutRootPaths(['EXT:tk_composer/Resources/Private/Layouts']);
                $standaloneView->setPartialRootPaths(['EXT:tk_composer/Resources/Private/Partials']);
                $standaloneView->setTemplatePathAndFilename('EXT:tk_composer/Resources/Private/Templates/Main.html');
                $standaloneView->assignMultiple([
                    'packages' => $packages,
                    'isLoggedIn' => is_object($account),
                    'loginError' => isset($_GET['login-error']),
                ]);

                $response = new Response();
                $response->getBody()->write($standaloneView->render());
            }

            // Request to `/login` or `/logout`
            elseif ($requestUri == 'login' || $requestUri == 'logout') {
                // Check if frontend is enabled
                if ($this->extensionConfiguration->get('tk_composer', 'frontend/disable')) {
                    throw new \Exception();
                }

                $suffix = '';

                if ($requestUri == 'login') {
                    $username = $_POST['username'] ?? '';
                    $password = $_POST['password'] ?? '';

                    if ($this->accountRepository->findByUsernameAndPassword($username, $password)) {
                        setcookie(
                            'auth',
                            $username . ':' . $this->accountService->getPasswordHashByPassword($password),
                            time() + 3600
                        );
                    } else {
                        // Prevent brute-forcing
                        sleep(2);

                        $suffix = 'login-error';
                    }
                } else {
                    // Invalidate cookie
                    setcookie('auth', '', time() - 86400);
                }

                header('Location: /' . ($suffix ? '?' . $suffix : ''), true, 302);

                exit;
            }

            // Request to `/robots.txt`
            elseif ($requestUri == 'robots.txt') {
                $data = [
                    'User-agent: *',
                    'Disallow: /',
                ];

                $response = $this->getPlainResponse($data);
            }

            // Initial request to `/packages.json`
            elseif ($requestUri == 'packages.json') {
                $data = [
                    'packages' => [],
                    'includes' => [],
                ];

                $account = $this->accountService->getAuthorizedAccount();
                $packages = $this->packageRepository->findByAccount($account);

                foreach ($packages as $package) {
                    $include = sprintf('include/%s$%s.json', $package->getPackageName(), $package->getHash());

                    $data['includes'][$include] = [
                        'sha256' => $package->getHash(),
                    ];
                }

                $response = $this->getJsonResponse($data);
            }

            // Request to the synchronization URI set in extension configuration
            elseif ($requestUri == 'update') {
                $errors = $this->packageService->updateAllPackages();

                if ($errors) {
                    $data = [
                        'status' => 'error',
                        'packages' => [],
                    ];

                    foreach ($errors as $error) {
                        /** @var Package $package */
                        $package = $error['package'];
                        /** @var \Exception $exception */
                        $exception = $error['exception'];

                        $data['packages'][$package->getRepositoryUrl()] = $exception->getMessage();
                    }
                } else {
                    $data = [
                        'status' => 'ok',
                    ];
                }

                $response = $this->getJsonResponse($data);
            }

            // Request to a single package
            elseif (preg_match('/^include\/' . Package::NAME_PATTERN . '\$[0-9a-f]{64}\.json$/', $requestUri)) {
                [$packageName, $hash] = explode('$', substr($requestUri, 8, -5));

                $package = $this->packageRepository->findByPackageName($packageName);

                if (!$package) {
                    throw new Exception(['Package "%s" does not exist', $packageName], 1603305837);
                }

                if ($package->getHash() != $hash) {
                    throw new \Exception(['Invalid hash for package "%s"', $packageName], 1603305845);
                }

                if ($package->getAccess() != Package::ACCESS_PUBLIC) {
                    $account = $this->accountService->getAuthorizedAccount();

                    if (!$account) {
                        throw new Exception(['Unable to access package "%s"', $packageName], 1603305970);
                    }

                    if ($package->getAccess() == Package::ACCESS_PRIVATE && !$account->getAllPackages()) {
                        $allowed = false;

                        /** @var Package $allowedPackage */
                        foreach ($account->getPackages() as $allowedPackage) {
                            if ($allowedPackage->getUid() == $package->getUid()) {
                                $allowed = true;

                                break;
                            }
                        }

                        if (!$allowed) {
                            throw new Exception(['Unable to access package "%s"', $packageName], 1603306135);
                        }
                    }
                }

                $path = $this->filesystemService->getStoragePathByPackage($package) . 'package';

                // Update package if package file is missing
                if (!@is_file($path)) {
                    $this->packageService->updatePackage($package);
                }

                $content = @file_get_contents($path);
                $data = json_decode($content, true);
                $response = $this->getJsonResponse($data);
            }

            // Request to another URL of the frontend
            else {
                throw new \Exception();
            }
        } catch(\Exception $e) {
            /** @var ErrorController $errorController */
            $errorController = GeneralUtility::makeInstance(ErrorController::class);

            $response = $errorController->unavailableAction($request, '');
        }

        return $response ?? $handler->handle($request);
    }

    /**
     * Get JSON response message
     *
     * @param array $data
     * @return Message
     */
    protected function getJsonResponse(array $data = []) : Message
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Get plain text response message
     *
     * @param array $data
     * @return Message
     */
    protected function getPlainResponse(array $data = []) : Message
    {
        $response = new Response();
        $response->getBody()->write(implode("\n", $data));

        return $response->withHeader('Content-Type', 'text/plain');
    }
}
