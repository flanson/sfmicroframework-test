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
            ->children()
                ->arrayNode('facebook_access_token')
//                    ->info('List of access tokens')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('value')->end()
                        ->end()
                    ->end()
//                    ->children()
//                        ->scalarNode('comment')->end()
//                        ->scalarNode('share')->end()
//                        ->scalarNode('link')->end()
//                    ->end()
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
//                    ->children()
//                        ->scalarNode('comment')->end()
//                        ->scalarNode('share')->end()
//                        ->scalarNode('link')->end()
//                    ->end()
                ->end()
                ->scalarNode('facebook_domain')
                    ->info('The facebook domain. ex: https://graph.facebook.com/v2.5/')
//                    ->addDefaultsIfNotSet()
                    ->defaultValue('')
                ->end()
            ->end()
        ;
//    facebook_domain: 'https://graph.facebook.com/v2.5/'
//    facebook_access_token:
//        comment: '/comments?access_token=CAAMRtuvxav4BAKOmkDKXfB2IwaHQfdtQAgQlw3BHTVMqvSCt165fOeGENAvw3b8YXa0ZBW2AXkcOdvcv63TSIkRBRecGL4ZAGhxoZAajfmUfYoegOnU46Fnj9RZCOrzTgbNRZB01mwBDwt9RbK7dEs0ws9soVEAUGh1cqU8rWjC3XUMRwnkViNdn2la41PGkZD&summary=true'
//        share: '?access_token=CAAMRtuvxav4BAKOmkDKXfB2IwaHQfdtQAgQlw3BHTVMqvSCt165fOeGENAvw3b8YXa0ZBW2AXkcOdvcv63TSIkRBRecGL4ZAGhxoZAajfmUfYoegOnU46Fnj9RZCOrzTgbNRZB01mwBDwt9RbK7dEs0ws9soVEAUGh1cqU8rWjC3XUMRwnkViNdn2la41PGkZD&fields=shares'
//        link: '?access_token=CAAMRtuvxav4BAKOmkDKXfB2IwaHQfdtQAgQlw3BHTVMqvSCt165fOeGENAvw3b8YXa0ZBW2AXkcOdvcv63TSIkRBRecGL4ZAGhxoZAajfmUfYoegOnU46Fnj9RZCOrzTgbNRZB01mwBDwt9RbK7dEs0ws9soVEAUGh1cqU8rWjC3XUMRwnkViNdn2la41PGkZD&fields=link'
//    auth_shorty_domains:
//        'f24.my':
//            login: 'france24market'
//            appkey: 'R_992f20f542dbdb82654749a49eb64ab5'
//            access_token: 'e0c45ad830d78e02bc2ee39aefc427330ea07a2f'
//        'rfi.my':
//            login: 'shortyrfi'
//            appkey: 'R_2244bd9d4d8dd662d08e55747eeddc86'
//            access_token: 'fd64998da9e2c10cd9036cfbeb834330616b4fec'
//        'mc-d.co':
//            login: 'shortymcd'
//            appkey: 'R_71ee788f1e3dbdbc1bb7432d864bc223'
//            access_token: 'a455b1552e33fd2df86dddf9a1dfbefa13cde6e9'
//        'fmm.io':
//            login: 'shortyfmm'
//            appkey: 'R_47eb68ba9593e09296654e1a454375e0'
//            access_token: '2ada0f81118318288007ef050a99066afbf7db57'
//        'bit.ly':
//            login: 'shortygeneric'
//            appkey: 'R_7bafeab6ad5705561b32c5ae415198b5'
//            access_token: '90d9312df18cad32c5029b38db8de08f8b2d55c4'
//        'on.mash.to':
//            login: 'shortymashfr'
//            appkey: 'todo'
//            access_token: 'e98a992dba212f2453e79887f9257f8ebe238ae9'

//        $this->addConstraints($rootNode);
        // ... add node definitions to the root of the tree

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