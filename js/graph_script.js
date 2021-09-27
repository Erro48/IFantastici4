let teams_colors = ['orange', 'blue', 'green', 'purple'];

function buildLineGraph(ctx, labels, datasets) {
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: datasets
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
  });
}

function getTeamsScorePerEachGpPromise() {
  return new Promise(function(resolve, reject) {
    $.ajax({
        type: "POST",
        url: '../Fanta/data/teams_score.csv',
        datatype: 'text',
        success: function(response) {
          resolve(response)
        },
        error: function(err) {
          reject(err)
        }
    });
  }); 
}

function getTeamTotalPerEachGp(team_id, data) {
  let team_id_index = -1;
  for(let i = 1; i < data[0].length; i++) {
    if(parseInt(data[0][i]) == team_id) team_id_index = i;
  }

  let total_score = [];
  for(let i = 1; i < data.length; i++) {
    if(i == 1){
      total_score.push(castScore(data[i][team_id_index]));
    } else {
      total_score.push(total_score[i-2] + castScore(data[i][team_id_index]));
    }
  }

  return total_score;
}

function getDriverTotalPerEachGp(driver_id, data) {
  let total = [];
  let i = 1;

  while(data[i][driver_id] != "") {
    if(i == 1) total.push(castScore(data[i][driver_id]));
    else total.push(total[i-2] + castScore(data[i][driver_id]));

    i++;
  }

  return total;
}

function getDriverPartialPerEachGpGraph(driver_id, data) {
  let partials = [];
  let i = 1;

  while(data[i][driver_id] != "") {
    partials.push(castScore(data[i][driver_id]));

    i++;
  }

  return partials;
}

function getStableTotalPerEachGp(stable_id, data) {
  let total = [];
  let i = 1;
  stable_id += 20;
  
  while(data[i][stable_id] != "") {
    if(i == 1) total.push(castScore(data[i][stable_id]));
    else total.push(total[i-2] + castScore(data[i][stable_id]));

    i++;
  }

  return total;
}

function getRacedGps(transposed_data, last_gp_index) {
  transposed_data = transposed_data[0].slice(1).map(function(e) { return e.split("-")[0]; });
  return transposed_data.slice(0, last_gp_index);
}

// id_squadra, nome_squadra, nome_utente
function createTeamGraph(json_elem) {

  getTeamsScorePerEachGpPromise().then(
    function(data) {
      data = data.split('\n').map(function(e) { return e.split(',') });
      transposed_data = transposeArr(data);


      getTeamsInfoPromise().then(
        function(teams_info) {
          let datasets_arr = [];
          
          for(let i = 0; i < JSON.parse(teams_info).length; i++) {
            let team_name = JSON.parse(JSON.parse(teams_info)[i]).nome_squadra;
            
            datasets_arr.push({
              label: team_name,
              data: getTeamTotalPerEachGp((i+1), data),
              borderColor: [ teams_colors[i] ],
              borderWidth: 1
            })
          }

          var ctx = document.getElementById('teams_chart').getContext('2d');
          buildLineGraph(ctx, getRacedGps(transposed_data, getLastGpIndex(data)), datasets_arr);

        }
      );
    }
  )
}

function createDriversGraph(json_elem) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let transposed_score = transposeArr(score);

  let datasets = [];

  for(let i = 0; i < json_elem.length; i++) {
    datasets.push({
      label: json_elem[i].cognome_pilota,
      data: getDriverTotalPerEachGp((i+1), score),
      borderColor: livery[Math.floor(i/2)],
      borderWidth: 1,
      borderDash: i % 2 == 0 ? [0, 0] : [10, 10]
    })
  }

  var ctx = document.getElementById('driver-graph').getContext('2d');
  buildLineGraph(ctx, getRacedGps(transposed_score, getLastGpIndex(score)), datasets);

}

function createStablesGraph(json_elem) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let transposed_score = transposeArr(score);

  let datasets = [];

  for(let i = 0; i < json_elem.length; i++) {
    datasets.push({
      label: json_elem[i].nome_scuderia,
      data: getStableTotalPerEachGp((i+1), score),
      borderColor: livery[i],
      borderWidth: 1
    })
  }

  var ctx = document.getElementById('stable-graph').getContext('2d');
  buildLineGraph(ctx, getRacedGps(transposed_score, getLastGpIndex(score)), datasets);

}

function createPersonalTeamGraph(json_elem) {
  let score = scoreConverterToArray(getCookie('scores_data'), 31);
  let transposed_score = transposeArr(score);

  let datasets = [];

  for(let i = 0; i < json_elem.length; i++) {
    let driver_id = json_elem[i].id_pilota;
    datasets.push({
      label: json_elem[i].cognome_pilota,
      data: getDriverPartialPerEachGpGraph((driver_id), score),
      borderColor: livery[Math.floor((driver_id-1)/2)],
      borderWidth: 1,
      borderDash: i % 2 == 0 ? [0, 0] : [10, 10]
    })
  }

  var ctx = document.getElementById('teams-graph').getContext('2d');
  buildLineGraph(ctx, getRacedGps(transposed_score, getLastGpIndex(score)), datasets);

}