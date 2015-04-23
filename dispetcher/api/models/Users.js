/**
* Users.js
*
* @description :: Model gets data from Users table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'users',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
            },
        first_name: 'string',
        second_name: 'string',
        username: {
            type: 'string',
            unique: true
            },
        ftppassword: 'string',
        email: {
            type: 'email',
            unique: true
            },
        steam_id: 'string',
        guid: 'string',

        servers: {
          collection: 'servers',
          columnName: 'server_id',
          via: 'user',
          through: 'serversusers',
          dominant: false
        }

    }

};

