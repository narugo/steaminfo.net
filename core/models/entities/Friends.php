<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="friends")
 */
class Friends
{
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_1", referencedColumnName="id")
     * @var User
     */
    protected $user;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_2", referencedColumnName="id")
     * @var User
     */
    protected $friend;
    /**
     * @Column(type="datetime", nullable=TRUE)
     * @var \DateTime
     */
    protected $since;

    public function getFriend()
    {
        return $this->friend;
    }

    public function setFriend($friend)
    {
        $this->friend = $friend;
    }

    public function getSince()
    {
        return $this->since;
    }

    public function setSince($since)
    {
        $this->since = $since;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

}