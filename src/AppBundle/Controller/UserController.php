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
     * @param        $data
     * @param int    $code
     * @param array  $headers
     *
     * @return \FOS\RestBundle\View\View
     */
    private function createView($data, int $code, array $headers = []): View{

        return new View($data, $code, $headers);

    }


    /**
     * List of all users.
     *
     * @Annotations\Route("api/users/", methods={"GET"}, name="get_users")
     *
     * @ApiDoc(
     *      section="User",
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
     *   section="User",
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
            return $this->createView('User not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->createView($user, Response::HTTP_OK);

    }

    /**
     * Creates a new user from the submitted data and return that object.
     *
     * @Annotations\Route("api/user/", methods={"POST"})
     *
     * @ApiDoc(
     *   section="User",
     *   resource = true,
     *   output = "AppBundle\Entity\User",
     *   statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned when there are errors",
     *          500 = "Returned when there are errors"
     *   },
     *   parameters = {
     *          { "name" = "username", "dataType" = "string" },
     *          { "name" = "email", "dataType" = "string" },
     *          { "name" = "firstname", "dataType" = "string" },
     *          { "name" = "lastname", "dataType" = "string" }
     *   }
     * )
     * @Annotations\View()
     *
     * @Annotations\RequestParam(name="username", description="Username", nullable=false, allowBlank=false)
     * @Annotations\RequestParam(name="email", description="E-mail", nullable=false, allowBlank=false)
     * @Annotations\RequestParam(name="firstname", description="Firstname", nullable=false, allowBlank=false)
     * @Annotations\RequestParam(name="lastname", description="Lastname", nullable=false, allowBlank=false)
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
            return $this->createView('User already exists.', Response::HTTP_NOT_ACCEPTABLE);
        }catch(NotNullConstraintViolationException $e){
            return $this->createView('Not fully submitted data.', Response::HTTP_NOT_ACCEPTABLE);
        }

        return $this->createView($user, Response::HTTP_OK);

    }

    /**
     * Update existing user from the submitted data and return that object.
     *
     * @Annotations\Route("api/user/{id}/", methods={"PUT"})
     *
     * @ApiDoc(
     *   section="User",
     *   resource = true,
     *   output = "AppBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when is not found",
     *     500 = "Returned when there are errors",
     *   },
     *   parameters = {
     *        { "name" = "username", "dataType" = "string" },
     *        { "name" = "email", "dataType" = "string" },
     *        { "name" = "firstname", "dataType" = "string" },
     *        { "name" = "lastname", "dataType" = "string" }
     *   }
     * )
     * @Annotations\View()
     *
     * @Annotations\RequestParam(name="username", description="Username", nullable=true, allowBlank=true)
     * @Annotations\RequestParam(name="email", description="E-mail", nullable=true, allowBlank=true)
     * @Annotations\RequestParam(name="firstname", description="Firstname", nullable=true, allowBlank=true)
     * @Annotations\RequestParam(name="lastname", description="Lastname", nullable=true, allowBlank=true)
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
            return $this->createView('User not found.', Response::HTTP_NOT_FOUND);
        }

        $user = UserManager::updateByParams($user, $paramFetcher);

        $em = $this->getManager();

        $em->persist($user);
        $em->flush();

        return $this->createView($user, Response::HTTP_OK);

    }

    /**
     * Removes a user.
     *
     * @Annotations\Route("api/user/{id}/", methods={"DELETE"})
     *
     * @ApiDoc(
     *   section="User",
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
            return $this->createView('User not found.', Response::HTTP_NOT_FOUND);
        }

        $em = $this->getManager();
        $em->remove($user);
        $em->flush();

        return $this->createView('Successfully removed.', Response::HTTP_OK);
    }
}