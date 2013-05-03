<?php
/** @Entity */
class User
{

    /** @Id @Column(type="bigint") */
    protected $id;
    /** @Column(type="datetime") */
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
    /** @Column(type="datetime") */
    protected $last_login_time;
    /** @Column(type="integer", nullable=TRUE) */
    protected $current_game_id;
    /** @Column(type="string", nullable=TRUE) */
    protected $current_game_name;
    /** @Column(type="string", nullable=TRUE) */
    protected $current_game_server_ip;

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
     * Non-steam
     */
    /** @Column(type="string", nullable=TRUE) */
    protected $tag;
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

    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl($avatar_url)
    {
        $this->avatar_url = $avatar_url;
    }

    public function getCreationTime()
    {
        return $this->creation_time;
    }

    public function setCreationTime($creation_time)
    {
        $this->creation_time = $creation_time;
    }

    public function getCurrentGameId()
    {
        return $this->current_game_id;
    }

    public function setCurrentGameId($current_game_id)
    {
        $this->current_game_id = $current_game_id;
    }

    public function getCurrentGameName()
    {
        return $this->current_game_name;
    }

    public function setCurrentGameName($current_game_name)
    {
        $this->current_game_name = $current_game_name;
    }

    public function getCurrentGameServerIp()
    {
        return $this->current_game_server_ip;
    }

    public function setCurrentGameServerIp($current_game_server_ip)
    {
        $this->current_game_server_ip = $current_game_server_ip;
    }

    public function getEconomyBanState()
    {
        return $this->economy_ban_state;
    }

    public function setEconomyBanState($economy_ban_state)
    {
        $this->economy_ban_state = $economy_ban_state;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIsCommunityBanned()
    {
        return $this->is_community_banned;
    }

    public function setIsCommunityBanned($is_community_banned)
    {
        $this->is_community_banned = $is_community_banned;
    }

    public function getIsVacBanned()
    {
        return $this->is_vac_banned;
    }

    public function setIsVacBanned($is_vac_banned)
    {
        $this->is_vac_banned = $is_vac_banned;
    }

    public function getLastLoginTime()
    {
        return $this->last_login_time;
    }

    public function setLastLoginTime($last_login_time)
    {
        $this->last_login_time = $last_login_time;
    }

    public function getLocationCityId()
    {
        return $this->location_city_id;
    }

    public function setLocationCityId($location_city_id)
    {
        $this->location_city_id = $location_city_id;
    }

    public function getLocationCountryCode()
    {
        return $this->location_country_code;
    }

    public function setLocationCountryCode($location_country_code)
    {
        $this->location_country_code = $location_country_code;
    }

    public function getLocationStateCode()
    {
        return $this->location_state_code;
    }

    public function setLocationStateCode($location_state_code)
    {
        $this->location_state_code = $location_state_code;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    public function getPrimaryGroupId()
    {
        return $this->primary_group_id;
    }

    public function setPrimaryGroupId($primary_group_id)
    {
        $this->primary_group_id = $primary_group_id;
    }

    public function getRealName()
    {
        return $this->real_name;
    }

    public function setRealName($real_name)
    {
        $this->real_name = $real_name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

}