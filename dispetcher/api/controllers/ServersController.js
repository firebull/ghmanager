/**
 * GameServerController
 *
 * @description :: Server-side logic for managing gameservers
 * @help        :: See http://links.sailsjs.org/docs/controllers
 */

module.exports = {



  /**
   * `GameServerController.readLog()`
   */
    readLog: function (req, res) {
        res.set('Content-Type', 'application/json');
        CommonService.setLocale(req);

        var lang = req.getLocale();

        if (!CommonService.checkAuth(req, res)){
            res.status(403);
            return res.json({   error: sails.__({
                                        phrase: 'Ivalid Auth, forbidden access',
                                        locale: lang})
                            });
        } else if (req.body === undefined){
            res.status(500);
            return res.json({   error: sails.__({
                                        phrase: 'Ivalid request',
                                        locale: lang})
                            });
        } else {
            Servers.getServerPayed(req, res, function(err, data){
                var runLog = [];
                runLog.push('INFO: ' + sails.__({
                                        phrase: "Got server data",
                                        locale: lang
                                            })
                            );

                var userName = "client" + data.user[0].id;
                var homeDir  = "/home/" + userName;
                var serversPath = homeDir + "/servers";

                CommonService.execCommand('/images/scripts/global/dispetcher/read_log.py',
                                         ['--action', req.body.action,
                                          '--pattern', req.body.pattern,
                                          '--lines', req.body.lines,
                                          '--path', homeDir + '/' + req.body.logpath,
                                          '--name', req.body.logname,
                                          '--lang', lang
                                          ], function(err, answer){

                    if (answer !== null) {
                        answer = JSON.parse(answer);

                        if (answer.log !== undefined){
                            answer.log.forEach(function(entry){
                                runLog.push(entry);
                            });
                        }

                        if (answer.error !== undefined){
                            answer.error.forEach(function(entry){
                                err.push(entry);
                            });
                        }

                        return res.send(JSON.stringify({ error: err,
                                                         log: runLog,
                                                         data: answer.data
                                                        }, null, 3));

                    } else {
                        return res.send(JSON.stringify({ error: err,
                                                         log: runLog
                                                        }, null, 3));
                    }
                });
            });
        }
    },


  /**
   * `GameServerController.readWriteParam()`
   */
    readWriteParam: function (req, res) {
        res.set('Content-Type', 'application/json');
        CommonService.setLocale(req);

        var lang = req.getLocale();

        if (!CommonService.checkAuth(req, res)){
            res.status(403);
            return res.json({   error: sails.__({
                                        phrase: 'Ivalid Auth, forbidden access',
                                        locale: lang})
                            });
        } else if (req.body === undefined){
            res.status(500);
            return res.json({   error: sails.__({
                                        phrase: 'Ivalid request',
                                        locale: lang})
                            });
        } else {
            Servers.getServerPayed(req, res, function(err, data){
                var path = require('path');
                var runLog = [];
                runLog.push('INFO: ' + sails.__({
                                        phrase: "Got server data",
                                        locale: lang
                                            })
                            );

                var userName = "client" + data.user[0].id;
                var homeDir  = "/home/" + userName;
                var serversPath = homeDir + "/servers";

                // Remove back moves like ../..
                var configPath = path.join(homeDir, req.body.path.replace('(\.{1,2}/)', ''));

                // Remove  ../ in the file name
                var config = req.body.conf.replace('(/|\.{1,2}/)', '');

                CommonService.execCommand('/images/scripts/global/dispetcher/read_write_param.py',
                                         ['--param', req.body.p,
                                          '--value', req.body.val,
                                          '--desc', req.body.desc,
                                          '--config', config,
                                          '--path', configPath,
                                          '--action', req.body.action,
                                          '--delim', req.body.d,
                                          '--lang', lang
                                          ], function(err, answer){

                    if (answer !== null) {
                        answer = JSON.parse(answer);

                        if (answer.log !== undefined){
                            answer.log.forEach(function(entry){
                                runLog.push(entry);
                            });
                        }

                        if (answer.error !== undefined){
                            answer.error.forEach(function(entry){
                                err.push(entry);
                            });
                        }

                        return res.send(JSON.stringify({ error: err,
                                                         log: runLog,
                                                         data: answer.data
                                                        }, null, 3));

                    } else {
                        return res.send(JSON.stringify({ error: err,
                                                         log: runLog
                                                        }, null, 3));
                    }
                });
            });
        }
      }
    };

