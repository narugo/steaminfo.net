<?php
/** @Entity */
class Application
{

    /** @Id @Column(type="bigint") */
    protected $id;
    /** @Column(type="string", nullable=TRUE) */
    protected $name;
    /** @Column(type="string", nullable=TRUE) */
    protected $header_image_url;
    /** @Column(type="text", nullable=TRUE) */
    protected $description;
    /** @Column(type="string", nullable=TRUE) */
    protected $type;
    /** @Column(type="string", nullable=TRUE) */
    protected $website;
    /** @Column(type="date", nullable=TRUE) */
    protected $release_date;
    /** @Column(type="string", nullable=TRUE) */
    protected $legal_notice;
    /** @Column(type="integer", nullable=TRUE) */
    protected $recommendations;

    /*
     * Platforms
     */
    /** @Column(type="time", nullable=TRUE) */
    protected $is_linux;
    /** @Column(type="time", nullable=TRUE) */
    protected $is_mac;
    /** @Column(type="time", nullable=TRUE) */
    protected $is_win;

}