<?php
/** @Entity */
class DotaLeague
{

    /** @Id @Column(type="integer") */
    protected $id;
    /** @Column(type="string") */
    protected $name;
    /** @Column(type="text", nullable=TRUE) */
    protected $description;
    /** @Column(type="string", nullable=TRUE) */
    protected $tournament_url;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getTournamentUrl()
    {
        return $this->tournament_url;
    }

    public function setTournamentUrl($tournament_url)
    {
        $this->tournament_url = $tournament_url;
    }

}