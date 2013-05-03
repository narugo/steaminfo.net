<?php
/** @Entity */
class DotaTeam
{

    /** @Id @Column(type="integer") */
    protected $id;
    /** @Column(type="string") */
    protected $name;
    /** @Column(type="string") */
    protected $tag;
    /** @Column(type="string") */
    protected $rating;
    /** @Column(type="string") */
    protected $logo;
    /** @Column(type="string") */
    protected $logo_sponsor;
    /** @Column(type="datetime") */
    protected $creation_time;
    /** @Column(type="string") */
    protected $country_code;
    /** @Column(type="string") */
    protected $url;
    /** @Column(type="string", nullable=TRUE) */
    protected $display_name;
    /** @Column(type="integer", nullable=TRUE) */
    protected $games_played_with_current_roster;

    /*
     * Players and admin
     */
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_1", referencedColumnName="id")
     */
    protected $player_1;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_2", referencedColumnName="id")
     */
    protected $player_2;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_3", referencedColumnName="id")
     */
    protected $player_3;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_4", referencedColumnName="id")
     */
    protected $player_4;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="admin_account", referencedColumnName="id")
     */
    protected $admin;

    public function getAdmin()
    {
        return $this->admin;
    }

    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    public function getCountryCode()
    {
        return $this->country_code;
    }

    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    public function getCreationTime()
    {
        return $this->creation_time;
    }

    public function setCreationTime($creation_time)
    {
        $this->creation_time = $creation_time;
    }

    public function getDisplayName()
    {
        return $this->display_name;
    }

    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    public function getGamesPlayedWithCurrentRoster()
    {
        return $this->games_played_with_current_roster;
    }

    public function setGamesPlayedWithCurrentRoster($games_played_with_current_roster)
    {
        $this->games_played_with_current_roster = $games_played_with_current_roster;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getLogoSponsor()
    {
        return $this->logo_sponsor;
    }

    public function setLogoSponsor($logo_sponsor)
    {
        $this->logo_sponsor = $logo_sponsor;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPlayer1()
    {
        return $this->player_1;
    }

    public function setPlayer1($player_1)
    {
        $this->player_1 = $player_1;
    }

    public function getPlayer2()
    {
        return $this->player_2;
    }

    public function setPlayer2($player_2)
    {
        $this->player_2 = $player_2;
    }

    public function getPlayer3()
    {
        return $this->player_3;
    }

    public function setPlayer3($player_3)
    {
        $this->player_3 = $player_3;
    }

    public function getPlayer4()
    {
        return $this->player_4;
    }

    public function setPlayer4($player_4)
    {
        $this->player_4 = $player_4;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }


}