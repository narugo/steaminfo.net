<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="app_owner")
 */
class AppOwner
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Application", inversedBy="users")
     * @var Application
     */
    protected $application;

    /**
     * @Id
     * @ManyToOne(targetEntity="User", inversedBy="applications")
     * @var User
     */
    protected $user;

    /*
     * Usage statistics
     */
    /** @Column(type="integer") */
    protected $used_in_last_2_weeks = 0;
    /** @Column(type="integer") */
    protected $used_total = 0;

    public function getApplication()
    {
        return $this->application;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getUsedInLast2Weeks()
    {
        return $this->used_in_last_2_weeks;
    }

    public function setUsedInLast2Weeks($used_in_last_2_weeks)
    {
        $this->used_in_last_2_weeks = $used_in_last_2_weeks;
    }

    public function getUsedTotal()
    {
        return $this->used_total;
    }

    public function setUsedTotal($used_total)
    {
        $this->used_total = $used_total;
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