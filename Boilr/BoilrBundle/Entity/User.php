<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Security\Core\User\UserInterface,
    Gedmo\Mapping\Annotation as Gedmo;
use Boilr\BoilrBundle\Validator\Constraints as MyAssert;

/**
 * Boilr\BoilrBundle\Entity\User
 *
 * @ORM\Table(name="users", uniqueConstraints={
 * @ORM\UniqueConstraint(name="user_uniq_idx", columns={"name", "surname", "login"})})
 * @ORM\Entity
 * @UniqueEntity("login")
 */
class User implements UserInterface
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=30, nullable=false)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string $surname
     *
     * @ORM\Column(type="string", length=30, nullable=false)
     * @Assert\NotBlank
     */
    protected $surname;

    /**
     * @var string $login
     *
     * @ORM\Column(type="string", length=30, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    protected $login;

    /**
     * @var string $password
     *
     * @ORM\Column(type="string", length=40, nullable=false)
     * @Assert\NotBlank
     * @MyAssert\EqualsField(field="name", negate=true, message="La password non può essere uguale al nome")
     */
    protected $password;

    /**
     * @var $isActive
     *
     * @ORM\Column(name="active", type="boolean")
     * @Assert\NotBlank
     * @Assert\Type(type="bool")
     */
    protected $isActive;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @var Group
     *
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="users_groups",
     *                joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *                inverseJoinColumns={
     *                      @ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    protected $groups;

    public function equals(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return false;
        }

        return ($this->getLogin() == $user->getUsername());
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        $roles = array();

        foreach ($this->getGroups() as $group) {
            /* @var $group \Boilr\BoilrBundle\Entity\Group */
            $roles[] = $group->getRole();
        }

        return $roles;
    }

    /**
     * Returns true if the user has given role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getLogin();
    }

    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set password (parameter is clear-text)
     *
     * @param string $password
     */
    public function setCleartextPassword($password)
    {
        $this->password = sha1($password);
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param Boilr\BoilrBundle\Entity\Group $groups
     */
    public function addGroup(\Boilr\BoilrBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Returns a description of user privs
     *
     * @return string
     */
    public function getGroupsDescr()
    {
        $descr = array();
        foreach ($this->getGroups() as $group) {
            $descr[] = $group->getName();
        }

        return implode(', ', $descr);
    }

    public function getFullName()
    {
        return $this->name . " " . $this->surname;
    }

    /**
     * rue(message = "The password cannot match your first name")

      public function isPasswordLegal()
      {
      $tokens = array(strtolower($this->name), strtolower($this->surname));

      return (!in_array($this->password, $tokens));
      }
     */
}