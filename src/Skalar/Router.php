<?php

namespace Skalar;

use Bitrix\Main\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router as SymfonyRouter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Skalar\Request;
use Skalar\AdvancedLoader;

/**
 * Class Router
 * @package Skalar
 */
class Router extends \CBitrixComponent
{
    /**
     * @var string
     */
    private $restDir;
    /**
     * @var string
     */
    private $controllersDir;
    /**
     * @var
     */
    private $classesDir;
    /**
     * @var
     */
    private $router;
    /**
     * @var
     */
    public $request;
    /**
     * @var array
     */
    public $config;
    /**
     * @var string
     */
    private $configDir;
    /**
     * @var string
     */
    private $middlewareDir;
    /**
     * @var
     */
    private $restRoute;

    /**
     * Bitrix module list
     * @var array
     */
    private $moduleList = [];
    /**
     * @var \Skalar\Controller\Base
     */
    private $controller;
    /**
     * @var string
     */
    private $action;

    /**
     * SkalarRouter constructor.
     * @param null $component
     */
    public function __construct($component = null)
    {
        $this->restDir = $this->restRoute = 'rest';
        $this->controllersDir = 'controllers';
        $this->classesDir = 'classes';
        $this->configDir = 'config';
        $this->middlewareDir = 'middleware';
        $this->config = [];

        parent::__construct($component);
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        $this->moduleList = $this->getModuleList();
        $this->includeModules();

        $this->setLoaders();
        $this->initConfig();
        $this->request = Request::createFromGlobals();
        $this->request->setBaseUrl($this->getBaseUrl());
        $this->initRouter();
        $state = $this->getState();
        $state = $this->runMiddleware($state);
        try {
            $parameters = $this->router->matchRequest($this->request);
            $this->request->attributes->add($parameters);
            $state = $this->executeController($state);
        } catch (\Exception $e) {
            $state = $this->callController('NotFoundController::index', $state);
            $this->controller->setStatus(Response::HTTP_NOT_FOUND);
        }
        $content = $this->render($state, $this->request->getPathInfo());
        $this->sendResponse($content);
    }

    /**
     * @param array $state
     * @param $url
     * @return string
     */
    protected function render(array $state, $url)
    {
        return $this->controller->getRender()($state, $url);
    }

    /**
     * @param $content
     * @param int $status
     */
    protected function sendResponse($content)
    {
        $response = new Response($content);
        $response->setStatusCode($this->controller->getStatus());
        $response->headers = $this->controller->getHeaders();
        $response->send();
    }

    /**
     * @return array
     * @todo create state
     */
    protected function getState()
    {
        return [];
    }

    /**
     * Initialize module list for loading from bitrix.
     * Can be reload in user class
     *
     * @return array
     */
    protected function getModuleList()
    {
        return [];
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    protected function includeModules()
    {
        if (sizeof($this->moduleList)) {
            foreach($this->moduleList as $module) {
                if(!Loader::includeModule($module)) {
                    throw new \Exception(sprintf('%s module not included!', $module));
                }
            }
        }
    }

    /**
     *
     */
    private function setLoaders()
    {
        $this->setLoader($this->getFullTemplateFolder($this->restDir));
        $this->setLoader($this->getFullTemplateFolder($this->controllersDir));
        $this->setLoader($this->getFullTemplateFolder($this->classesDir));
    }

    /**
     *
     */
    protected function initConfig()
    {
        $fileList = $this->getAllFolderFiles($this->configDir);
        $this->config['paths'] = [];
        foreach($fileList as $fileName) {
            list($name, $extension) = explode('.', $fileName, 2);
            if($name && $extension == 'yaml') {
                try {
                    $path = $this->getFullTemplateFolder($this->configDir) . '/' . $fileName;
                    $this->config['paths'][$name] = $path;
                    $this->config[$name] = Yaml::parseFile($path);
                } catch (\Exception $exception) {
                    printf('Unable to parse the YAML string: %s', $exception->getMessage()); //todo write to log
                }
            }
        }
    }

    /**
     *
     */
    protected function initRouter()
    {
        $fileLocator = new FileLocator(array(__DIR__));
        $requestContext = new RequestContext();
        $requestContext->fromRequest($this->request);
        $requestContext->setBaseUrl('/path');
        $this->router = new SymfonyRouter(
            new AdvancedLoader($fileLocator, $this->getAllFolderFiles($this->restDir)),
            $this->config['paths']['routes'],
            array(),
            $requestContext
        );
    }

    /**
     * @param $folder
     */
    private function setLoader($folder){
        spl_autoload_register(function($class) use ($folder)
        {
            $arClass = explode('\\', trim($class, '\\'));
            $class = end($arClass);
            $file = rtrim($folder, '/') . '/' . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    /**
     * @param $state
     * @return mixed
     * @throws \Exception
     */
    protected function runMiddleware($state)
    {
        $fileList = $this->getAllFolderFiles($this->middlewareDir);
        foreach($fileList as $fileName) {
            list($class, $extension) = explode('.', $fileName, 2);
            if($extension == 'php') {
                $classPath = $this->getFullTemplateFolder($this->middlewareDir) . '/' . $fileName;
                if(file_exists($classPath)) {
                    require_once($classPath);
                } else {
                    throw new \Exception(sprintf('File "%s" is not exist.', $classPath));
                }
                $middleware = $this->instantiateClass($class);
                $state = $middleware($this->request, $state);
            }
        }
        return $state;
    }

    /**
     * @param array $state
     * @return mixed
     */
    private function executeController(array $state)
    {
        $controllerResolver = new ControllerResolver();
        $controller = $controllerResolver->getController($this->request);
        $this->setController($controller);
        return \call_user_func_array($controller, [$this->request, $state]);
    }

    /**
     * @param $controller
     * @param array $arguments
     * @return mixed
     */
    public function callController($controller, $state, array $arguments = [])
    {
        $callable = $this->createController($controller);
        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('The controller %s is not callable.', $callable));
        }
        $this->setController($controller);
        if($arguments) {
            $this->request->attributes->add($arguments);
        }
        return \call_user_func_array($callable, [$this->request, $state]);
    }

    /**
     * @param $controller
     * @return array
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }
        list($class, $method) = explode('::', $controller, 2);
        return array($this->instantiateClass($class), $method);
    }

    /**
     * @param $class
     * @return mixed
     */
    private function instantiateClass($class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        return new $class();
    }

    private function setController(array $controller)
    {
        $this->controller = $controller[0];
        $this->action = $controller[1];
    }

    /**
     * @return string
     */
    public function getTemplateNameExt()
    {
        $templateName = $this->getTemplateName();
        return $templateName ? $templateName : '.default';
    }

    /**
     * @return null|string
     */
    public function getTemplateFolder()
    {
        $folder = '';
        $template = $this->getTemplate();
        if($template instanceof \CBitrixComponentTemplate) {
            $folder = $template->GetFolder();
        } else {
            $folder = $this->getPath() . '/templates/' . $this->getTemplateNameExt();
        }
        return $folder;
    }

    /**
     * @param string $folder
     * @return string
     */
    public function getFullTemplateFolder($folder = '')
    {
        $resFolder = $_SERVER["DOCUMENT_ROOT"].$this->getTemplateFolder();
        if($folder) {
            $resFolder .= '/' . $folder;
        }
        return $resFolder;
    }

    /**
     * @param $folder
     * @return array
     */
    private function getAllFolderFiles($folder)
    {
        $dirPath = $this->getFullTemplateFolder($folder);
        $fileList = scandir($dirPath);
        $fileList = array_diff($fileList, ['.', '..']);
        return array_values($fileList);
    }

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        return isset($this->arParams['BASE_URL']) ? $this->arParams['BASE_URL'] : '';
    }
}