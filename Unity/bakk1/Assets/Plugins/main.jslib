
should_set_p = true;
should_set_time_slider = true;

mergeInto(LibraryManager.library, {

  Init: function() {
      init()
  },

  EndEvent: function() {
      end()
  },

  SetParameters: function(p_flat, p_hole, p_obstacle, obst_height, speed, force, gravity, block_length, seed, score, score_scaled, players, max_players) {
      should_set_p = false;
      sbecky.p_flat = p_flat
      sbecky.p_hole = p_hole
      sbecky.p_obstacle = p_obstacle
      sbecky.obst_height = obst_height
      sbecky.speed = speed
      sbecky.force = force
      sbecky.gravity = gravity
      sbecky.block_length = block_length
      sbecky.seed = seed
      sbecky.score = score
      sbecky.score_scaled = score_scaled
      sbecky.players = players
      sbecky.max_players = max_players
      should_set_p = true;
  },

  SetScore: function(score, score_scaled, standard_deviation, players, max_players) {
      sbecky.score = score
      sbecky.score_scaled_label = score_scaled+" ("+standard_deviation+")"
      sbecky.score_scaled = score_scaled
      sbecky.score_scaled_standard_deviation = standard_deviation
      if (players != -1) {
          sbecky.players = players;
      }
  },

  SetTime: function(time) {
      should_set_time_slider = false
      sbecky.time = time.toFixed(2);
      sbecky.sim_speed = sbecky.time
      should_set_time_slider = true
  },

  SetChart: function(data, size) {
      var d = []
      for(var i = 0; i < size; i++)
        d.push(HEAP32[(data >> 2) + i])
      updateChart(d);
  },

  SaveParameters: function() {
      var data = {
          "p_flat": sbecky.p_flat,
          "p_hole": sbecky.p_hole,
          "p_obstacle": sbecky.p_obstacle,
          "speed": sbecky.speed,
          "force": sbecky.force,
          "gravity": sbecky.gravity,
          "obst_height": sbecky.obst_height,
          "block_length": sbecky.block_length,
          "seed": sbecky.seed,
          "score": sbecky.score,
          "difficulty": sbecky.score_scaled,
          "difficulty_standard_deviation": sbecky.score_scaled_standard_deviation,
          "survivors": sbecky.players,
          "max_players": sbecky.max_players
      }
      var json = JSON.stringify(data, null, 2)
      var hist = document.getElementById("history")
      if (hist != undefined && hist != null) {

          var date = new Date().toLocaleString('de-DE')

          var canvas = document.getElementById('chart')
          // var id = Date.now();

          // TODO: fix canvas height and width
          var div = document.createElement("div");
          div.classList.add('item')
          if(window.id == undefined)
          window.id = 0;
          var _id = window.id++;
          div.innerHTML = " <div>\
          <span>"+date+"</span>\
          <a href='javascript:void(0)' onclick='set("+_id+")'>Play</a>\
          <a href='data:application/octet-stream;charset=utf-16le;base64,"+btoa(unescape(encodeURIComponent(json)))+"' download='file.txt'>Export</a>\
          <a href='javascript:void(0)' onclick='remove("+_id+", this)'>Delete</a>\
          <!--a href='javascript:void(0)' onclick='clip(this)'>Copy JSON</a-->\
          </div>\
          <div>\
          <pre>"+json+"</pre>\
          </div>"

          var image = new Image();
          image.src = canvas.toDataURL("image/png");
          image.classList.add("hist-chart")
          div.children[1].appendChild(image)

          hist.prepend(div);

          // save current params
          sbecky["hist_"+_id] = sbecky.p_flat+";"+sbecky.p_hole+";"+sbecky.p_obstacle+";"+sbecky.obst_height
          +";"+sbecky.speed+";"+sbecky.force+";"+sbecky.gravity+";"+sbecky.block_length
          +";"+sbecky.seed+";"+sbecky.players

          // save to server via ajax
          data["image"] = canvas.toDataURL("image/png");
          ajax("http://rokity.com/webgl/save.php", "post", data, function(resp){
              console.log(resp);
          }, function(err){
              console.log(err);
          })

      }
  }
});
