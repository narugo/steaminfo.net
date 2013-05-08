<?php
namespace SteamInfo\Models\Entities;
/**
 * @Entity
 * @Table(name="dota_match")
 */
class DotaMatch
{

    /** @Id @Column(type="integer") */
    protected $id;
    /** @Column(type="datetime") */
    protected $start_time;
    /** @Column(type="smallint") */
    protected $season;
    /** @Column(type="boolean") */
    protected $is_radiant_win;
    /** @Column(type="smallint") */
    protected $duration;
    /** @Column(type="string") */
    protected $cluster;
    /** @Column(type="smallint") */
    protected $first_blood_time;
    /** @Column(type="string") */
    protected $lobby_type;
    /** @Column(type="smallint") */
    protected $human_players;
    /** @Column(type="integer") */
    protected $positive_votes;
    /** @Column(type="integer") */
    protected $negative_votes;
    /** @Column(type="integer") */
    protected $game_mode;
    /** @Column(type="integer") */
    protected $match_seq_num;
    /**
     * @ManyToOne(targetEntity="DotaLeague", inversedBy="matches")
     * @JoinColumn(name="league_id", referencedColumnName="id", nullable=true)
     * @var DotaLeague
     */
    protected $league;
    /**
     * @OneToMany(targetEntity="DotaMatchPlayer", mappedBy="match")
     * @var DotaMatchPlayer[]
     **/
    protected $players = null;

    /*
     * Radiant
     */
    /**
     * @ManyToOne(targetEntity="DotaTeam")
     * @JoinColumn(name="radiant_team", referencedColumnName="id", nullable=true)
     * @var DotaTeam
     */
    protected $radiant_team;
    /** @Column(type="boolean", nullable=TRUE) */
    protected $radiant_team_complete;
    /** @Column(type="integer", nullable=TRUE) */
    protected $radiant_tower_status;
    /** @Column(type="integer", nullable=TRUE) */
    protected $radiant_barracks_status;

    /*
     * Dire
     */
    /**
     * @ManyToOne(targetEntity="DotaTeam")
     * @JoinColumn(name="dire_team", referencedColumnName="id", nullable=true)
     * @var DotaTeam
     */
    protected $dire_team;
    /** @Column(type="boolean", nullable=TRUE) */
    protected $dire_team_complete;
    /** @Column(type="integer", nullable=TRUE) */
    protected $dire_tower_status;
    /** @Column(type="integer", nullable=TRUE) */
    protected $dire_barracks_status;

    public function addPlayers($players)
    {
        $this->players[] = $players;
    }

    // Status

    public function getPlayers()
    {
        return $this->players;
    }

    public function getCluster()
    {
        return $this->cluster;
    }

    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }

    public function getDireBarracksStatus()
    {
        return $this->dire_barracks_status;
    }

    public function setDireBarracksStatus($dire_barracks_status)
    {
        $this->dire_barracks_status = $dire_barracks_status;
    }

    public function getMatchSeqNum()
    {
        return $this->match_seq_num;
    }

    public function setMatchSeqNum($match_seq_num)
    {
        $this->match_seq_num = $match_seq_num;
    }

    /**
     * @return DotaTeam
     */
    public function getDireTeam()
    {
        return $this->dire_team;
    }

    /**
     * @param DotaTeam $dire_team
     */
    public function setDireTeam($dire_team)
    {
        $this->dire_team = $dire_team;
    }

    public function getDireTeamComplete()
    {
        return $this->dire_team_complete;
    }

    public function setDireTeamComplete($dire_team_complete)
    {
        $this->dire_team_complete = $dire_team_complete;
    }

    public function getDireTowerStatus()
    {
        return $this->dire_tower_status;
    }

    public function setDireTowerStatus($dire_tower_status)
    {
        $this->dire_tower_status = $dire_tower_status;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    public function getFirstBloodTime()
    {
        return $this->first_blood_time;
    }

    public function setFirstBloodTime($first_blood_time)
    {
        $this->first_blood_time = $first_blood_time;
    }

    public function getGameMode()
    {
        return $this->game_mode;
    }

    public function setGameMode($game_mode)
    {
        $this->game_mode = $game_mode;
    }

    public function getHumanPlayers()
    {
        return $this->human_players;
    }

    public function setHumanPlayers($human_players)
    {
        $this->human_players = $human_players;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIsRadiantWin()
    {
        return $this->is_radiant_win;
    }

    public function setIsRadiantWin($is_radiant_win)
    {
        $this->is_radiant_win = $is_radiant_win;
    }

    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param DotaLeague $league
     */
    public function setLeague($league)
    {
        $league->addMatch($this);
        $this->league = $league;
    }

    public function getLobbyType()
    {
        return $this->lobby_type;
    }

    public function setLobbyType($lobby_type)
    {
        $this->lobby_type = $lobby_type;
    }

    public function getNegativeVotes()
    {
        return $this->negative_votes;
    }

    public function setNegativeVotes($negative_votes)
    {
        $this->negative_votes = $negative_votes;
    }

    public function getPositiveVotes()
    {
        return $this->positive_votes;
    }

    public function setPositiveVotes($positive_votes)
    {
        $this->positive_votes = $positive_votes;
    }

    public function getRadiantBarracksStatus()
    {
        return $this->radiant_barracks_status;
    }

    public function setRadiantBarracksStatus($radiant_barracks_status)
    {
        $this->radiant_barracks_status = $radiant_barracks_status;
    }

    /**
     * @return DotaTeam
     */
    public function getRadiantTeam()
    {
        return $this->radiant_team;
    }

    /**
     * @param DotaTeam $radiant_team
     */
    public function setRadiantTeam($radiant_team)
    {
        $this->radiant_team = $radiant_team;
    }

    public function getRadiantTeamComplete()
    {
        return $this->radiant_team_complete;
    }

    public function setRadiantTeamComplete($radiant_team_complete)
    {
        $this->radiant_team_complete = $radiant_team_complete;
    }

    public function getRadiantTowerStatus()
    {
        return $this->radiant_tower_status;
    }

    public function setRadiantTowerStatus($radiant_tower_status)
    {
        $this->radiant_tower_status = $radiant_tower_status;
    }

    public function getSeason()
    {
        return $this->season;
    }

    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    public function setStartTime($start_time)
    {
        $this->start_time = $start_time;
    }

}