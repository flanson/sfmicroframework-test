<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 15/02/16
 * Time: 14:46
 */

namespace App;


use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlAppConfigLoader extends FileLoader
{

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return mixed $configValues
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $configValues = Yaml::parse(file_get_contents($resource));
        //@todo check that the configuration found in the file fits the application config
        return $configValues;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}