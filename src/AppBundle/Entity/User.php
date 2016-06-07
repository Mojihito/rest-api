<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use AppBundle\Serializer\Annotation as AppSerializer;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package AppBundle\Entity
 * @author Rafał Lorenz <vardius@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"show", "update", "list", "elastica"})
     */
    protected $id;

    /**
     * @var string
     * @Assert\Email()
     * @Serializer\Groups({"show", "update", "elastica"})
     */
    protected $email;

    /**
     * @var array
     * @Serializer\Groups({"show", "update"})
     */
    protected $roles;

    /**
     * @var boolean
     * @Serializer\Groups({"show", "update"})
     */
    protected $enabled;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Serializer\Groups({"show", "update", "elastica"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Serializer\Groups({"show", "update", "elastica"})
     */
    protected $surname;

    /**
     * @var \DateTime $birth
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Assert\NotBlank()
     * @Serializer\Groups({"show", "update"})
     */
    protected $birth;

    /**
     * @var File
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\File", cascade={"remove"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     * @AppSerializer\Depth(2)
     * @Serializer\Groups({"show", "update", "elastica"})
     */
    protected $avatar;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"show", "update"})
     */
    protected $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"show", "update"})
     */
    protected $updated;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     * @return $this
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @param File $avatar
     * @return $this
     */
    public function setAvatar(File $avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return File
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return \DateTime
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * @param \DateTime $birth
     * @return User
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return string
     * @Serializer\Groups({"show", "update"})
     */
    public function getFullName()
    {
        if (!$this->name && !$this->surname) {
            return $this->getUsername();
        }

        return $this->name . ' ' . $this->surname;
    }
}
