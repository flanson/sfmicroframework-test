<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 12/02/16
 * Time: 17:00
 */

namespace App;


class TwigParameterBag
{
    /**
     * @var array
     */
    private $parametersBag = [];

    /**
     * TwigParameterBag constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getParametersBag()
    {
        return $this->parametersBag;
    }

    /**
     * @param $parameterName
     * @return array
     */
    public function getParameter($parameterName)
    {
        if (isset($this->parametersBag[$parameterName])) {
            return $this->parametersBag[$parameterName];
        }
        return null;
    }

    /**
     * @param $parameterName
     * @param $parameter
     * @return TwigParameterBag
     */
    public function setParameter($parameterName, $parameter)
    {
        $this->parametersBag = array_merge($this->parametersBag, [$parameterName => $parameter]);
        return $this;
    }


}