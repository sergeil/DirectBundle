<?php
namespace Neton\DirectBundle\Api;

use Symfony\Component\Finder\Finder;
/**
 * Controller Finder find all controllers from a Bundle.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class ControllerFinder
{
    /**
     * Find all controllers from a bundle, nested controllers will also be taken into account.
     * 
     * @param  \Symfony\HttpKernel\Bundle\Bundle $bundle
     * @return Mixed
     */
    public function getControllers($bundle)
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = $bundle->getPath().$ds."Controller";
        $controllers = array();
        
        if (is_dir($dir)) {
            $finder = new Finder();            
            $finder->files()->in($dir)->name('*Controller.php');
            
            foreach ($finder as $file) {
                /* @var \SplFileInfo $file */
                $expFilename = explode($ds, $file->getPath());
                $expDir = explode($ds, $dir);

                $isNested = false;
                $nestedPath = array_slice($expFilename, count($expDir));

                $class = $bundle->getNamespace().'\\Controller';
                $name = explode('.', $file->getFileName());
                if (count($expFilename) > count($expDir)) { // located in subdirectory
                    $isNested = true;
                    $class .= '\\'.implode('\\', $nestedPath).'\\'.$name[0];
                } else {
                    $class .= '\\'.$name[0];
                }
                
                $controllers[] = array(
                    'class' => $class,
                    'isNested' => $isNested,
                    'path' => $nestedPath
                );
            }
        }

        return $controllers;
    }
}
