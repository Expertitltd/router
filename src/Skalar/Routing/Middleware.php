<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27.11.18
 * Time: 14:25
 */

namespace Skalar\Routing;


class Middleware
{
    /**
     * @var
     */
    private $dir;

    /**
     * Middleware constructor.
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param $state
     * @return mixed
     * @throws \Exception
     */
    public function execute($state)
    {
        $fileList = $this->getAllFolderFiles($this->middlewareDir);

        foreach($fileList as $fileName) {
            list($class, $extension) = explode('.', $fileName, 2);

            if($extension == 'php') {
                $classPath = $this->dir . '/' . $fileName;

                if(file_exists($classPath)) {
                    require_once($classPath);
                } else {
                    throw new \Exception(sprintf('File "%s" is not exist.', $classPath));
                }
//                $middleware = $this->instantiateClass($class);
//                $state = $middleware($this->request, $state);
            }
        }
        return $state;
    }

    /**
     * @param $folder
     * @return array
     */
    private function getAllFolderFiles($folder)
    {
        $fileList = scandir($this->dir);
        $fileList = array_diff($fileList, ['.', '..']);

        return array_values($fileList);
    }
}