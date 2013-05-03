<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="`user`")
 */
class User
{

    /** @Id @Column(type="bigint") */
    protected $id;
    /** @Column(type="datetime", nullable=TRUE) */
    protected $creation_time;
    /** @Column(type="string") */
    protected $nickname;
    /** @Column(type="string", nullable=TRUE) */
    protected $real_name;
    /** @Column(type="string") */
    protected $avatar_url;
    /** @Column(type="bigint", nullable=TRUE) */
    protected $primary_group_id;

    /*
     * Current status
     */
    /** @Column(type="smallint") */
    protected $status;
    /** @Column(type="datetime", nullable=TRUE) */
    protected $last_login_time;
    /** @Column(type="integer", nullable=TRUE) */
    protected $current_app_id;
    /** @Column(type="string", nullable=TRUE) */
    protected $current_app_name;
    /** @Column(type="string", nullable=TRUE) */
    protected $current_server_ip;

    /*
     * Bans
     */
    /** @Column(type="boolean", nullable=TRUE) */
    protected $is_vac_banned;
    /** @Column(type="boolean", nullable=TRUE) */
    protected $is_community_banned;
    /** @Column(type="string", nullable=TRUE) */
    protected $economy_ban_state;

    /*
     * Location
     */
    /** @Column(type="string", nullable=TRUE) */
    protected $location_city_id;
    /** @Column(type="string", nullable=TRUE) */
    protected $location_country_code;
    /** @Column(type="string", nullable=TRUE) */
    protected $location_state_code;

    /*
     * Other
     */
    /** @Column(type="string", nullable=TRUE) */
    protected $tag;
    /**
     * @OneToMany(targetEntity="AppOwner", mappedBy="user")
     * @var AppOwner[]
     **/
    protected $applications = null;
    /**
     * @OneToMany(targetEntity="DotaMatchPlayer", mappedBy="player")
     * @var DotaMatchPlayer[]
     **/
    protected $dota_matches = null;
    /**
     * @ManyToMany(targetEntity="User", mappedBy="users_friends")
     */
    private $friends_with_user;
    /**
     * @ManyToMany(targetEntity="User", inversedBy="friends_with_user")
     * @JoinTable(name="friends",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    private $users_friends;

    public function __construct()
    {
        $this->friends_with_user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users_friends = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param $dota_matches
     */
    public function addDotaMatches($dota_matches)
    {
        $this->dota_matches[] = $dota_matches;
    }

    /**
     * @return null|DotaMatchPlayer[]
     */
    public function getDotaMatches()
    {
        return $this->dota_matches;
    }

    /**
     * @return mixed
     */
    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    /**
     * @param $avatar_url
     */
    public function setAvatarUrl($avatar_url)
    {
        $this->avatar_url = $avatar_url;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creation_time;
    }

    /**
     * @param \DateTime $creation_time
     */
    public function setCreationTime($creation_time)
    {
        $this->creation_time = $creation_time;
    }

    /**
     * @return mixed
     */
    public function getCurrentAppId()
    {
        return $this->current_app_id;
    }

    /**
     * @param $current_game_id
     */
    public function setCurrentAppId($current_game_id)
    {
        $this->current_app_id = $current_game_id;
    }

    /**
     * @return string
     */
    public function getCurrentAppName()
    {
        return $this->current_app_name;
    }

    /**
     * @param string $current_game_name
     */
    public function setCurrentAppName($current_game_name)
    {
        $this->current_app_name = $current_game_name;
    }

    /**
     * @return mixed
     */
    public function getCurrentServerIp()
    {
        return $this->current_server_ip;
    }

    /**
     * @param $current_game_server_ip
     */
    public function setCurrentServerIp($current_game_server_ip)
    {
        $this->current_server_ip = $current_game_server_ip;
    }

    /**
     * @return mixed
     */
    public function getEconomyBanState()
    {
        return $this->economy_ban_state;
    }

    /**
     * @param $economy_ban_state
     */
    public function setEconomyBanState($economy_ban_state)
    {
        $this->economy_ban_state = $economy_ban_state;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function getIsCommunityBanned()
    {
        return $this->is_community_banned;
    }

    /**
     * @param boolean $is_community_banned
     */
    public function setCommunityBanState($is_community_banned)
    {
        $this->is_community_banned = $is_community_banned;
    }

    /**
     * @return boolean
     */
    public function getIsVacBanned()
    {
        return $this->is_vac_banned;
    }

    /**
     * @param boolean $is_vac_banned
     */
    public function setVacBanState($is_vac_banned)
    {
        $this->is_vac_banned = $is_vac_banned;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginTime()
    {
        return $this->last_login_time;
    }

    /**
     * @param \DateTime $last_login_time
     */
    public function setLastLoginTime($last_login_time)
    {
        $this->last_login_time = $last_login_time;
    }

    /**
     * @return mixed
     */
    public function getLocationCityId()
    {
        return $this->location_city_id;
    }

    /**
     * @param $location_city_id
     */
    public function setLocationCityId($location_city_id)
    {
        $this->location_city_id = $location_city_id;
    }

    /**
     * @return mixed
     */
    public function getLocationCountryCode()
    {
        return $this->location_country_code;
    }

    /**
     * @param $location_country_code
     */
    public function setLocationCountryCode($location_country_code)
    {
        $this->location_country_code = $location_country_code;
    }

    /**
     * @return mixed
     */
    public function getLocationStateCode()
    {
        return $this->location_state_code;
    }

    /**
     * @param $location_state_code
     */
    public function setLocationStateCode($location_state_code)
    {
        $this->location_state_code = $location_state_code;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getPrimaryGroupId()
    {
        return $this->primary_group_id;
    }

    /**
     * @param $primary_group_id
     */
    public function setPrimaryGroupId($primary_group_id)
    {
        $this->primary_group_id = $primary_group_id;
    }

    /**
     * @return string
     */
    public function getRealName()
    {
        return $this->real_name;
    }

    /**
     * @param string $real_name
     */
    public function setRealName($real_name)
    {
        $this->real_name = $real_name;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        switch ($this->status) {
            case '1':
                return 'Online';
            case '2':
                return 'Busy';
            case '3':
                return 'Away';
            case '4':
                return 'Snooze';
            case '5':
                return 'Looking to trade';
            case '6':
                return 'Looking to play';
            case '0':
            default:
                return 'Offline';
        }
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

}