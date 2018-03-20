<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest controller for users
 */
class UserController extends FOSRestController
{

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     *
     * @throws \LogicException
     */
    private function getManager(): ObjectManager{

        return $this->getDoctrine()->getManager();

    }

    /**
     * @return \AppBundle\Repository\UserRepository
     *
     * @throws \LogicException
     */
    private function getRepository(): UserRepository{

        return $this->getManager()->getRepository(User::class);

    }

    /**
     * @param $id
     *
     * @return null|User
     *
     * @throws \LogicException
     */
    private function getUserById($id){

        /** @var User $entity */
        $entity = $this->getRepository()->find($id);

        return $entity;

    }


    /**
     * List of all users.
     *
     * @Annotations\Route("api/users/", methods={"GET"}, name="get_users")
     *
     * @ApiDoc(
     *      statusCodes = {
     *          200 = "Returned when successful"
     *      }
     * )
     * @Annotations\View()
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function getUsersAction(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Get a single user.
     *
     * @Annotations\Route("api/user/{id}/", methods={"GET"}, name="get_user")
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     * @Annotations\View(templateVar="user")
     *
     * @param int $id the user id
     *
     * @return View
     *
     * @throws \LogicException
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserAction($id): View
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return new View('User not found.', Response::HTTP_NOT_FOUND);
        }

        return new View($user);

    }

    /**
     * Creates a new user from the submitted data.
     *
     * @Annotations\Route("api/user/", methods={"POST"})
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\UserType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Returned when there are errors"
     *   }
     * )
     * @Annotations\View()
     *
     * @Annotations\QueryParam(name="username", description="Username", nullable=false, allowBlank=false)
     * @Annotations\QueryParam(name="email", description="E-mail", nullable=false, allowBlank=false)
     * @Annotations\QueryParam(name="firstname", description="Firstname", nullable=false, allowBlank=false)
     * @Annotations\QueryParam(name="lastname", description="Lastname", nullable=false, allowBlank=false)
     *
     * @param ParamFetcherInterface $paramFetcher the paramFetcher object
     *
     * @return View
     *
     * @throws \LogicException
     */
    public function postUsersAction(ParamFetcherInterface $paramFetcher): View
    {

        try{

            $user = UserManager::createByParams($paramFetcher);
            $em = $this->getManager();

            $em->persist($user);
            $em->flush();

        }catch(UniqueConstraintViolationException $e){
            return new View('User already exists.', Response::HTTP_NOT_ACCEPTABLE);
        }catch(NotNullConstraintViolationException $e){
            return new View('Not fully submitted data.', Response::HTTP_NOT_ACCEPTABLE);
        }

        return new View($user, Response::HTTP_OK);

    }

    /**
     * Update existing user from the submitted data.
     *
     * @Annotations\Route("api/user/{id}/", methods={"PUT"})
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\UserType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when is not found",
     *     500 = "Returned when there are errors",
     *   }
     * )
     * @Annotations\View()
     *
     * @Annotations\QueryParam(name="username", description="Username", nullable=true, allowBlank=true)
     * @Annotations\QueryParam(name="email", description="E-mail", nullable=true, allowBlank=true)
     * @Annotations\QueryParam(name="firstname", description="Firstname", nullable=true, allowBlank=true)
     * @Annotations\QueryParam(name="lastname", description="Lastname", nullable=true, allowBlank=true)
     *
     * @param ParamFetcherInterface $paramFetcher the paramFetcher object
     * @param int     $id      the user id
     *
     * @return View
     *
     * @throws \LogicException
     * @throws NotFoundHttpException when user not exist
     */
    public function putUsersAction($id, ParamFetcherInterface $paramFetcher): View
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return new View('User not found.', Response::HTTP_NOT_FOUND);
        }

        $user = UserManager::updateByParams($user, $paramFetcher);

        $em = $this->getManager();

        $em->persist($user);
        $em->flush();

        return new View($user, Response::HTTP_OK);

    }

    /**
     * Removes a user.
     *
     * @Annotations\Route("api/user/{id}/", methods={"DELETE"})
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when it not found"
     *   }
     * )
     *
     * @param int $id the user id
     *
     * @return View
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeUsersAction($id): View
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return new View('User not found.', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getManager();
        $em->remove($user);
        $em->flush();

        return new View('Successfully removed.', Response::HTTP_OK);
    }
}