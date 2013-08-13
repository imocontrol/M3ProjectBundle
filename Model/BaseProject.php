<?php
namespace IMOControl\M3\ProjectBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use IMOControl\M3\ProjectBundle\Model\Interfaces\ProjectInterface;
use IMOControl\M3\CustomerBundle\Model\Interfaces\CustomerInterface as Customer;
use IMOControl\M3\CustomerBundle\Model\Interfaces\ContactInterface as IContact;
use IMOControl\M3\InvoiceBundle\Model\Interfaces\InvoiceInterface as Invoice;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseProject implements ProjectInterface
{
        
    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=15, unique=true, nullable=true)
     */
    protected $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="project_address_street", type="string", length=255)
     */
    protected $project_address_street;
    
    /**
     * @var string
     *
     * @ORM\Column(name="project_address_plz", type="integer", length=5)
     */
    protected $project_address_postalcode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="project_address_city", type="string", length=150)
     */
    protected $project_address_city;

    /**
     * @var string
     *
     * @ORM\Column(name="project_address_country", type="string", length=3)
     */
    protected $project_address_country;
    
    /**
     * @var Customer $customer
     *
     */
    protected $customer;
    
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updated_at;


    /**
     * @var string
     *
     * @ORM\Column(name="project_path", type="string", length=255, nullable=true)
     */
    protected $project_path;
    
    
    public function __toString() {
        return "" . $this->getAlias() . "-" . $this->getName();
    }
    
    public function __construct() {
        //$this->required_values = new ArrayCollection();
        //$this->invoices = new ArrayCollection();
        $this->certificates = new ArrayCollection();
    }
    
    public function getProjectAddress() {
        return sprintf("%s, %s %s - %s", $this->getProjectAddressStreet(), $this->getProjectAddressPlz(), $this->getProjectAddressCity(), $this->getProjectAddressCountry());
    }
    
    public function getInternalProjectName() {
        return sprintf("%s - [%s]", $this->getAlias(), $this->getName());
    }
	
    
    /**
     * Set alias
     *
     * @param string $alias
     * @return Project
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    
        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * Set customer
     *
     * @param CustomerInterface $customer
     * @return Project
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set contacts
     *
     * @param ContactInterface $contacts
     * @return Project
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    
        return $this;
    }

    /**
     * Get contacts
     *
     * @return ContactInterface 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set project_path
     *
     * @param string $projectPath
     * @return Project
     */
    public function setProjectPath($projectPath)
    {
        $this->project_path = $projectPath;
    
        return $this;
    }

    /**
     * Get project_path
     *
     * @return string 
     */
    public function getProjectPath($absolute=false)
    {
        if ($absolute) {
            return Project::ROOTPATH . $this->project_path;
        }
        
        return $this->project_path;
    }

    /**
     * Set project_address_street
     *
     * @param string $projectAddressStreet
     * @return Project
     */
    public function setProjectAddressStreet($projectAddressStreet)
    {
        $this->project_address_street = $projectAddressStreet;
    
        return $this;
    }

    /**
     * Get project_address_street
     *
     * @return string 
     */
    public function getProjectAddressStreet()
    {
        return $this->project_address_street;
    }

    /**
     * Set project_address_plz
     *
     * @param integer $projectAddressPlz
     * @return Project
     */
    public function setProjectAddressPostalcode($projectAddressPlz)
    {
        $this->project_address_postalcode = $projectAddressPlz;
    
        return $this;
    }

    /**
     * Get project_address_plz
     *
     * @return integer 
     */
    public function getProjectAddressPostalcode()
    {
        return $this->project_address_postalcode;
    }

    /**
     * Set project_address_city
     *
     * @param string $projectAddressCity
     * @return Project
     */
    public function setProjectAddressCity($projectAddressCity)
    {
        $this->project_address_city = $projectAddressCity;
    
        return $this;
    }

    /**
     * Get project_address_city
     *
     * @return string 
     */
    public function getProjectAddressCity()
    {
        return $this->project_address_city;
    }

    /**
     * Set project_address_country
     *
     * @param string $projectAddressCountry
     * @return Project
     */
    public function setProjectAddressCountry($projectAddressCountry)
    {
        $this->project_address_country = $projectAddressCountry;
    
        return $this;
    }

    /**
     * Get project_address_country
     *
     * @return string 
     */
    public function getProjectAddressCountry()
    {
        return $this->project_address_country;
    }

    /**
     * Add invoices
     *
     * @param Invoice $invoices
     * @return Project
     */
    public function addInvoice(Invoice $invoices)
    {
        $this->invoices[] = $invoices;
    
        return $this;
    }

    /**
     * Remove invoices
     *
     * @param Invoice $invoices
     */
    public function removeInvoice(Invoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

}
