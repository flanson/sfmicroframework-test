<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 12/02/16
 * Time: 15:02
 */

namespace App;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $facebookDomain = '';

    /**
     * @var array
     */
    private $authShortyDomains = [];
    private $accessToken = [];

    public function __construct($configArray = [])
    {
        if (isset($configArray['facebook_domain'])) {
            $this->facebookDomain = $configArray['facebook_domain'];
        }
        if (isset($configArray['facebook_access_token'])) {
            $this->accessToken = $configArray['facebook_access_token'];
        }
        if (isset($configArray['auth_shorty_domains'])) {
            $accessTokenConfig = [];
            foreach ($configArray['auth_shorty_domains'] as $domain_Key => $auth_shorty_domain) {
                $validConfig = $this->resolveBitlyAuthItemOptions($auth_shorty_domain);
                $accessTokenConfig[$domain_Key] = $validConfig;
            }
            $this->authShortyDomains = $accessTokenConfig;
        }
    }

    /**
     * @return array
     */
    public function getAuthShortyDomains()
    {
        return $this->authShortyDomains;
    }

    /**
     * @return string
     */
    public function getFacebookDomain()
    {
        return $this->facebookDomain;
    }

    /**
     * @return array
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        // TODO: Implement getConfigTreeBuilder() method.
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('application');

        $rootNode
            ->ignoreExtraKeys(true)
            ->children()
                ->arrayNode('facebook_access_token')
                    ->info('List of access tokens')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                    ->end()
                ->end()
                ->arrayNode('auth_shorty_domains')
                    ->info('List auth node for each domain')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('login')->end()
                            ->scalarNode('appkey')->end()
                            ->scalarNode('access_token')->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('facebook_domain')
                    ->info('The facebook domain. ex: https://graph.facebook.com/v2.5/')
                    ->defaultValue('')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param array $bitlyAuthItem
     * @return array
     */
    public function resolveBitlyAuthItemOptions(array $bitlyAuthItem)
    {
        $resolver = new OptionsResolver();
        //add public configureBitlyAuthItemOptions function in order to add supplementary validation
        $this->configureBitlyAuthItemOptions($resolver);
        return $resolver->resolve($bitlyAuthItem);

    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureBitlyAuthItemOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('login', 'appkey', 'access_token'));
    }
}