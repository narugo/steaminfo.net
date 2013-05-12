<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="api_interface")
 */
class ApiInterface
{

    /**
     * @Id
     * @Column(type="string")
     * @var string
     */
    protected $name;
    /**
     * @OneToMany(targetEntity="ApiMethod", mappedBy="interface", cascade={"ALL"}, indexBy="name")
     */
    private $methods;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function addMethods($name, $version, $http_method)
    {
        $this->methods[$name] = new ApiMethod($name, $this, $version, $http_method);
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}