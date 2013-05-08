<?php
namespace SteamInfo\Models\Entities;

define('DOTA_APP_ID', 570);

/**
 * @Entity
 * @Table(name="dota_team")
 */
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
    /** @Column(type="string", nullable=TRUE) */
    protected $logo;
    /** @Column(type="string", nullable=TRUE) */
    protected $logo_sponsor;
    /** @Column(type="datetime") */
    protected $creation_time;
    /** @Column(type="string", nullable=TRUE) */
    protected $country_code;
    /** @Column(type="string", nullable=TRUE) */
    protected $url;
    /** @Column(type="integer", nullable=TRUE) */
    protected $games_played_with_current_roster;

    /*
     * Players and admin
     */
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_0", referencedColumnName="id")
     */
    protected $player_0;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_1", referencedColumnName="id")
     */
    protected $player_1;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_2", referencedColumnName="id")
     */
    protected $player_2;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_3", referencedColumnName="id")
     */
    protected $player_3;
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="player_4", referencedColumnName="id")
     */
    protected $player_4;
    /**
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
        if (is_null($this->country_code)) return NULL;
        return strtoupper($this->country_code);
    }

    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creation_time;
    }

    public function setCreationTime($creation_time)
    {
        $this->creation_time = $creation_time;
    }

    /**
     * @return User
     */
    public function getPlayer0()
    {
        return $this->player_0;
    }

    public function setPlayer0($player_0)
    {
        $this->player_0 = $player_0;
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
        if (is_null($this->logo)) return NULL;
        $steam = new \Locomotive(STEAM_API_KEY);
        $response = $steam->ISteamRemoteStorage->GetUGCFileDetails($this->logo, DOTA_APP_ID);
        if (empty($response->data->filename) OR empty($response->data->url)) return NULL;
        $path = PATH_TO_ASSETS . 'img/dota/' . $response->data->filename . '.png';
        $fp = fopen($path, 'w');
        $ch = curl_init($response->data->url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $response->data->filename . '.png';
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

    /**
     * @return User
     */
    public function getPlayer1()
    {
        return $this->player_1;
    }

    public function setPlayer1($player_1)
    {
        $this->player_1 = $player_1;
    }

    /**
     * @return User
     */
    public function getPlayer2()
    {
        return $this->player_2;
    }

    public function setPlayer2($player_2)
    {
        $this->player_2 = $player_2;
    }

    /**
     * @return User
     */
    public function getPlayer3()
    {
        return $this->player_3;
    }

    public function setPlayer3($player_3)
    {
        $this->player_3 = $player_3;
    }

    /**
     * @return User
     */
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
        if (is_null(parse_url($url, PHP_URL_SCHEME))) {
            $url = "http://" . $url;
        }
        $this->url = $url;
    }


}