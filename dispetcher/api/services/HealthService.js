module.exports = {

  memory: function (callback) {
    var exec = require('child_process').exec;
    var err = null;
    var stat = {};

    exec('cat /proc/meminfo | grep -E "^(MemTotal|MemFree|Active|SwapTotal|SwapFree)"',
        { maxBuffer: 64 * 1024 },
        function (error, stdout, stderr){
            if (error !== null){
                err = 'Memcheck: ' + stderr;
            } else {
                var str = stdout.toString();
                var lines = str.split('\n');

                lines.forEach(function(entry){
                    splitted = entry.split(':');
                    if (typeof splitted[1] == 'string'){
                        stat[splitted[0]] = splitted[1].trim();
                    }
                });
            }

            callback(err, stat);

        }).unref();

  },

};
