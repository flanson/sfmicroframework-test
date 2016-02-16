<?php

namespace App;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * Created by PhpStorm.
 * User: dave
 * Date: 11/02/16
 * Time: 16:19
 */
class App
{
    const CONFIG_FILE_NAME = './../Resources/config/app_config.yml';
    const DEFAULT_TEMPLATE_DIRECTORY = '/../Resources/views';
    const DEFAULT_CONFIG_DIRECTORY = '/../Resources/config';
    const APP_CONFIG_YML_FILE_NAME = 'app_config.yml';
    const APP_CONFIG_CACHE_FILENAME = '/../../cache/appConfigCache.php';
    /**
     * @var AppRouteCollection
     */
    private $appRouteCollection;

    /**
     * @var Config
     */
    private $config = null;

    /**
     * @var \Twig_Environment
     */
    private $twigEnv = null;

    /**
     * @var TwigParameterBag
     */
    private $twigParameterBag;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->loadConfig();
        $this->twigParameterBag = new TwigParameterBag();
        $this->appRouteCollection = new AppRouteCollection();
    }

    /**
     * Load configuration from Yaml file
     */
    private function loadConfig()
    {
        $cachePath = __DIR__ . self::APP_CONFIG_CACHE_FILENAME;
        $cachedConfig = new ConfigCache($cachePath, true);
        if(!$cachedConfig->isFresh()){
            $resources = [];
            $configDirectories = array(__DIR__. self::DEFAULT_CONFIG_DIRECTORY);
            $locator = new FileLocator($configDirectories);
            $loaderResolver = new LoaderResolver(array(new YamlAppConfigLoader($locator)));
            $delegatingLoader = new DelegatingLoader($loaderResolver);
            $appConfigFileNameList = $locator->locate(self::APP_CONFIG_YML_FILE_NAME, null, false);
            $configArrayToCache = [];
            foreach ($appConfigFileNameList as $appConfigFileName) {
                $configArrayToCache = array_merge($configArrayToCache, $delegatingLoader->load($appConfigFileName));
                $resources[] = new FileResource($appConfigFileName);
            }
            $processor = new Processor();
            $configuration = new Config();
//            $processedConfiguration = $processor->processConfiguration(
//                $configuration,
//                [$configArrayToCache['application_test']]
//            );
//            var_dump($processedConfiguration);
            $cachedConfigCode = '<?php return '.var_export($configArrayToCache, true).';'; // rar :)
            $cachedConfig->write($cachedConfigCode, $resources);
        }
        if (file_exists($cachePath)) {
            $configArray = (array)require $cachePath;
        }

        if (isset($configArray['application'])) {
            $config = new Config($configArray['application']);
            $this->config = $config;
        }
        $resourceDirectories = [__DIR__.self::DEFAULT_TEMPLATE_DIRECTORY];
        if (isset($configArray['twig_ressource_directory'])) {
            $templateResourceDirectory = $configArray['twig_ressource_directory'];
            $resourceDirectories = array_merge($resourceDirectories, [__DIR__.$templateResourceDirectory]);
        }
        $this->createTwigEnv($resourceDirectories);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $resourceDirectories
     */
    private function createTwigEnv($resourceDirectories)
    {
        $twigLoader = new \Twig_Loader_Filesystem($resourceDirectories);
        $this->twigEnv = new \Twig_Environment($twigLoader);
    }

    /**
     * Main function to launch the application
     */
    public function run()
    {
        $request = Request::createFromGlobals();
        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);
        $matcher = new UrlMatcher($this->appRouteCollection->getRoutes(), $requestContext);
        try {
            $test = $matcher->matchRequest($request);
        } catch (ResourceNotFoundException $ex) {
            $this->send404();
        }
        $this->twigParameterBag
            ->setParameter('facebookDomain', $this->getConfig()->getFacebookDomain())
            ->setParameter('facebookAccessToken', $this->getConfig()->getAccessToken())
            ->setParameter('shortDomainList', $this->getConfig()->getAuthShortyDomains())
            ->setParameter('title', 'Test application Facebook');
        $content = $this->twigEnv->render("index.html.twig", $this->twigParameterBag->getParametersBag());
        $response = new Response($content, 200, array('Content-Type' => 'text/html; charset=UTF-8'));
        $response->send();
    }

    public function send404()
    {
        $response = new Response('Not Found', 404, array('Content-Type' => 'text/plain; charset=UTF-8'));
        $response->send();
    }
}