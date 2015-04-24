/**
* Configs.js
*
* @description :: Model gets data from Configs table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'configs',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
        },
        name: 'string',
        path: 'string',

        gameTemplate: {
          collection: 'GameTemplates',
          columnName: 'game_template_id',
          via: 'configs',
          through: 'configsgametemplates',
          dominant: false
        },

    }

};

