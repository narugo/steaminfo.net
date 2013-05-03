<?php
/** @Entity */
class DotaHero
{

    /** @Id @Column(type="integer") */
    protected $id;
    /** @Column(type="string") */
    protected $name;
    /** @Column(type="string", nullable=TRUE) */
    protected $display_name;


    /**
     * @OneToMany(targetEntity="DotaMatchPlayer", mappedBy="hero")
     * @var DotaMatchPlayer[]
     **/
    protected $players = null;

    public function addPlayers($players)
    {
        $this->players[] = $players;
    }

    public function getPlayers()
    {
        return $this->players;
    }
    public function getDisplayName()
    {
        return $this->display_name;
    }

    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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