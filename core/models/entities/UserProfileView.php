<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="user_profile_view_log")
 */
class UserProfileView
{
    /**
     * @Id
     * @ManyToOne(targetEntity="User", inversedBy="applications")
     * @var User
     */
    protected $user;
    /**
     * @Id
     * @Column(type="integer")
     * @var integer
     */
    protected $timestamp;

    public function __construct()
    {
        $this->timestamp = date_create()->getTimestamp();
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        $creation_time = date_create();
        date_timestamp_set($creation_time, $this->timestamp);
        return $creation_time;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp->getTimestamp();
    }

    /**
     * @return \SteamInfo\Models\Entities\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \SteamInfo\Models\Entities\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}