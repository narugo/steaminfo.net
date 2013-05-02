<?php
/** @Entity */
class AppOwners
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Application")
     * @JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /*
     * Usage statistics
     */
    /** @Column(type="integer") */
    protected $used_in_last_2_weeks = 0;
    /** @Column(type="integer") */
    protected $used_total = 0;

}