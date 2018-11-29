<?php

namespace Skalar\Routing;
use Skalar\Request;


class Middleware
{
    /**
     * @var
     */
    private $dir;

    /**
     * Middleware constructor.
     * @param $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param Request $request
     * @param array $state
     * @return array
     * @throws \Exception
     */
    public function execute(Request $request, array $state)
    {
        $fileList = $this->getAllFolderFiles();

        foreach($fileList as $fileName) {
            list($class, $extension) = explode('.', $fileName, 2);

            if($extension == 'php') {
                $classPath = $this->dir . '/' . $fileName;

                if(file_exists($classPath)) {
                    require_once($classPath);
                } else {
                    throw new \Exception(sprintf('File "%s" is not exist.', $classPath));
                }
                $middleware = $this->instantiateClass($class);
                $state = $middleware($request, $state);
            }
        }
        return $state;
    }

    /**
     * @return array
     */
    private function getAllFolderFiles()
    {
        $fileList = scandir($this->dir);
        $fileList = array_diff($fileList, ['.', '..']);

        return array_values($fileList);
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
}