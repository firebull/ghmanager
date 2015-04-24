module.exports = {


  migrate: 'safe',
  connection: 'ghmanagerMysqlServer',
  tableName: 'configs_game_templates',
  autoCreatedAt: false,
  autoUpdatedAt: false,
  tables: ['configs', 'game_templates'],

  junctionTable: true,

  attributes: {

    id: {
      primaryKey: true,
      autoIncrement: true,
      type: 'integer'
    },

    configs: {
      columnName: 'config_id',
      type: 'integer',
      foreignKey: true,
      references: 'configs',
      on: 'id',
      via: 'gametemplates',
      groupBy: 'configs'
    },

    gameTemplates: {
      columnName: 'game_template_id',
      type: 'integer',
      foreignKey: true,
      references: 'gametemplates',
      on: 'id',
      via: 'configs',
      groupBy: 'gameTemplates'
    }
  }

};

