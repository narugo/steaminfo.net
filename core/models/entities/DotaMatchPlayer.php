<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="dota_match_player")
 */
class DotaMatchPlayer
{

    /**
     * @Id
     * @ManyToOne(targetEntity="DotaMatch", inversedBy="players")
     * @var DotaMatch
     */
    protected $match;
    /**
     * @Id
     * @ManyToOne(targetEntity="DotaHero", inversedBy="players")
     * @var DotaHero
     */
    protected $hero;
    /**
     * @Id
     * @Column(type="smallint")
     */
    protected $slot;
    /**
     * @ManyToOne(targetEntity="User", inversedBy="dota_matches")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @var User
     */
    protected $player;

    /*
     * Stats
     */
    /** @Column(type="smallint") */
    protected $kills;
    /** @Column(type="smallint") */
    protected $deaths;
    /** @Column(type="smallint") */
    protected $assists;
    /** @Column(type="smallint") */
    protected $leaver_status;
    /** @Column(type="smallint") */
    protected $gold;
    /** @Column(type="smallint") */
    protected $last_hits;
    /** @Column(type="smallint") */
    protected $denies;
    /** @Column(type="smallint") */
    protected $gold_per_min;
    /** @Column(type="smallint") */
    protected $xp_per_min;
    /** @Column(type="smallint") */
    protected $gold_spent;
    /** @Column(type="integer") */
    protected $hero_damage;
    /** @Column(type="integer") */
    protected $tower_damage;
    /** @Column(type="integer") */
    protected $hero_healing;
    /** @Column(type="smallint") */
    protected $level;

    /*
     * Items
     */
    /** @Column(type="integer") */
    protected $item_0;
    /** @Column(type="integer") */
    protected $item_1;
    /** @Column(type="integer") */
    protected $item_2;
    /** @Column(type="integer") */
    protected $item_3;
    /** @Column(type="integer") */
    protected $item_4;
    /** @Column(type="integer") */
    protected $item_5;

    public function getAssists()
    {
        return $this->assists;
    }

    public function setAssists($assists)
    {
        $this->assists = $assists;
    }

    public function getDeaths()
    {
        return $this->deaths;
    }

    public function setDeaths($deaths)
    {
        $this->deaths = $deaths;
    }

    public function getDenies()
    {
        return $this->denies;
    }

    public function setDenies($denies)
    {
        $this->denies = $denies;
    }

    public function getGold()
    {
        return $this->gold;
    }

    public function setGold($gold)
    {
        $this->gold = $gold;
    }

    public function getGoldPerMin()
    {
        return $this->gold_per_min;
    }

    public function setGoldPerMin($gold_per_min)
    {
        $this->gold_per_min = $gold_per_min;
    }

    public function getGoldSpent()
    {
        return $this->gold_spent;
    }

    public function setGoldSpent($gold_spent)
    {
        $this->gold_spent = $gold_spent;
    }

    public function getHero()
    {
        return $this->hero;
    }

    public function setHero($hero)
    {
        $this->hero = $hero;
    }

    public function getHeroDamage()
    {
        return $this->hero_damage;
    }

    public function setHeroDamage($hero_damage)
    {
        $this->hero_damage = $hero_damage;
    }

    public function getHeroHealing()
    {
        return $this->hero_healing;
    }

    public function setHeroHealing($hero_healing)
    {
        $this->hero_healing = $hero_healing;
    }

    public function getItem0()
    {
        return $this->item_0;
    }

    public function setItem0($item_0)
    {
        $this->item_0 = $item_0;
    }

    public function getItem1()
    {
        return $this->item_1;
    }

    public function setItem1($item_1)
    {
        $this->item_1 = $item_1;
    }

    public function getItem2()
    {
        return $this->item_2;
    }

    public function setItem2($item_2)
    {
        $this->item_2 = $item_2;
    }

    public function getItem3()
    {
        return $this->item_3;
    }

    public function setItem3($item_3)
    {
        $this->item_3 = $item_3;
    }

    public function getItem4()
    {
        return $this->item_4;
    }

    public function setItem4($item_4)
    {
        $this->item_4 = $item_4;
    }

    public function getItem5()
    {
        return $this->item_5;
    }

    public function setItem5($item_5)
    {
        $this->item_5 = $item_5;
    }

    public function getKills()
    {
        return $this->kills;
    }

    public function setKills($kills)
    {
        $this->kills = $kills;
    }

    public function getLastHits()
    {
        return $this->last_hits;
    }

    public function setLastHits($last_hits)
    {
        $this->last_hits = $last_hits;
    }

    public function getLeaverStatus()
    {
        return $this->leaver_status;
    }

    public function setLeaverStatus($leaver_status)
    {
        $this->leaver_status = $leaver_status;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getMatch()
    {
        return $this->match;
    }

    public function setMatch($match)
    {
        $this->match = $match;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }

    public function getSlot()
    {
        return $this->slot;
    }

    public function setSlot($slot)
    {
        $this->slot = $slot;
    }

    public function getTowerDamage()
    {
        return $this->tower_damage;
    }

    public function setTowerDamage($tower_damage)
    {
        $this->tower_damage = $tower_damage;
    }

    public function getXpPerMin()
    {
        return $this->xp_per_min;
    }

    public function setXpPerMin($xp_per_min)
    {
        $this->xp_per_min = $xp_per_min;
    }

}