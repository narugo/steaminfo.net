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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getHeaderImageUrl()
    {
        return $this->header_image_url;
    }

    public function setHeaderImageUrl($header_image_url)
    {
        $this->header_image_url = $header_image_url;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIsLinux()
    {
        return $this->is_linux;
    }

    public function setIsLinux($is_linux)
    {
        $this->is_linux = $is_linux;
    }

    public function getIsMac()
    {
        return $this->is_mac;
    }

    public function setIsMac($is_mac)
    {
        $this->is_mac = $is_mac;
    }

    public function getIsWin()
    {
        return $this->is_win;
    }

    public function setIsWin($is_win)
    {
        $this->is_win = $is_win;
    }

    public function getLegalNotice()
    {
        return $this->legal_notice;
    }

    public function setLegalNotice($legal_notice)
    {
        $this->legal_notice = $legal_notice;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRecommendations()
    {
        return $this->recommendations;
    }

    public function setRecommendations($recommendations)
    {
        $this->recommendations = $recommendations;
    }

    public function getReleaseDate()
    {
        return $this->release_date;
    }

    public function setReleaseDate($release_date)
    {
        $this->release_date = $release_date;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }

}