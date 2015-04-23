/**
* GameTemplates.js
*
* @description :: Model gets data from GameTemplates table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'game_templates',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
        },
        longname: 'string',
        name: 'string',
        rootPath: 'datetime',
        addonsPath: 'integer',
        configPath: 'string',
        mapsPath: 'string',

        servers: {
          collection: 'servers',
          columnName: 'server_id',
          via: 'gameTemplate',
          through: 'gametemplatesservers',
          dominant: false
        }

    }

};

