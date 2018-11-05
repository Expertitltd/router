<?php

namespace Skalar;

use Bitrix\Main\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router as SymfonyRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Yaml\Yaml;

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
     * SkalarRouter constructor.
     * @param null $component
     */
    public function __construct($component = null)
    {
        $this->restDir = $this->restRoute =  'rest';
        $this->controllersDir = 'controllers';
        $this->configDir = 'config';
        $this->middlewareDir = 'middleware';
        $this->config = [];

        parent::__construct($component);
    }

    /**
     * @return mixed
     */
    public function executeComponent()
    {
        $this->initConfig();
        $this->request = Request::createFromGlobals();
        $this->initRouter();
        $state = $this->getState();
        try {
            if($this->isRest()) {
                $state = $this->executeRestController($state);
            } else {
                $parameters = $this->router->matchRequest($this->request);
                $this->request->attributes->add($parameters);
                $state = $this->executeController($state);
            }
            $state = $this->runMiddleware($state);
        } catch(\Exception $e) {
            print_r($e->getMessage());
            $state = $this->callController('NotFoundController::index', $state);
        }
        $response = $this->render($state);
        $this->echoResponse($response);
    }

    /**
     * @param array $state
     * @return string
     * @todo get and call render function from External class (from config)
     */
    protected function render(array $state)
    {
        $response = json_encode($state);
        return $response;
    }

    /**
     * @param $response
     */
    protected function echoResponse($response)
    {
        echo $response;
        exit();
    }

    /**
     * @return array
     * @todo create state
     */
    protected function getState()
    {
        $state = [];
        $state['state'] = 'state';
        return $state;
    }

    /**
     * @param array $modules
     * @throws \Exception
     */
    protected function includeModules(array $modules)
    {
        foreach($modules as $module) {
            if(!Loader::includeModule($module)) {
                throw new \Exception(sprintf('%s module not included!', $module));
            }
        }
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
        $this->router = new SymfonyRouter(
            new YamlFileLoader($fileLocator),
            $this->config['paths']['routes'],
            array(),
            $requestContext
        );
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
        $this->loadController();
        $controllerResolver = new ControllerResolver();
        $controller = $controllerResolver->getController($this->request);
        return \call_user_func_array($controller, [$this->request, $state]);
    }

    /**
     * @param array $state
     * @return mixed
     */
    private function executeRestController(array $state)
    {
        $controller = $this->getRestControllerParams();
        $this->loadRestController($controller);
        $callable = $this->getRestController($controller);
        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('The controller %s is not callable.', $callable));
        }
        return \call_user_func_array($callable, [$this->request, $state]);

    }

    /**
     * @return bool
     */
    protected function isRest() {
        return explode('/', trim($this->request->getPathInfo(), '/'))[0] == $this->restRoute;
    }

    /**
     * @return array
     */
    protected function getRestControllerParams()
    {
        $pathInfo = trim($this->request->getPathInfo(), '/');
        $arPaths = explode('/', $pathInfo);
        array_shift($arPaths);
        $controller = ucfirst(array_shift($arPaths));
        if(!empty($arPaths)) {
            throw new NotFoundHttpException(sprintf('URI "%s" is not exist.', $this->request->getPathInfo()));
        }
        if(empty($controller)) {
            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not exist.', $controller, $this->request->getPathInfo()));
        }
        $action = strtolower($this->request->getMethod());
        if(empty($action)) {
            throw new \InvalidArgumentException(sprintf('Action "%s" for URI "%s" is not exist.', $action, $this->request->getPathInfo()));
        }
        return [
            $controller,
            $action
        ];
    }

    /**
     * @param array $controller
     */
    private function loadRestController(array $controller)
    {
        $class = $controller[0];
        $classPath = $this->getFullTemplateFolder($this->restDir) . '/' . $class . '.php';
        if(file_exists($classPath)) {
            require_once($classPath);
        } else {
            throw new NotFoundHttpException(sprintf('Controller "%s" for URI "%s" is not exist.', $class, $this->request->getPathInfo()));
        }
    }

    /**
     * @param array $controller
     * @return array
     */
    private function getRestController(array $controller) {
        return [
            $this->instantiateClass($controller[0]),
            $controller[1]
        ];
    }

    /**
     * @param $controller
     * @param array $arguments
     * @return mixed
     */
    public function callController($controller, $state, array $arguments = [])
    {
        $this->loadController($controller);
        $callable = $this->createController($controller);
        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('The controller %s is not callable.', $callable));
        }
        if($arguments) {
            $this->request->attributes->add($arguments);
        }
        return \call_user_func_array($callable, [$this->request, $state]);
    }

    /**
     * @param string $class
     */
    private function loadController($class = '') {
        $class = empty($class) ? $this->request->attributes->get('_controller') : $class;
        $class = explode('::', $class)[0];
        $arClassPath = [
            'templates',
            $this->getTemplateNameExt()
        ];
        $arClass = explode('\\', $class);
        $arClassPath[] = stripos(reset($arClass), $this->restDir) !== false ? $this->restDir : $this->controllersDir;
        $className = end($arClass);
        $arClassPath[] = $className;
        $classPath = $_SERVER['DOCUMENT_ROOT'].$this->getPath().'/'.implode('/', $arClassPath).'.php';
        if(file_exists($classPath)) {
            require_once($classPath);
        } else {
            throw new NotFoundHttpException(sprintf('Controller "%s" for URI "%s" is not exist.', $class, $this->request->getPathInfo()));
        }
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
}