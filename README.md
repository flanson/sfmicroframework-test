# Facebook-test
Quick and dirty creation of app then transmutation to microframework Traits of symfony
  - [Facebook php SDK][FbPhpSdk] (not really used)
  - YAML parser
  - Twig
  - Symfony/Config (Loading Resources|Cache Config)
  - Option resolver
  - Request/Response
  - Routing
  - Apc autoloader

### Version
0.0.1

### Todos

 - ?/ add '[symfony/dependency-injection][symfony/dependency-injection]' to manage services as they should be 
 - ?/ add '[symfony/config][symfony/config]' to manage configuration
    - [Definition][config-definition]
 - Change whole code to use MicroKernel Trait (in order to understand first then use the microKernel) [1][microframework1],[2][microframework2] [ex1][microframeworkEx1],[ex2][microframeworkEx2]
 - See how to create an installation
 - ...

License
----

MIT

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)

   [FbPhpSdk]: <https://github.com/facebook/facebook-php-sdk-v4/>
   [symfony/config]: <https://github.com/symfony/config>
   [symfony/options-resolver]: <https://github.com/symfony/options-resolver>
   [symfony/dependency-injection]: <https://github.com/symfony/dependency-injection>
   [symfony/routing]: <https://github.com/symfony/routing>
   [symfony/debug]: <https://github.com/symfony/debug>
   [symfony/http-foundation]: <https://github.com/symfony/http-foundation>
   [symfony/http-kernel]: <https://github.com/symfony/http-kernel>
   [microframework1]: <http://symfony.com/blog/new-in-symfony-2-8-symfony-as-a-microframework>
   [microframework2]: <http://symfony.com/doc/current/cookbook/configuration/micro-kernel-trait.html>
   [microframeworkEx1]: <https://github.com/henrikbjorn/Muse/blob/master/src/Application.php>
   [microframeworkEx2]: <https://github.com/henrikbjorn/Muse/blob/master/src/Kernel.php>
   [config-caching]: <http://symfony.com/doc/current/components/config/caching.html>
   [config-definition]: <http://symfony.com/doc/current/components/config/definition.html>

