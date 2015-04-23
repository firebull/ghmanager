module.exports = {


  migrate: 'safe',
  connection: 'ghmanagerMysqlServer',
  tableName: 'servers_users',
  autoCreatedAt: false,
  autoUpdatedAt: false,
  tables: ['servers', 'users'],

  junctionTable: true,

  attributes: {

    id: {
      primaryKey: true,
      autoIncrement: true,
      type: 'integer'
    },

    servers: {
      columnName: 'server_id',
      type: 'integer',
      foreignKey: true,
      references: 'servers',
      on: 'id',
      via: 'types',
      groupBy: 'servers'
    },

    users: {
      columnName: 'user_id',
      type: 'integer',
      foreignKey: true,
      references: 'users',
      on: 'id',
      via: 'servers',
      groupBy: 'users'
    }
  }

};

