<?php
/** @Entity */
class User
{

    /** @Id @Column(type="bigint") */
    protected $id;
    /** @Column(type="time") */
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
    /** @Column(type="time") */
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
    /** @Column(type="string", nullable=TRUE) */
    protected $is_vac_banned;
    /** @Column(type="string", nullable=TRUE) */
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    protected function getCommunityId()
    {
        return $this->community_id;
    }

    protected function getSteamId()
    {
        $steam = new Locomotive(STEAM_API_KEY);
        return $steam->tools->users->communityIdToSteamId($this->community_id);
    }

    protected function getStatus()
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

    protected function getCurrentGameId()
    {
        return $this->current_game_id;
    }

    protected function isInGame()
    {
        if (isset($this->current_game_id)) return TRUE;
        else return FALSE;
    }

    protected function getCurrentAppStorePageURL()
    {
        if (isset($this->current_game_id)) {
            return 'http://store.steampowered.com/app/' . $this->current_game_id;
        }
        return NULL;
    }

    protected function getCurrentAppName()
    {
        return $this->current_game_name;
    }

    /**
     * @return null|string Returns connection URL if current server IP is set, NULL otherwise.
     */
    protected function getConnectionUrl()
    {
        if (isset($this->current_game_server_ip)) {
            return 'steam://connect/' . $this->current_game_server_ip;
        }
        return NULL;
    }

    protected function getCurrentGameServerIp()
    {
        return $this->current_game_server_ip;
    }

    protected function isCommunityBanned()
    {
        return $this->is_community_banned;
    }

    protected function isVacBanned()
    {
        return $this->is_vac_banned;
    }

    protected function getEconomyBanState()
    {
        return $this->economy_ban_state;
    }

    protected function getLastLoginTime($raw = FALSE)
    {
        if ($raw) return $this->last_login_time;
        else return date(DATE_RFC850, $this->last_login_time);
    }

    protected function getLastUpdateTime()
    {
        return $this->last_updated;
    }

    /**
     * @return null|string
     */
    protected function getLocation()
    {
        $result = NULL;
        if (isset($this->location_country_code)) {
            $result = $this->location_country_code;
            if (isset($this->location_state_code))
                $result .= ', ' . $this->location_state_code;
            if (isset($this->location_city_id))
                $result .= ', ' . $this->location_city_id;
        }
        return $result;
    }

    protected function getLocationCountryCode()
    {
        if (isset($this->location_country_code))
            return strtoupper($this->location_country_code);
        return $this->location_country_code;
    }

    protected function getNickname()
    {
        return (string)$this->nickname;
    }

    protected function getPrimaryGroupId()
    {
        return $this->primary_group_id;
    }

    protected function getRealName()
    {
        return $this->real_name;
    }

    protected function getTag()
    {
        return $this->tag;
    }

    protected function getBadgesHTML()
    {
        $steam = new Locomotive();
        return $steam->tools->users->getBadges($this->community_id);
    }

    protected function getCreationTime()
    {
        if (isset($this->creation_time))
            return date(DATE_RFC850, $this->creation_time);
        return NULL;
    }
}