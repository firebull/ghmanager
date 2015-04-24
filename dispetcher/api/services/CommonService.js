module.exports = {

    setLocale: function(req){
        if (req.query['lang'] !== undefined){
            req.setLocale(req.query['lang']);
        } else {
            req.setLocale('en');
        }
    },

    checkAuth: function(req, res){

        if (req.query['auth'] !== undefined
                && req.query['auth'] == sails.config.ghmanager.authKey){
            return true;
        } else {
            return false;
        }
    },

    execCommand: function(command, options, callback){
        var spawn  = require('child_process').spawn,
            run    = spawn(command, options);
        var err    = [],
            result = "";

        run.stdout.on('data', function (data) {
            result =  result + data.toString();
        });

        run.stderr.on('data', function (data) {
            err.push(data.toString());
            console.log(err);

        });

        run.on('close', function (code) {

          if (code !== 0) {
            err.push('ps process exited with code ' + code);

          }

          run.stdin.end();
          callback(err, result);

        });
    },
};
