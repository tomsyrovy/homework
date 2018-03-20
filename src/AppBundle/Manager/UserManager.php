<?php
/**
 * Project: intergram-homework
 * File: UserManager.php
 * Author: Tomas SYROVY <tomas@syrovy.pro>
 * Date: 19.03.18
 * Version: 1.0
 */

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use FOS\RestBundle\Request\ParamFetcherInterface;

class UserManager {

    /**
     * Create a new user entity, fill it by params and return it
     *
     * @param \FOS\RestBundle\Request\ParamFetcherInterface $paramFetcher
     *
     * @return \AppBundle\Entity\User
     */
    public static function createByParams(ParamFetcherInterface $paramFetcher): User{

        $user = new User();

        $user = self::setAttributes($user, $paramFetcher);

        return $user;

    }

    /**
     * Update a user entity, fill it by params and return it
     *
     * @param \AppBundle\Entity\User                        $user
     * @param \FOS\RestBundle\Request\ParamFetcherInterface $paramFetcher
     *
     * @return \AppBundle\Entity\User
     */
    public static function updateByParams(User $user, ParamFetcherInterface $paramFetcher): User{

        $user = self::setAttributes($user, $paramFetcher);

        return $user;

    }

    /**
     * Fill a user entity by params and return it
     *
     * @param \AppBundle\Entity\User                        $user
     * @param \FOS\RestBundle\Request\ParamFetcherInterface $paramFetcher
     *
     * @return \AppBundle\Entity\User
     */
    private static function setAttributes(User $user, ParamFetcherInterface $paramFetcher): User{

        foreach($paramFetcher->all() as $key => $value){
            if($value){
                $user = self::setAttribute($user, $key, $value);
            }
        }

        return $user;

    }

    /**
     * Fill a user entity by key and value and return it
     *
     * @param \AppBundle\Entity\User $user
     * @param                        $key
     * @param                        $value
     *
     * @return \AppBundle\Entity\User
     */
    private static function setAttribute(User $user, $key, $value): User{

        switch($key){
            case 'username' : {
                $user->setUsername($value);
            }break;
            case 'email' : {
                $user->setEmail($value);
            }break;
            case 'firstname' : {
                $user->setFirstname($value);
            }break;
            case 'lastname' : {
                $user->setLastname($value);
            }break;
        }

        return $user;

    }

}