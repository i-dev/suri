<?php

namespace Idev\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/** @ORM\Entity @ORM\Table(name="users") */
class User implements UserInterface {
	
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	private $id;
	
	/** @ORM\Column(type="string", length=255) */
	private $lastname;
	
	/** @ORM\Column(type="string", length=255) */
	private $firstname;
	
	/** @ORM\Column(type="string", length=255) */
	private $email;
	
	/** @ORM\Column(type="string", length=255, unique=true) @Gedmo\Slug(fields={"firstname", "lastname"}, updatable=true, separator=".") */
	private $login;
	
	/** @ORM\Column(type="string", length=255) */
	private $password;
	
	private $salt = null;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Idev\Entity\UserGroup")
	 * @ORM\JoinTable(name="users_in_groups",
	 *      joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="role")}
	 *      )
	 */
	private $groups;
	
	
	public function __construct() {
		$this->groups = new ArrayCollection();
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setLogin($login) {
		$this->login = $login;
	}
	public function getLogin() {
		return $this->login;
	}
	
	public function getUsername() {
		return $this->getLogin();
	}
	
	public function setPassword($password) {
		$this->password = $password;
	}
	public function getPassword() {
		return $this->password;
	}
	
	public function setLastname($lastname) {
		$this->lastname = strtoupper($lastname);
	}
	public function getLastname() {
		return $this->lastname;
	}
	
	public function setFirstname($firstname) {
		$this->firstname = ucfirst($firstname);
	}
	public function getFirstname() {
		return $this->firstname;
	}
	
	public function getFullname() {
		return $this->getFirstname().' '.$this->getLastname();
	}
	
	public function setEmail($email) {
		$this->email = $email;
	}
	public function getEmail() {
		return $this->email;
	}
	
	public function addRole($role) {
		$this->groups[] = $role;
	}
	public function getRoles() {
		$roles = array();
    	foreach ($this->groups as $role) {
        	$roles[] = $role->getRole();
    	}

    	return $roles;
	}
	
	public function getGroups() {
	    return $this->groups;
	}
	
	public function getSalt() {
		return null;
	}
	
	public function eraseCredentials() {
		
	}
	
	public function generatePassword($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
	    // Length of character list
	    $chars_length = (strlen($chars) - 1);
	
	    // Start our string
	    $string = $chars{rand(0, $chars_length)};
	
	    // Generate random string
	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
	        // Grab a random character from our list
	        $r = $chars{rand(0, $chars_length)};
	
	        // Make sure the same two characters don't appear next to each other
	        if ($r != $string{$i - 1}) $string .=  $r;
	    }
	
	    // Return the string
	    $this->password = $string;
	    return $string;
	}
	
	static public function loadValidatorMetadata(ClassMetadata $metadata) {
	    $metadata->addPropertyConstraint('lastname', new Assert\NotBlank());
	    $metadata->addPropertyConstraint('firstname', new Assert\NotBlank());
	    $metadata->addPropertyConstraint('email', new Assert\NotBlank());
	    $metadata->addPropertyConstraint('email', new Assert\Email());
	}
	
	
}