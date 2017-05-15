<style type="text/css">
  #GalaxyMapImage2{
    background-image: url('/recources/EmptyGalaxy.jpg');
    background-position: 30px 30px;
    background-size: 740px 740px;
    background-repeat: no-repeat;
    position: absolute;
    width: 770px;
    height: 770px;
    display: none;
  }
  #GalaxyMapImage{
    background-image: url('/recources/Galaxy740.png');
    background-position: 30px 30px;
    background-size: 740px 740px;
    background-repeat: no-repeat;
    position: absolute;
    width: 770px;
    height: 770px;
  }

</style>
<div id="Top"><span class="Title"><svg class="icon"><use xlink:href="#icon-map"></use></svg>FACTIONS MAP</span><span class="Time"></span></div>
<br/>
<h1>Factions Map</h1>
This map is loading alot of data, Please be patient.
<br/>
<div id="GalaxyMapImage"></div>
<div id="GalaxyMapImage2"></div>
<div id="FactionsMap" style="width: 800px; height: 800px;"><!-- Plotly chart will be drawn inside this DIV --></div>
<div id="HoverInfo"></div>
<script type="text/javascript">
    console.clear()
    var layout = {
      showlegend: false,
      paper_bgcolor:'rgba(0,0,0,0)',
      plot_bgcolor:'rgba(0,0,0,0)',
      yaxis: {
        gridcolor: 'rgba(255,255,255,0.2)',
        range: [-500,500],
      },
      xaxis: {
        gridcolor: 'rgba(255,255,255,0.2)',
        range: [-500,500],
      },
      margin: {
          l: 30,
          r: 30,
          t: 30,
          b: 30
      },
      hovermode: 'closest',
      images: [{
          "source": "/recources/Galaxy.png",
          "xref": "x",
          "yref": "y",
          "x": -500,
          "y": 500,
          "sizex": 1000,
          "sizey": 1000,
          "sizing": "stretch",
          "opacity": 1,
          "layer": "below"
      }]
    };
    var SectorData = <?php echo $Data['SectorData']; ?>;

    var FactionsMap = document.getElementById('FactionsMap');
    var Dragging = false;
    var ZoomFirst = false;
    var disableDragging = false;
    FactionsMap.addEventListener('contextmenu', event => event.preventDefault());
    $.get( "RefreshController.php", {function:"GetFactionsMap"},function(RecievedData) {
        Plotly.plot(FactionsMap, RecievedData , layout,{displaylogo: false,scrollZoom: false,
          modeBarButtonsToRemove: ['toImage','sendDataToCloud','zoomOut2d','zoomIn2d','autoScale2d','pan2d','hoverClosestCartesian'],
          modeBarButtonsToAdd:[{name:'Download Image (Does not include background)',icon:Plotly.Icons.camera,click:function(gd){
            Plotly.downloadImage(gd,{format:'png',width:740,height:740,filename:'RustyOP_FactionsMap'})
          }}]
        });

        FactionsMap.on('plotly_hover', function(data){
          var infotext = data.points.map(function(d){
            var FactionName = '';
            var Crafts = '';
            var Wrecks = '';
            var Stations = '';
            var Asteroids = '';
            var Influence = '';
            var FactionIndex = '';
            SectorData.forEach(function(Sector) {
                if(Sector['X'] == d.x){
                  if(Sector['Y'] == d.y){
                    if(("FactionName" in Sector)){
                      FactionName = 'FactionName: '+Sector['FactionName'];
                    }
                    if(Sector['Crafts'] != '0')
                      Crafts = 'Crafts: '+Sector['Crafts']+' ';
                    if(Sector['Wrecks'] != '0')
                      Wrecks = 'Wrecks: '+Sector['Wrecks']+' ';
                    if(Sector['Stations'] != '0')
                      Stations = 'Stations: '+Sector['Stations']+' ';
                    if(Sector['Asteroids'] != '0')
                      Asteroids = 'Asteroids: '+Sector['Asteroids']+' ';
                    if(Sector['Influence'] != '0')
                      Influence = 'Influence: '+Sector['Influence']+' ';
                    if(Sector['FactionIndex'] != '0')
                      FactionIndex = 'FactionIndex: '+Sector['FactionIndex']+' ';
                  }
                }
            });
            return ('x = '+d.x+', y = '+d.y+' '+Crafts+Wrecks+Stations+Asteroids+Influence+FactionIndex+FactionName);
          });
            $('#HoverInfo').html(infotext);
        })
         .on('plotly_unhover', function(data){
            $('#HoverInfo').html('Hover over dots to view more info.');
        });

        var XOffsetDown = 0;
        var YOffsetDown = 0;
        $('#FactionsMap').on('mousedown',function(event){
          Dragging = false;
          var offset = $(this).offset();
          XOffsetDown = (event.pageX - offset.left - 30);
          YOffsetDown = (event.pageY - offset.top - 30);
        });
        $('.modebar').on('mouseenter',function(event){
          Dragging = false;
          disableDragging = true;
        });
        $('.modebar').on('mouseleave',function(event){
          Dragging = false;
          disableDragging = false;
        });
        $('#FactionsMap').on('mousemove',function(event){
          if(!disableDragging){
            Dragging = true;
          }
        });
        var XOffsetUp = 0;
        var YOffsetUp = 0;
        $('#FactionsMap').on('mouseup',function(event){
          var wasDragging = Dragging;
          Dragging = false;
          mousedown = false;
          if(wasDragging){
            var offset = $(this).offset();
            XOffsetUp = 740 - (event.pageX - offset.left - 30);
            YOffsetUp = 740 - (event.pageY - offset.top - 30);
            var TempImg = $('#GalaxyMapImage').css('background-size').split(" ");
            var Image = {};
            Image['Width'] = parseInt(TempImg[0]);
            Image['Height'] = parseInt(TempImg[1]);
            TempImg = $('#GalaxyMapImage').css('background-position').split(" ");
            Image['Left'] = parseInt(TempImg[0]);
            Image['Top'] = parseInt(TempImg[1]);

            var Width = Image['Width'] + ((XOffsetUp + XOffsetDown) * 3.5)
            var Height = Image['Height'] + ((YOffsetUp + YOffsetDown) * 3.5)
            var Left = Image['Left'] - Math.abs(XOffsetDown *3.5)
            var Top = Image['Top'] - Math.abs(YOffsetDown*3.5)

            if(!ZoomFirst){
              ZoomFirst = true;
              var update = {'marker.size': '5'};
              Plotly.restyle(FactionsMap, update);
              console.log('ZoomFirst');
              $('#GalaxyMapImage').css({'background-size':Width+'px '+Height+'px'})
              $('#GalaxyMapImage').css({'background-position':Left+'px '+Top+'px'})
            }else{
              var update = {'marker.size': '10'};
              Plotly.restyle(FactionsMap, update);
              console.log('To many zoomms');
              $('#GalaxyMapImage').css({'background-size':'740px 740px','background-position':'30px 30px'})
              $('#GalaxyMapImage').hide();
              $('#GalaxyMapImage2').show();
            }
          }
          return;
        });
        FactionsMap.on('plotly_relayout',function(eventdata){
            if(eventdata['xaxis.range[0]']){
              ZoomFirst = false;
              console.log('Reset');
              var update = {'marker.size': '3'};
              Plotly.restyle(FactionsMap, update);
              console.log('To many zoomms');
              $('#GalaxyMapImage').css({'background-size':'740px 740px','background-position':'30px 30px'})
              $('#GalaxyMapImage').show();
              $('#GalaxyMapImage2').hide();
              return;
            }
        });
    },"json");
</script>
