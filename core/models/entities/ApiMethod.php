<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="api_method")
 */
class ApiMethod
{

    /**
     * @Id
     * @Column(type="string")
     */
    protected $name;
    /**
     * @Id
     * @ManyToOne(targetEntity="ApiInterface", inversedBy="methods")
     * @JoinColumn(name="interface_name", referencedColumnName="name")
     */
    protected $interface;
    /**
     * @Id
     * @Column(type="integer")
     */
    protected $version;
    /**
     * @Id
     * @Column(type="string")
     */
    protected $http_method;

    /**
     * @OneToMany(targetEntity="ApiMethodParameter", mappedBy="method", cascade={"ALL"}, indexBy="name")
     */
    // private $parameters;

    /**
     * @param string $name
     * @param ApiInterface $interface
     * @param integer $version
     * @param string $http_method
     */
    public function __construct($name, $interface, $version, $http_method)
    {
        $this->name = $name;
        $this->interface = $interface;
        $this->version = $version;
        $this->http_method = $http_method;
    }

    public function addParameters($name)
    {
        $this->methods[$name] = new ApiMethodParameter(
            $name,
            $this->getName(),
            $this->getVersion(),
            $this->interface->getName()
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getHttpMethod()
    {
        return $this->http_method;
    }

    public function setHttpMethod($http_method)
    {
        $this->http_method = $http_method;
    }

    /**
     * @return \SteamInfo\Models\Entities\ApiInterface
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @param \SteamInfo\Models\Entities\ApiInterface $interface
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}