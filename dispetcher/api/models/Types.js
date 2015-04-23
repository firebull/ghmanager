/**
* Types.js
*
* @description :: Model gets data from GameTemplates table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'types',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
        },
        longname: 'string',
        name: 'string',
        active: 'integer',

        servers: {
          collection: 'servers',
          columnName: 'server_id',
          via: 'type',
          through: 'typesservers',
          dominant: false
        }

    }

};

