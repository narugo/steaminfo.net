<?php

use SteamInfo\Models\Entities\DotaHero;
use SteamInfo\Models\Entities\DotaLeague;
use SteamInfo\Models\Entities\DotaMatch;
use SteamInfo\Models\Entities\DotaMatchPlayer;
use SteamInfo\Models\Entities\DotaTeam;

class Dota_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $match_id
     * @return DotaMatch
     */
    public function getMatchDetails($match_id)
    {
        $cache_key = 'dota_match_' . $match_id;
        $match = $this->memcached->get($cache_key);
        if ($match === FALSE) {
            $playersRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaMatch');

            $match = $playersRepository->find($match_id);
            $response = $this->steam->IDOTA2Match_570->GetMatchDetails($match_id);
            if (empty($match)) {
                $match = new DotaMatch();
                $match->setId($response->result->match_id);
                $this->entityManager->persist($match);

                $match->setIsRadiantWin($response->result->radiant_win);
                $match->setDuration($response->result->duration);

                $start_time = date_create();
                date_timestamp_set($start_time, $response->result->start_time);
                $match->setStartTime($start_time);

                $match->setMatchSeqNum($response->result->match_seq_num);
                $match->setSeason($response->result->season);
                $match->setRadiantTowerStatus($response->result->tower_status_radiant);
                $match->setDireTowerStatus($response->result->tower_status_dire);
                $match->setRadiantBarracksStatus($response->result->barracks_status_radiant);
                $match->setDireBarracksStatus($response->result->barracks_status_dire);
                $match->setCluster($response->result->cluster);
                $match->setFirstBloodTime($response->result->first_blood_time);
                $match->setLobbyType($response->result->lobby_type);
                $match->setHumanPlayers($response->result->human_players);
                $match->setPositiveVotes($response->result->positive_votes);
                $match->setNegativeVotes($response->result->negative_votes);
                $match->setGameMode($response->result->game_mode);

                if (!empty($response->result->leagueid)) {
                    $leagueRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaLeague');
                    $league = $leagueRepository->find($response->result->leagueid);
                    if (empty($league)) {
                        self::getLeagueListing();
                        $league = $leagueRepository->find($response->result->leagueid);
                    }
                    $match->setLeague($league);
                }

                $teamRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaTeam');
                if (!empty($response->result->radiant_team_id)) {
                    self::getTeamDetails($response->result->radiant_team_id);
                    $match->setRadiantTeam($teamRepository->find($response->result->radiant_team_id));
                }
                if (!empty($response->result->dire_team_id)) {
                    self::getTeamDetails($response->result->dire_team_id);
                    $match->setDireTeam($teamRepository->find($response->result->dire_team_id));
                }

                if (isset($response->result->radiant_team_complete)) $match->setRadiantTeamComplete($response->result->radiant_team_complete);
                if (isset($response->result->dire_team_complete)) $match->setDireTeamComplete($response->result->dire_team_complete);
            }

            $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');
            $heroRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaHero');
            $matchPlayerRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaMatchPlayer');
            foreach ($response->result->players as $profile) {
                $hero = $heroRepository->find($profile->hero_id);
                if (empty($hero)) {
                    self:: updateHeroes();
                    $hero = $heroRepository->find($profile->hero_id);
                }

                $current_player = $matchPlayerRepository->findOneBy(array(
                    'match' => $match,
                    'hero' => $hero
                ));

                if (empty($current_player)) {
                    $current_player = new DotaMatchPlayer();
                }

                if ($profile->account_id != 4294967295) {
                    require_once PATH_TO_MODELS . "users.php";
                    $users_model = new Users_Model();
                    $player_id = self::convertUserID($profile->account_id);
                    $users_model->getUsersFromSteam(array($player_id), false);
                    $player_profile = $userRepository->find(self::convertUserID($profile->account_id));
                    $current_player->setPlayer($player_profile);
                }

                $current_player->setMatch($match);
                $current_player->setHero($hero);
                $current_player->setSlot($profile->player_slot);

                $current_player->setLevel($profile->level);

                $current_player->setItem0($profile->item_0);
                $current_player->setItem1($profile->item_1);
                $current_player->setItem2($profile->item_2);
                $current_player->setItem3($profile->item_3);
                $current_player->setItem4($profile->item_4);
                $current_player->setItem5($profile->item_5);

                $current_player->setKills($profile->kills);
                $current_player->setDeaths($profile->deaths);
                $current_player->setAssists($profile->assists);

                $current_player->setLastHits($profile->last_hits);
                $current_player->setDenies($profile->denies);

                $current_player->setGold($profile->gold);
                $current_player->setGoldPerMin($profile->gold_per_min);
                $current_player->setXpPerMin($profile->xp_per_min);
                $current_player->setGoldSpent($profile->gold_spent);

                $current_player->setHeroDamage($profile->hero_damage);
                $current_player->setHeroHealing($profile->hero_healing);
                $current_player->setTowerDamage($profile->tower_damage);

                $current_player->setLeaverStatus($profile->leaver_status);

                $this->entityManager->persist($current_player);
            }
        }
        $this->entityManager->flush();

        // TODO: Fix
        //$this->memcached->add($cache_key, $match, 3000);

        return $match;
    }

    public function getLeagueListing()
    {
        $cache_key = 'dota_league_listing';
        $leagues = $this->memcached->get($cache_key);
        if ($leagues === FALSE) {
            $leagues = array();

            $response = $this->steam->IDOTA2Match_570->GetLeagueListing();

            $leagueRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaLeague');
            foreach ($response->result->leagues as $league) {
                $current_league = $leagueRepository->find($league->leagueid);
                if (empty($current_league)) {
                    $current_league = new DotaLeague();
                    $current_league->setId($league->leagueid);
                    $current_league->setName($league->name);
                    if (!empty($league->description)) $current_league->setDescription($league->description);
                    if (!empty($league->tournament_url)) $current_league->setTournamentUrl($league->tournament_url);
                }
                $this->entityManager->persist($current_league);
                array_push($leagues, $current_league);
            }
            $this->entityManager->flush();

            $this->memcached->set($cache_key, $leagues, 3600);
        }
        return $leagues;
    }

    public function getTeamDetails($team_id)
    {
        $cache_key = 'dota_team_' . $team_id;
        $team = $this->memcached->get($cache_key);
        if ($team === FALSE) {
            $teamRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaTeam');

            $response = $this->steam->IDOTA2Match_570->GetTeamInfoByTeamID($team_id, 1);
            $team = $teamRepository->find($team_id);
            if (empty($team)) {
                $team = new DotaTeam();
                $team->setId($response->result->teams[0]->team_id);
                $this->entityManager->persist($team);
            }
            $team->setName($response->result->teams[0]->name);
            $team->setTag($response->result->teams[0]->tag);

            $creation_time = date_create();
            date_timestamp_set($creation_time, $response->result->teams[0]->time_created);
            $team->setCreationTime($creation_time);

            $team->setRating($response->result->teams[0]->rating);
            if (!empty($response->result->teams[0]->logo)) $team->setLogo($response->result->teams[0]->logo);
            if (!empty($response->result->teams[0]->logo_sponsor)) $team->setLogoSponsor($response->result->teams[0]->logo_sponsor);
            if (!empty($response->result->teams[0]->country_code)) $team->setCountryCode($response->result->teams[0]->country_code);
            if (!empty($response->result->teams[0]->url)) $team->setUrl($response->result->teams[0]->url);
            $team->setGamesPlayedWithCurrentRoster($response->result->teams[0]->games_played_with_current_roster);

            require_once PATH_TO_MODELS . "users.php";
            $users_model = new Users_Model();
            $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');

            // Admin
            $admin_id = self::convertUserID($response->result->teams[0]->admin_account_id);
            $users_model->getUsersFromSteam(array($admin_id), false);
            $admin = $userRepository->find($admin_id);
            $team->setAdmin($admin);

            /*
             * Players
             */
            // TODO: Fix (player_N_account_id may be undefined and stuff)
            $ids = array();
            $player_0_id = self::convertUserID($response->result->teams[0]->player_0_account_id);
            if (!empty($player_0_id)) {
                array_push($ids, $player_0_id);
                $player_1_id = self::convertUserID($response->result->teams[0]->player_1_account_id);
                if (!empty($player_1_id)) {
                    array_push($ids, $player_1_id);
                    $player_2_id = self::convertUserID($response->result->teams[0]->player_2_account_id);
                    if (!empty($player_2_id)) {
                        array_push($ids, $player_2_id);
                        $player_3_id = self::convertUserID($response->result->teams[0]->player_3_account_id);
                        if (!empty($player_3_id)) {
                            array_push($ids, $player_3_id);
                            $player_4_id = self::convertUserID($response->result->teams[0]->player_4_account_id);
                            if (!empty($player_4_id)) {
                                array_push($ids, $player_4_id);
                            }
                        }
                    }
                }
            }

            $users_model->getUsersFromSteam($ids, false);

            if (!empty($ids[0])) {
                $player_0 = $userRepository->find($ids[0]);
                $team->setPlayer0($player_0);
                if (!empty($ids[1])) {
                    $player_1 = $userRepository->find($ids[1]);
                    $team->setPlayer1($player_1);
                    if (!empty($ids[2])) {
                        $player_2 = $userRepository->find($ids[2]);
                        $team->setPlayer2($player_2);
                        if (!empty($ids[3])) {
                            $player_3 = $userRepository->find($ids[3]);
                            $team->setPlayer3($player_3);
                            if (!empty($ids[4])) {
                                $player_4 = $userRepository->find($ids[4]);
                                $team->setPlayer4($player_4);
                            }
                        }
                    }
                }
            }

            $this->entityManager->flush();

            // TODO: Fix
            //$this->memcached->add($cache_key, $team, 3000);
        }
        return $team;
    }

    private function convertUserID($id)
    {
        $odd_id = $id % 2;
        $temp = floor($id / 2);
        $steam_id = '0:' . $odd_id . ':' . $temp;
        return $this->steam->tools->user->steamIdToCommunityId($steam_id);
    }

    public function updateHeroes()
    {
        $heroRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\DotaHero');

        $response = $this->steam->IEconDOTA2_570->GetHeroes();
        foreach ($response->result->heroes as $hero) {
            $current_hero = $heroRepository->find($hero->id);
            if (empty($current_hero)) {
                $current_hero = new DotaHero();
                $current_hero->setId($hero->id);
            }
            $current_hero->setName($hero->name);

            $this->entityManager->persist($current_hero);
        }
        $this->entityManager->flush();
    }

    public function getLiveLeagueMatches()
    {
        $cache_key = 'dota_live_league_matches';
        $matches = $this->memcached->get($cache_key);
        if ($matches === FALSE) {
            $response = $this->steam->IDOTA2Match_570->GetLiveLeagueGames();
            $matches = $response->games;
            $this->memcached->set($cache_key, $matches, 240);
        }
        return $matches;
    }

}