<?php

echo $this->Html->script(array (
         'graph/highstock.js' 
        ,'graph/themes/grid.js'   
    ));

?>

<script type="text/javascript">
Highcharts.setOptions({
  global: {
    useUTC: false
  }
});
</script>

<?php

foreach ($graphs as $graph) {


?>

<div id="graph_<?php echo $graph; ?>"></div>


<script type="text/javascript">

var chart;

  $.getJSON('/stats/rootServerStatJson/<?php echo $server["id"]; ?>/<?php echo $graph; ?>/1000/20', function(data) {
    data = eval(data);

    var d = [];
    var i = 0;
    var max = 0;
    var min = 0;
    var obj = 0;

    $.each(data, function(time, obj) {
      d[i] = new Array(2);

      obj = eval(obj);

      d[i] = [eval(time), obj];

      if (min == 0)
      {
        min = obj;
      }

      if (obj > max)
      {
        max = obj;
      }
      else
      if (obj < min)
      {
        min = obj;
      }

      i++;
    });
//alert(max);
  chart = new Highcharts.StockChart({
    chart: {
      renderTo: 'graph_<?php echo $graph; ?>',
      events: {
          load: function(chart) {
            this.setTitle(null, {
              text: 'Minimum: ' + min +' and Maximum: ' + max
            });
          }
        },
      zoomType: 'x'
    },
    rangeSelector: {
        selected: 2,
     
        buttons: [{
          type: 'hour',
          count: 12,
          text: '12h'
        }, {
          type: 'day',
          count: 1,
          text: '1d'
        }, {
          type: 'hour',
          count: 36,
          text: '36h'
        },{
          type: 'week',
          count: 1,
          text: '1w'
        }, {
          type: 'month',
          count: 1,
          text: '1m'
        }, {
          type: 'year',
          count: 1,
          text: '1y'
        }, {
          type: 'all',
          text: 'All'
        }]

     },


    title: {
        text: '<?php echo ucfirst($graph); ?> at <?php echo $server["name"]; ?>'
    },
    subtitle: {
        text: '...' // dummy text to reserve space for dynamic subtitle
      },
    series: [{
        name: '<?php echo ucfirst($graph); ?>',
        id: '<?php echo $graph."_".$server["id"]; ?>',
        data: d,
        type : 'areaspline'
        }],
    yAxis: {
      min: 0
    },

    // the event marker flags

    tooltip: {
        formatter: function() {
            var s = '<b>'+ Highcharts.dateFormat('%A, %b %e, %Y', this.x) +'</b>';

            $.each(this.points, function(i, point) {
                s += '<br/>'+ point.y +' <?php echo ucfirst($graph); ?>';
            });
        
            return s;
        }
    },

  });
});

</script>

<?php
}
?>