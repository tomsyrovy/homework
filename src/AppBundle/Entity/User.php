<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @UniqueEntity("username", message="Username is already used.")
 * @UniqueEntity("email", message="E-mail is already used.")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Username cannot be blank.")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Email cannot be blank.")
     * @Assert\Email(message="Email is not in valid format.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     *
     * @Assert\NotBlank(message="Firstname cannot be blank.")
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     *
     * @Assert\NotBlank(message="Lastname cannot be blank.")
     */
    private $lastname;

    /**
     * @return int
     */
    public function getId(): int{
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string{
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername( string $username ): void{
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string{
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail( string $email ): void{
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname(): string{
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname( string $firstname ): void{
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string{
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname( string $lastname ): void{
        $this->lastname = $lastname;
    }
}

