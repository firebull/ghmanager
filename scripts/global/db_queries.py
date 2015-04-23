# coding: UTF-8


def defineUser(userCursor, serverID):
    userCursor.execute("""SELECT `User`.`id`, `User`.`first_name`, `User`.`second_name`,
                                    `User`.`username`, `User`.`ftppassword`, `User`.`email`,
                                    `User`.`steam_id`, `User`.`guid`,
                                    `ServersUser`.`server_id`, `ServersUser`.`user_id`
                                    FROM
                                    `users`
                                    AS
                                    `User`
                                    JOIN
                                    `servers_users`
                                    AS
                                    `ServersUser`
                                    ON
                                    (`ServersUser`.`server_id` = %s
                                    AND
                                    `ServersUser`.`user_id` = `User`.`id`) LIMIT 1""", [serverID])

    return userCursor.fetchone()


def defineRootServer(rootServerCursor, serverID):
    rootServerCursor.execute("""SELECT `RootServer`.`id`, `RootServer`.`name`,
                                        `RootServer`.`slotsMax`, `RootServer`.`slotsBought`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`
                                        FROM
                                        `root_servers` AS `RootServer`
                                        JOIN
                                        `servers_root_servers` AS `ServersRootServer`
                                        ON
                                        (`ServersRootServer`.`server_id` IN (%s)
                                        AND
                                        `ServersRootServer`.`root_server_id` = `RootServer`.`id`) LIMIT 1""", [serverID])

    return rootServerCursor.fetchone()


def getRootServerInitServers(rootServerCursor, rootServerID):
    rootServerCursor.execute("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`,
                                        `Server`.`privateType`, `Server`.`privateStatus`,
                                        `Server`.`emptySince`, `Server`.`status`,
                                        `Server`.`slots`, `Server`.`fpsmax`,
                                        `Server`.`rconPassword`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`
                                 FROM `servers`
                                 AS `Server`
                                 JOIN `servers_root_servers`
                                 AS `ServersRootServer`
                                 ON
                                     (`ServersRootServer`.`root_server_id` IN (%s)
                                         AND
                                      `ServersRootServer`.`server_id` = `Server`.`id`)
                                 WHERE
                                     payedTill > NOW()
                                 AND
                                     initialised != 1
                                 """, [rootServerID])
    return rootServerCursor


def getRootServerPayedServers(rootServerCursor, rootServerID):
    rootServerCursor.execute ("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`, 
                                        `Server`.`privateType`, `Server`.`privateStatus`, 
                                        `Server`.`emptySince`, `Server`.`status`,
                                        `Server`.`slots`, `Server`.`fpsmax`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id` 
                                 FROM `servers` 
                                 AS `Server` 
                                 JOIN `servers_root_servers` 
                                 AS `ServersRootServer` 
                                 ON 
                                     (`ServersRootServer`.`root_server_id` IN (%s) 
                                         AND 
                                      `ServersRootServer`.`server_id` = `Server`.`id`) 
                                 WHERE 
                                     payedTill > NOW() 
                                 AND 
                                     initialised = 1 
                                 """, [rootServerID])
    return rootServerCursor


def getRootServerPrivateServers(rootServerCursor, rootServerID):
    rootServerCursor.execute ("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`, 
                                        `Server`.`privateType`, `Server`.`privateStatus`, 
                                        `Server`.`emptySince`, `Server`.`status`, `Server`.`fpsmax`, 
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id` 
                                 FROM `servers` 
                                 AS `Server` 
                                 JOIN `servers_root_servers` 
                                 AS `ServersRootServer` 
                                 ON 
                                     (`ServersRootServer`.`root_server_id` IN (%s) 
                                         AND 
                                      `ServersRootServer`.`server_id` = `Server`.`id`) 
                                 WHERE 
                                     payedTill > NOW() 
                                 AND 
                                     initialised = 1 
                                 AND
                                     status = 'exec_success'
                                 AND 
                                     privateType > 0""", [rootServerID]) 
    return rootServerCursor


def getRootServerCod4v1Servers(rootServerCursor, rootServerID):
    rootServerCursor.execute("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`,
                                        `Server`.`privateType`, `Server`.`privateStatus`,
                                        `Server`.`emptySince`, `Server`.`status`, `Server`.`fpsmax`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`,
                                        `ServersGameTemplates`.`game_template_id`, `ServersGameTemplates`.`server_id`

                                 FROM `servers`
                                 AS `Server`

                                 JOIN `servers_root_servers`
                                 AS `ServersRootServer`
                                 ON
                                     (`ServersRootServer`.`root_server_id` IN (%s)
                                         AND
                                      `ServersRootServer`.`server_id` = `Server`.`id`)

                                 JOIN `game_templates_servers`
                                 AS `ServersGameTemplates`
                                 ON
                                     (`ServersGameTemplates`.`game_template_id` IN (35)
                                         AND
                                      `ServersGameTemplates`.`server_id` = `Server`.`id`)
                                 WHERE
                                     payedTill > NOW()
                                 AND
                                     initialised = 1
                                 AND
                                     status = 'exec_success'
                                 AND
                                     privateType < 2""", [rootServerID])
    return rootServerCursor


def getRootServerExecutedServers(rootServerCursor, rootServerID):
    rootServerCursor.execute("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`,
                                        `Server`.`privateType`, `Server`.`privateStatus`,
                                        `Server`.`emptySince`, `Server`.`debug`,
                                        `Server`.`status`, `Server`.`statusTime`, `Server`.`slots`,
                                        `Server`.`crashReboot`, `Server`.`crashCount`,
                                        `Server`.`crashTime`, `Server`.`fpsmax`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`
                                 FROM `servers`
                                 AS `Server`
                                 JOIN `servers_root_servers`
                                 AS `ServersRootServer`
                                 ON
                                     (`ServersRootServer`.`root_server_id` IN (%s)
                                         AND
                                      `ServersRootServer`.`server_id` = `Server`.`id`)
                                 WHERE
                                     payedTill > NOW()
                                 AND
                                     initialised = 1
                                 AND
                                     status = 'exec_success'
                                 """, [rootServerID])
    return rootServerCursor


# Запрос списка серверов для перезагрузки - т.е. запущенных больше 24 часов
def getRootServerExecutedServersToReboot(rootServerCursor, rootServerID):
    rootServerCursor.execute("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`,
                                        `Server`.`status`, `Server`.`hltvStatus`, `Server`.`statusTime`,
                                        `Server`.`slots`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`
                                 FROM `servers`
                                 AS `Server`
                                 JOIN `servers_root_servers`
                                 AS `ServersRootServer`
                                 ON
                                     (`ServersRootServer`.`root_server_id` IN (%s)
                                         AND
                                      `ServersRootServer`.`server_id` = `Server`.`id`)
                                 WHERE
                                     payedTill > NOW()
                                 AND
                                     initialised = 1
                                 AND
                                     status = 'exec_success'
                                 AND
                                     TIMESTAMPDIFF( HOUR , `Server`.`statusTime`, NOW() ) >= 24
                                 """, [rootServerID])
    return rootServerCursor


def getRootServerServersByType(rootServerCursor, rootServerID, typeID):
    rootServerCursor.execute("""SELECT `Server`.`id`, `Server`.`address`, `Server`.`port`,
                                        `Server`.`privateType`, `Server`.`privateStatus`,
                                        `Server`.`emptySince`, `Server`.`status`,
                                        `Server`.`slots`, `Server`.`fpsmax`,
                                        `ServersRootServer`.`root_server_id`, `ServersRootServer`.`server_id`,
                                        `ServersType`.`type_id`, `ServersType`.`server_id`
                                 FROM `servers`
                                 AS `Server`
                                 JOIN `servers_root_servers`
                                 AS `ServersRootServer`
                                 ON
                                     (`ServersRootServer`.`root_server_id` IN (%s)
                                         AND
                                      `ServersRootServer`.`server_id` = `Server`.`id`)
                                 JOIN `servers_types`
                                 AS `ServersType`
                                 ON
                                     (`ServersType`.`type_id` IN (%s)
                                         AND
                                      `ServersType`.`server_id` = `Server`.`id`)
                                 WHERE
                                     initialised = 1
                                 ORDER BY `id`
                                 """, [rootServerID, typeID])
    return rootServerCursor


def defineTemplate(templateCursor, serverID):
    templateCursor.execute("""SELECT `GameTemplate`.`id`, `GameTemplate`.`name`, `GameTemplate`.`longname`,
                                        `GameTemplate`.`rootPath`, `GameTemplate`.`configPath`,
                                        `GameTemplate`.`addonsPath`, `GameTemplate`.`mapsPath`,
                                        `GameTemplate`.`mapExt`, `GameTemplate`.`slots_max`,
                                        `GameTemplatesServer`.`game_template_id`,
                                        `GameTemplatesServer`.`server_id`
                                        FROM
                                        `game_templates`
                                        AS
                                        `GameTemplate`
                                        JOIN
                                        `game_templates_servers` AS `GameTemplatesServer`
                                        ON
                                        (`GameTemplatesServer`.`server_id` IN (%s)
                                        AND `GameTemplatesServer`.`game_template_id` = `GameTemplate`.`id`)
                                        LIMIT 1""", [serverID])

    return templateCursor.fetchone()


def defineType(typeCursor, templateID):
    typeCursor.execute("""SELECT `Type`.`id`, `Type`.`name`, `Type`.`longname`,
                                           `GameTemplatesType`.`game_template_id`, `GameTemplatesType`.`type_id`
                                           FROM
                                           `types`
                                           AS
                                           `Type`
                                           JOIN
                                           `game_templates_types`
                                           AS
                                           `GameTemplatesType`
                                           ON
                                           (`GameTemplatesType`.`game_template_id` IN (%s)
                                           AND
                                           `GameTemplatesType`.`type_id` = `Type`.`id`)
                                           LIMIT 1""", [templateID])

    return typeCursor.fetchone()


def cleanServerFromDb(db, cursor, serverID):
    cursor.execute("""DELETE FROM `servers_users` WHERE `server_id` = %s""", [serverID]) 
    cursor.execute("""DELETE FROM `game_templates_servers` WHERE `server_id` = %s""", [serverID])
    cursor.execute("""DELETE FROM `servers_root_servers` WHERE `server_id` = %s""", [serverID]) 
    cursor.execute("""DELETE FROM `servers_types` WHERE `server_id` = %s""", [serverID])
    cursor.execute("""DELETE FROM `servers` WHERE `id` = %s""", [serverID])  

    return True


def saveServerStatus(db, cursor, serverID, serverStatus, description, time):
    cursor.execute("""UPDATE `servers` 
                      SET 
                      `status` = %s, 
                      `statusDescription` = %s, 
                      `statusTime` = %s 
                      WHERE 
                      `id` = %s""", 
                      (serverStatus, description, time, serverID))

    return db.commit()


def saveServerTvStatus(db, cursor, serverID, serverStatus, description, time):
    cursor.execute("""UPDATE `servers` 
                      SET 
                      `hltvStatus` = %s, 
                      `hltvStatusDescription` = %s, 
                      `hltvStatusTime` = %s 
                      WHERE 
                      `id` = %s""", 
                      (serverStatus, description, time, serverID))

    return db.commit()


def setServerPrivateStatus(db, cursor, serverID, privateStatus):
    cursor.execute("""UPDATE  `servers` 
                      SET  
                      `privateStatus` =  %s 
                      WHERE  
                      `id` = %s;""",
                      (privateStatus, serverID))

    return db.commit()


def setServerEmptyTime(db, cursor, serverID, time='Null'):
    if time == 'Null':
        cursor.execute("""UPDATE  `servers`
                          SET
                          `emptySince` =  NULL
                          WHERE
                          `id` = %s;""",
                          (serverID))
    else:
        cursor.execute("""UPDATE  `servers`
                          SET
                          `emptySince` =  %s
                          WHERE
                          `id` = %s;""",
                          (time, serverID))

    return db.commit()


def setServerCrushStatus(db, cursor, serverID, count, time='Null'):
    if time == 'Null':
        cursor.execute("""UPDATE  `servers`
                          SET
                          `crashCount` = %s,
                          `crashTime` =  NULL
                          WHERE
                          `id` = %s;""",
                          (count, serverID))
    elif time == 'leave':
        cursor.execute("""UPDATE  `servers`
                          SET
                          `crashCount` = %s
                          WHERE
                          `id` = %s;""",
                          (count, serverID))
    else:
        cursor.execute("""UPDATE  `servers`
                          SET
                          `crashCount` = %s,
                          `crashTime` =  %s
                          WHERE
                          `id` = %s;""",
                          (count, time, serverID))

    return db.commit()


def definePlugin(id, cursor):
    cursor.execute("""SELECT `id`,`name`,`version` FROM `plugins` where `id`=%s""", id)

    return cursor.fetchone()


# Запись событий в журнал
def writeJournal(db, cursor, userId, text, status):
    import commands
    from datetime import datetime

    created = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    ip = commands.getoutput("ifconfig").split("\n")[1].split()[1][5:]  # IP локального сервера

    cursor.execute("""INSERT INTO  `teamserver`.`actions` (
                                        `id` ,
                                        `user_id` ,
                                        `action` ,
                                        `creator` ,
                                        `ip` ,
                                        `status` ,
                                        `created`
                                        )
                                        VALUES (
                                        NULL ,  %s,  %s,  'script', %s,  %s,  %s
                                        );""", (userId, text, ip, status, created))

    return db.commit()


# Проверка существования шаблона сервера
def checkGameTemplate(cursor, name):
    cursor.execute("""SELECT `id`,`name` FROM `game_templates` WHERE `name` = %s""", name)

    return cursor.fetchone()


# Сохранить версию сервера в базу
def saveGameTemplateVersion(db, cursor, id, version):
    cursor.execute("""UPDATE  `game_templates`
                          SET
                          `current_version` = %s
                          WHERE
                          `id` = %s;""",
                          (version, id))
    return db.commit()
