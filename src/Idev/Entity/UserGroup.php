<?php

namespace Idev\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;

use Doctrine\ORM\Mapping AS ORM;

/** @ORM\Entity @ORM\Table(name="users_groups") */
class UserGroup implements RoleInterface {
	
	/** @ORM\Column(type="string", length=255) @ORM\Id @ORM\GeneratedValue(strategy="NONE") */
	private $role;
	
	/** @ORM\Column(type="string", length=255) */
	private $name;
	
	public function getRole() {
		return $this->role;
	}
	
	public function setRole($role) {
		$this->role = $role;
	}
	
	public function getName() {
	    return $this->name;
	}
	public function setName($name) {
	    $this->name = $name;
	}

}