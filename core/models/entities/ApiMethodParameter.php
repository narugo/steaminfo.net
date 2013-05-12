<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="api_method_parameter")
 */
class ApiMethodParameter
{
    /**
     * @Id
     * @Column(type="string")
     */
    protected $name;
    /**
     * @Id
     * @Column(type="string")
     */
    protected $method;
    /**
     * @Id
     * @Column(type="integer")
     */
    protected $version;
    /**
     * @Id
     * @Column(type="string")
     */
    protected $interface;
    /** @Column(type="string") */
    protected $type;
    /** @Column(type="boolean") */
    protected $optional;
    /** @Column(type="text", nullable=TRUE) */
    protected $description;

    /**
     * @param string $name
     * @param string $method
     * @param integer $version
     * @param string $interface
     */
    public function __construct($name, $method, $version, $interface)
    {
        $this->name = $name;
        $this->method = $method;
        $this->version = $version;
        $this->interface = $interface;
    }

    public function getInterface()
    {
        return $this->interface;
    }

    public function setInterface($interface)
    {
        $this->interface = $interface;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \SteamInfo\Models\Entities\ApiMethod
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param \SteamInfo\Models\Entities\ApiMethod $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getOptional()
    {
        return $this->optional;
    }

    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

}