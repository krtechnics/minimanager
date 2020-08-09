<?php


//list of tables in realmd db will be saved on Global backup
$tables_backup_realmd = [
    'account',
    'account_banned',
    'ip_banned',
    'realmcharacters',
    'realmlist',
];


//list of tables in characters db will be saved on Global backup
$tables_backup_characters =
    [
        'account_data',
        'account_instance_times',
        'account_tutorial',
        'addons',
        'arena_team',
        'arena_team_member',
        'auctionbidders',
        'auctionhouse',
        'banned_addons',
        'battleground_deserters',
        'bugreport',
        'calendar_events',
        'calendar_invites',
        'channels',
        'character_account_data',
        'character_achievement',
        'character_achievement_progress',
        'character_action',
        'character_arena_stats',
        'character_aura',
        'character_banned',
        'character_battleground_data',
        'character_battleground_random',
        'character_declinedname',
        'character_equipmentsets',
        'character_fishingsteps',
        'character_gifts',
        'character_glyphs',
        'character_homebind',
        'character_instance',
        'character_inventory',
        'character_pet',
        'character_pet_declinedname',
        'character_queststatus',
        'character_queststatus_daily',
        'character_queststatus_monthly',
        'character_queststatus_rewarded',
        'character_queststatus_seasonal',
        'character_queststatus_weekly',
        'character_reputation',
        'character_skills',
        'character_social',
        'character_spell',
        'character_spell_cooldown',
        'character_stats',
        'character_talent',
        'characters',
        'corpse',
        'game_event_condition_save',
        'game_event_save',
        'gm_subsurvey',
        'gm_survey',
        'gm_ticket',
        'group_instance',
        'group_member',
        'groups',
        'guild',
        'guild_bank_eventlog',
        'guild_bank_item',
        'guild_bank_right',
        'guild_bank_tab',
        'guild_eventlog',
        'guild_member',
        'guild_member_withdraw',
        'guild_rank',
        'instance',
        'instance_reset',
        'item_instance',
        'item_loot_items',
        'item_loot_money',
        'item_refund_instance',
        'item_soulbound_trade_data',
        'lag_reports',
        'lfg_data',
        'mail',
        'mail_items',
        'pet_aura',
        'pet_spell',
        'pet_spell_cooldown',
        'petition',
        'petition_sign',
        'pool_quest_save',
        'pvpstats_battlegrounds',
        'pvpstats_players',
        'quest_tracker',
        'reserved_name',
        'respawn',
        'updates',
        'updates_include',
        'warden_action',
        'worldstates',
    ];

//list of tables in realmd db you need to delete data on user deletion
$tab_del_user_realmd = [
    [
        'realmcharacters',
        'acctid',
    ],
    [
        'account_banned',
        'id',
    ],
    [
        'account_access',
        'id',
    ],
    [
        'account',
        'id',
    ],
];

$tab_del_user_char = [
    [
        'account_data',
        'account',
    ],
];

//list of tables in realmd db you need to backup data on single user backup
$tab_backup_user_realmd = $tab_del_user_realmd;

// characters table needs to be separated from the tother tables cos of orphan clen up
$tab_del_user_characters_table = [
    [
        'characters',
        'guid',
    ],
];

$tab_del_user_other_tables = [
    [
        'arena_team_member',
        'guid',
    ],
    [
        'auctionhouse',
        'itemowner',
    ],
    [
        'character_account_data',
        'guid',
    ],
    [
        'character_achievement',
        'guid',
    ],
    [
        'character_achievement_progress',
        'guid',
    ],
    [
        'character_action',
        'guid',
    ],
    [
        'character_aura',
        'guid',
    ],
    [
        'character_battleground_data',
        'guid',
    ],
    [
        'character_declinedname',
        'guid',
    ],
    [
        'character_equipmentsets',
        'guid',
    ],
    [
        'character_gifts',
        'guid',
    ],
    [
        'character_glyphs',
        'guid',
    ],
    [
        'character_homebind',
        'guid',
    ],
    [
        'character_instance',
        'guid',
    ],
    [
        'character_inventory',
        'guid',
    ],
    [
        'character_pet',
        'owner',
    ],
    [
        'character_pet_declinedname',
        'owner',
    ],
    [
        'character_queststatus',
        'guid',
    ],
    [
        'character_queststatus_daily',
        'guid',
    ],
    [
        'character_reputation',
        'guid',
    ],
    [
        'character_skills',
        'guid',
    ],
    [
        'character_social',
        'guid',
    ],
    [
        'character_social',
        'friend',
    ],
    [
        'character_spell',
        'guid',
    ],
    [
        'character_spell_cooldown',
        'guid',
    ],
    [
        'character_stats',
        'guid',
    ],
    [
        'character_talent',
        'guid',
    ],
    [
        'gm_tickets',
        'playerGuid',
    ],
    [
        'corpse',
        'player',
    ],
    [
        'groups',
        'leaderGuid',
    ],
    [
        'group_member',
        'memberGuid',
    ],
    [
        'guild_bank_eventlog',
        'PlayerGuid',
    ],
    [
        'guild_eventlog',
        'PlayerGuid2',
    ],
    [
        'guild_eventlog',
        'PlayerGuid1',
    ],
    [
        'guild_member',
        'guid',
    ],
    [
        'item_instance',
        'owner_guid',
    ],
    [
        'item_refund_instance',
        'player_guid',
    ],
    [
        'mail',
        'receiver',
    ],
    [
        'mail_items',
        'receiver',
    ],
    [
        'petition',
        'ownerguid',
    ],
    [
        'petition_sign',
        'ownerguid',
    ],
    [
        'petition_sign',
        'playerguid',
    ],
    [
        'characters',
        'guid',
    ],
];

//list of tables in characters db you need to delete data from on user deletion
$tab_del_user_characters = $tab_del_user_characters_table + $tab_del_user_other_tables;

//list of tables in characters db you need to backup data from on single user backup
$tab_backup_user_characters = $tab_del_user_characters;

//list of extra pet tables in characters db you need to delete data from on orphan deletion
$tab_del_pet = [
    [
        'pet_aura',
        'guid',
    ],
    [
        'pet_spell',
        'guid',
    ],
    [
        'pet_spell_cooldown',
        'guid',
    ],
];

//list of tables in characters db while you delete guild
$tab_del_guild = [
    [
        'guild_bank_item',
        'guildid',
    ],
    [
        'guild_bank_eventlog',
        'guildid',
    ],
    [
        'guild_bank_right',
        'guildid',
    ],
    [
        'guild_bank_tab',
        'guildid',
    ],
    [
        'guild_eventlog',
        'guildid',
    ],
    [
        'guild_rank',
        'guildid',
    ],
    [
        'guild_member',
        'guildid',
    ],
    [
        'guild',
        'guildid',
    ],
];

//list of tables in characters db while you delete arena teams
$tab_del_arena = [
    [
        'arena_team',
        'arenaTeamId',
    ],
//    [
//        'arena_team_stats',
//        'arenateamid',
//    ],
    [
        'arena_team_member',
        'arenaTeamId',
    ],
];


?>
