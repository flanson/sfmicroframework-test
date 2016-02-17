<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Created by PhpStorm.
 * User: dave
 * Date: 11/02/16
 * Time: 16:19
 */
class App extends Kernel
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
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
        $this->loadConfig();
        $this->twigParameterBag = new TwigParameterBag();
        $this->appRouteCollection = new AppRouteCollection();
    }

    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new TwigBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new WebProfilerBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        // PHP equivalent of config.yml
        $c->loadFromExtension('framework', array(
            'secret' => 'S0ME_SECRET',
            'profiler' => null,
            'templating' => ['engines' => ['twig']],
        ));

        if (isset($this->bundles['WebProfilerBundle'])) {
            $c->loadFromExtension('web_profiler', ['toolbar' => true]);
        }

        // add configuration parameters
        $c->setParameter('mail_sender', 'user@example.com');

        // register services
        $c->register('app.markdown', 'AppBundle\\Service\\Parser\\Markdown');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        // kernel is a service that points to this class
        // optional 3rd argument is the route name
        $routes->mount('/_wdt', $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml'));
        $routes->mount('/_profiler', $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml'));

        $routes->add('/random/{limit}', 'kernel:randomAction');
        $routes->add('home_route', 'kernel:run');
        $routes->add('/', 'kernel:run');
    }

    public function randomAction($limit)
    {
        return new JsonResponse(array(
            'number' => rand(0, $limit)
        ));
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