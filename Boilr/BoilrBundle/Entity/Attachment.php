<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext,
    Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Boilr\BoilrBundle\Entity\Attachment
 *
 * @ORM\Entity
 * @ORM\Table(name="attachments")
 * @Vich\Uploadable
 */
class Attachment
{

    const TYPE_SYSTEM = 1;
    const TYPE_INTERVENTION = 2;

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
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @Assert\File(
     *     maxSize="3M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg", "application/pdf",
     *                "text/plain", "application/msword", "application/excel",
     *                 "application/vnd.openxmlformats", "application/vnd.ms-excel",
     *                 "application/zip"}
     * )
     * @Vich\UploadableField(mapping="attachment_file", fileNameProperty="name")
     *
     * @var File $document
     */
    protected $document;

    /**
     * @var type
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @Assert\Choice(choices = {"1", "2"})
     */
    protected $type;

    /**
     * @var owner
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $owner;

    /**
     * @var datetime $uploadDate
     *
     * @ORM\Column(name="upload_date", type="datetime", nullable=false)
     * @Assert\NotBlank
     */
    protected $uploadDate;

    /**
     * @var parentSystem
     *
     * @ORM\ManyToOne(targetEntity="System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=true)
     */
    protected $parentSystem;

    /**
     * @var parentIntervention
     *
     * @ORM\ManyToOne(targetEntity="MaintenanceIntervention")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=true)
     */
    protected $parentIntervention;


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
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set uploadDate
     *
     * @param datetime $uploadDate
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * Get uploadDate
     *
     * @return datetime
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set owner
     *
     * @param Boilr\BoilrBundle\Entity\User $owner
     */
    public function setOwner(\Boilr\BoilrBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return Boilr\BoilrBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set parentSystem
     *
     * @param Boilr\BoilrBundle\Entity\System $parentSystem
     */
    public function setParentSystem(\Boilr\BoilrBundle\Entity\System $parentSystem)
    {
        $this->parentSystem = $parentSystem;
    }

    /**
     * Get parentSystem
     *
     * @return Boilr\BoilrBundle\Entity\System
     */
    public function getParentSystem()
    {
        return $this->parentSystem;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument($document)
    {
        $this->document = $document;
    }


    /**
     * Set parentIntervention
     *
     * @param Boilr\BoilrBundle\Entity\MaintenanceIntervention $parentIntervention
     */
    public function setParentIntervention(\Boilr\BoilrBundle\Entity\MaintenanceIntervention $parentIntervention)
    {
        $this->parentIntervention = $parentIntervention;
    }

    /**
     * Get parentIntervention
     *
     * @return Boilr\BoilrBundle\Entity\MaintenanceIntervention
     */
    public function getParentIntervention()
    {
        return $this->parentIntervention;
    }

    public function getTypeDescr()
    {
        if ($this->type == self::TYPE_INTERVENTION) {
            return "Intervento";
        } else {
            return "Impianto";
        }
    }
}